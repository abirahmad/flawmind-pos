<?php

namespace Modules\Sales\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Sales\Models\Transaction;
use Modules\Sales\Models\TransactionPayment;
use Modules\Sales\Models\Contact;
use Modules\Sales\Models\CashRegister;
use Modules\Sales\Models\CashRegisterTransaction;

class PaymentService extends BaseService
{
    /**
     * Get payments for a transaction
     */
    public function getPayments(int $transactionId): Collection
    {
        return TransactionPayment::where('transaction_id', $transactionId)
            ->with('createdByUser:id,first_name,last_name')
            ->orderBy('paid_on', 'desc')
            ->get();
    }

    /**
     * Get single payment
     */
    public function getPayment(int $paymentId): ?TransactionPayment
    {
        return TransactionPayment::with(['transaction', 'createdByUser:id,first_name,last_name'])
            ->find($paymentId);
    }

    /**
     * Process multiple payments for a transaction
     */
    public function processPayments(Transaction $transaction, array $payments, int $userId): void
    {
        foreach ($payments as $payment) {
            $this->addPayment($transaction, array_merge($payment, ['created_by' => $userId]));
        }
    }

    /**
     * Add a single payment
     */
    public function addPayment(Transaction $transaction, array $data): TransactionPayment
    {
        $payment = TransactionPayment::create([
            'transaction_id' => $transaction->id,
            'business_id' => $transaction->business_id,
            'amount' => $data['amount'],
            'method' => $data['method'] ?? TransactionPayment::METHOD_CASH,
            'paid_on' => $data['paid_on'] ?? now(),
            'created_by' => $data['created_by'],
            'is_return' => false,
            'is_advance' => $data['is_advance'] ?? false,
            'payment_for' => $data['payment_for'] ?? null,
            'note' => $data['note'] ?? null,
            'payment_ref_no' => $data['payment_ref_no'] ?? $this->generateReferenceNumber('PAY-'),
            'account_id' => $data['account_id'] ?? null,

            // Card payment fields
            'card_transaction_number' => $data['card_transaction_number'] ?? null,
            'card_number' => $data['card_number'] ?? null,
            'card_type' => $data['card_type'] ?? null,
            'card_holder_name' => $data['card_holder_name'] ?? null,
            'card_month' => $data['card_month'] ?? null,
            'card_year' => $data['card_year'] ?? null,

            // Cheque/Bank fields
            'cheque_number' => $data['cheque_number'] ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? null,
            'transaction_no' => $data['transaction_no'] ?? null,

            // Other fields
            'document' => $data['document'] ?? null,
            'paid_through_link' => false,
            'gateway' => $data['gateway'] ?? null,
        ]);

        // Add to cash register if applicable
        if (!empty($data['cash_register_id'])) {
            $this->addToCashRegister($data['cash_register_id'], $payment, 'sell');
        }

        $this->logActivity('add_payment', $payment);

        return $payment;
    }

    /**
     * Update a payment
     */
    public function updatePayment(TransactionPayment $payment, array $data): TransactionPayment
    {
        $payment->update([
            'amount' => $data['amount'] ?? $payment->amount,
            'method' => $data['method'] ?? $payment->method,
            'paid_on' => $data['paid_on'] ?? $payment->paid_on,
            'note' => $data['note'] ?? $payment->note,
            'account_id' => $data['account_id'] ?? $payment->account_id,

            // Card payment fields
            'card_transaction_number' => $data['card_transaction_number'] ?? $payment->card_transaction_number,
            'card_number' => $data['card_number'] ?? $payment->card_number,
            'card_type' => $data['card_type'] ?? $payment->card_type,
            'card_holder_name' => $data['card_holder_name'] ?? $payment->card_holder_name,

            // Cheque/Bank fields
            'cheque_number' => $data['cheque_number'] ?? $payment->cheque_number,
            'bank_account_number' => $data['bank_account_number'] ?? $payment->bank_account_number,
            'transaction_no' => $data['transaction_no'] ?? $payment->transaction_no,
        ]);

        $this->logActivity('update_payment', $payment);

        return $payment->fresh();
    }

    /**
     * Delete a payment
     */
    public function deletePayment(TransactionPayment $payment): bool
    {
        $this->logActivity('delete_payment', $payment);

        return $payment->delete();
    }

    /**
     * Process refund for a transaction
     */
    public function processRefund(Transaction $transaction, float $amount, string $method, int $userId): TransactionPayment
    {
        $payment = TransactionPayment::create([
            'transaction_id' => $transaction->id,
            'business_id' => $transaction->business_id,
            'amount' => $amount,
            'method' => $method,
            'paid_on' => now(),
            'created_by' => $userId,
            'is_return' => true,
            'is_advance' => false,
            'payment_ref_no' => $this->generateReferenceNumber('REF-'),
            'paid_through_link' => false,
        ]);

        $this->logActivity('process_refund', $payment);

        return $payment;
    }

    /**
     * Get contact's total due
     */
    public function getContactDue(int $contactId): float
    {
        $contact = Contact::find($contactId);

        if (!$contact) {
            return 0;
        }

        return $contact->balance ?? 0;
    }

    /**
     * Get contact's advance balance
     */
    public function getContactAdvance(int $contactId, int $businessId): float
    {
        return TransactionPayment::where('payment_for', $contactId)
            ->where('business_id', $businessId)
            ->where('is_advance', true)
            ->whereNull('transaction_id')
            ->sum('amount');
    }

    /**
     * Pay contact due
     */
    public function payContactDue(int $contactId, array $data): array
    {
        return $this->executeInTransaction(function () use ($contactId, $data) {
            $contact = Contact::findOrFail($contactId);

            // Get due transactions
            $dueTransactions = Transaction::where('contact_id', $contactId)
                ->where('business_id', $data['business_id'])
                ->whereIn('payment_status', [Transaction::PAYMENT_DUE, Transaction::PAYMENT_PARTIAL])
                ->orderBy('transaction_date', 'asc')
                ->get();

            $remainingAmount = $data['amount'];
            $paymentsCreated = [];

            foreach ($dueTransactions as $transaction) {
                if ($remainingAmount <= 0) {
                    break;
                }

                $dueAmount = $transaction->balance_due;
                $paymentAmount = min($remainingAmount, $dueAmount);

                if ($paymentAmount > 0) {
                    $payment = $this->addPayment($transaction, [
                        'amount' => $paymentAmount,
                        'method' => $data['method'] ?? TransactionPayment::METHOD_CASH,
                        'paid_on' => $data['paid_on'] ?? now(),
                        'created_by' => $data['created_by'],
                        'note' => $data['note'] ?? 'Bulk payment',
                    ]);

                    $paymentsCreated[] = $payment;
                    $remainingAmount -= $paymentAmount;

                    // Update transaction payment status
                    $this->updateTransactionPaymentStatus($transaction);
                }
            }

            // If there's remaining amount, create advance payment
            if ($remainingAmount > 0) {
                $advancePayment = TransactionPayment::create([
                    'business_id' => $data['business_id'],
                    'amount' => $remainingAmount,
                    'method' => $data['method'] ?? TransactionPayment::METHOD_CASH,
                    'paid_on' => $data['paid_on'] ?? now(),
                    'created_by' => $data['created_by'],
                    'is_advance' => true,
                    'payment_for' => $contactId,
                    'note' => 'Advance payment',
                    'payment_ref_no' => $this->generateReferenceNumber('ADV-'),
                    'is_return' => false,
                    'paid_through_link' => false,
                ]);

                $paymentsCreated[] = $advancePayment;
            }

            // Update contact balance
            $this->updateContactBalance($contact);

            return [
                'payments' => $paymentsCreated,
                'total_paid' => $data['amount'],
                'applied_to_invoices' => $data['amount'] - $remainingAmount,
                'advance_created' => $remainingAmount,
            ];
        });
    }

    /**
     * Update transaction payment status
     */
    protected function updateTransactionPaymentStatus(Transaction $transaction): void
    {
        $totalPaid = $transaction->paymentLines()
            ->where('is_return', false)
            ->sum('amount');

        $totalRefunded = $transaction->paymentLines()
            ->where('is_return', true)
            ->sum('amount');

        $netPaid = $totalPaid - $totalRefunded;

        if ($netPaid >= $transaction->final_total) {
            $status = Transaction::PAYMENT_PAID;
        } elseif ($netPaid > 0) {
            $status = Transaction::PAYMENT_PARTIAL;
        } else {
            $status = Transaction::PAYMENT_DUE;
        }

        $transaction->update(['payment_status' => $status]);
    }

    /**
     * Update contact balance
     */
    protected function updateContactBalance(Contact $contact): void
    {
        $totalDue = Transaction::where('contact_id', $contact->id)
            ->whereIn('type', [Transaction::TYPE_SELL, Transaction::TYPE_SELL_RETURN])
            ->where('status', Transaction::STATUS_FINAL)
            ->get()
            ->sum(function ($transaction) {
                if ($transaction->type === Transaction::TYPE_SELL) {
                    return $transaction->balance_due;
                }
                return -$transaction->balance_due;
            });

        $contact->update(['balance' => $totalDue]);
    }

    /**
     * Add payment to cash register
     */
    protected function addToCashRegister(int $registerId, TransactionPayment $payment, string $transactionType): void
    {
        CashRegisterTransaction::create([
            'cash_register_id' => $registerId,
            'amount' => $payment->amount,
            'pay_method' => $payment->method,
            'type' => $payment->is_return ? 'debit' : 'credit',
            'transaction_type' => $transactionType,
            'transaction_id' => $payment->transaction_id,
        ]);
    }

    /**
     * Get payment methods
     */
    public static function getPaymentMethods(): array
    {
        return TransactionPayment::paymentMethods();
    }
}
