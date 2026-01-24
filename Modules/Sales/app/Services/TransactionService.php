<?php

namespace Modules\Sales\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Sales\Models\Transaction;
use Modules\Sales\Models\TransactionSellLine;
use Modules\Sales\Models\Contact;
use Modules\Sales\Traits\TransactionCalculations;

class TransactionService extends BaseService
{
    use TransactionCalculations;

    protected PaymentService $paymentService;
    protected StockService $stockService;
    protected InvoiceService $invoiceService;

    public function __construct(
        PaymentService $paymentService,
        StockService $stockService,
        InvoiceService $invoiceService
    ) {
        $this->paymentService = $paymentService;
        $this->stockService = $stockService;
        $this->invoiceService = $invoiceService;
    }

    /**
     * Get paginated list of transactions
     */
    public function getTransactions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Transaction::query()
            ->with(['contact:id,name,mobile', 'createdBy:id,first_name,last_name'])
            ->sells();

        // Apply filters
        if (!empty($filters['business_id'])) {
            $query->forBusiness($filters['business_id']);
        }

        if (!empty($filters['location_id'])) {
            $query->forLocation($filters['location_id']);
        }

        if (!empty($filters['contact_id'])) {
            $query->forContact($filters['contact_id']);
        }

        if (!empty($filters['payment_status'])) {
            $query->paymentStatus($filters['payment_status']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('ref_no', 'like', "%{$search}%")
                  ->orWhereHas('contact', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'transaction_date';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get single transaction with details
     */
    public function getTransaction(int $id, ?int $businessId = null): ?Transaction
    {
        $query = Transaction::with([
            'sellLines.product:id,name,sku,type',
            'sellLines.variation:id,name,sub_sku,sell_price_inc_tax',
            'paymentLines',
            'contact',
            'createdBy:id,first_name,last_name',
            'returnTransaction',
        ]);

        if ($businessId) {
            $query->forBusiness($businessId);
        }

        return $query->find($id);
    }

    /**
     * Create a new sale transaction
     */
    public function createSell(array $data): Transaction
    {
        return $this->executeInTransaction(function () use ($data) {
            // Validate customer credit limit
            if (!empty($data['contact_id'])) {
                $this->validateCreditLimit($data['contact_id'], $data['final_total'] ?? 0);
            }

            // Generate invoice number
            $invoiceNo = $this->invoiceService->generateInvoiceNumber(
                $data['business_id'],
                $data['location_id'] ?? null,
                $data['invoice_scheme_id'] ?? null
            );

            // Create transaction
            $transaction = Transaction::create([
                'business_id' => $data['business_id'],
                'location_id' => $data['location_id'] ?? null,
                'type' => Transaction::TYPE_SELL,
                'status' => $data['status'] ?? Transaction::STATUS_FINAL,
                'sub_status' => $data['sub_status'] ?? null,
                'is_quotation' => $data['is_quotation'] ?? false,
                'contact_id' => $data['contact_id'],
                'customer_group_id' => $data['customer_group_id'] ?? null,
                'invoice_no' => $invoiceNo,
                'ref_no' => $data['ref_no'] ?? null,
                'source' => $data['source'] ?? 'api',
                'transaction_date' => $data['transaction_date'] ?? now(),
                'total_before_tax' => $data['total_before_tax'] ?? 0,
                'tax_id' => $data['tax_id'] ?? null,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_type' => $data['discount_type'] ?? null,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'final_total' => $data['final_total'] ?? 0,
                'shipping_details' => $data['shipping_details'] ?? null,
                'shipping_address' => $data['shipping_address'] ?? null,
                'shipping_status' => $data['shipping_status'] ?? null,
                'shipping_charges' => $data['shipping_charges'] ?? 0,
                'additional_notes' => $data['additional_notes'] ?? null,
                'staff_note' => $data['staff_note'] ?? null,
                'round_off_amount' => $data['round_off_amount'] ?? 0,
                'payment_status' => Transaction::PAYMENT_DUE,
                'is_direct_sale' => $data['is_direct_sale'] ?? false,
                'is_suspend' => $data['is_suspend'] ?? false,
                'is_export' => $data['is_export'] ?? false,
                'is_created_from_api' => true,
                'exchange_rate' => $data['exchange_rate'] ?? 1,
                'selling_price_group_id' => $data['selling_price_group_id'] ?? null,
                'pay_term_number' => $data['pay_term_number'] ?? null,
                'pay_term_type' => $data['pay_term_type'] ?? null,
                'commission_agent' => $data['commission_agent'] ?? null,
                'created_by' => $data['created_by'],
                'custom_field_1' => $data['custom_field_1'] ?? null,
                'custom_field_2' => $data['custom_field_2'] ?? null,
                'custom_field_3' => $data['custom_field_3'] ?? null,
                'custom_field_4' => $data['custom_field_4'] ?? null,
            ]);

            // Create sell lines
            if (!empty($data['sell_lines'])) {
                $this->createSellLines($transaction, $data['sell_lines']);
            }

            // Update stock if final
            if ($transaction->status === Transaction::STATUS_FINAL) {
                $this->stockService->decreaseStockForSell($transaction);
            }

            // Process payments
            if (!empty($data['payments'])) {
                $this->paymentService->processPayments($transaction, $data['payments'], $data['created_by']);
            }

            // Update payment status
            $this->updatePaymentStatus($transaction);

            // Update customer balance
            if ($transaction->status === Transaction::STATUS_FINAL && $transaction->contact_id) {
                $this->updateContactBalance($transaction->contact_id);
            }

            $this->logActivity('create_sell', $transaction);

            return $transaction->fresh(['sellLines', 'paymentLines', 'contact']);
        });
    }

    /**
     * Update a sale transaction
     */
    public function updateSell(Transaction $transaction, array $data): Transaction
    {
        return $this->executeInTransaction(function () use ($transaction, $data) {
            $oldStatus = $transaction->status;

            // If changing from draft/quotation to final, validate credit limit
            if ($oldStatus !== Transaction::STATUS_FINAL &&
                ($data['status'] ?? $oldStatus) === Transaction::STATUS_FINAL &&
                $transaction->contact_id) {
                $this->validateCreditLimit($transaction->contact_id, $data['final_total'] ?? $transaction->final_total);
            }

            // Update transaction
            $transaction->update([
                'contact_id' => $data['contact_id'] ?? $transaction->contact_id,
                'status' => $data['status'] ?? $transaction->status,
                'sub_status' => $data['sub_status'] ?? $transaction->sub_status,
                'ref_no' => $data['ref_no'] ?? $transaction->ref_no,
                'transaction_date' => $data['transaction_date'] ?? $transaction->transaction_date,
                'total_before_tax' => $data['total_before_tax'] ?? $transaction->total_before_tax,
                'tax_id' => $data['tax_id'] ?? $transaction->tax_id,
                'tax_amount' => $data['tax_amount'] ?? $transaction->tax_amount,
                'discount_type' => $data['discount_type'] ?? $transaction->discount_type,
                'discount_amount' => $data['discount_amount'] ?? $transaction->discount_amount,
                'final_total' => $data['final_total'] ?? $transaction->final_total,
                'shipping_details' => $data['shipping_details'] ?? $transaction->shipping_details,
                'shipping_address' => $data['shipping_address'] ?? $transaction->shipping_address,
                'shipping_status' => $data['shipping_status'] ?? $transaction->shipping_status,
                'shipping_charges' => $data['shipping_charges'] ?? $transaction->shipping_charges,
                'additional_notes' => $data['additional_notes'] ?? $transaction->additional_notes,
                'staff_note' => $data['staff_note'] ?? $transaction->staff_note,
                'round_off_amount' => $data['round_off_amount'] ?? $transaction->round_off_amount,
                'custom_field_1' => $data['custom_field_1'] ?? $transaction->custom_field_1,
                'custom_field_2' => $data['custom_field_2'] ?? $transaction->custom_field_2,
                'custom_field_3' => $data['custom_field_3'] ?? $transaction->custom_field_3,
                'custom_field_4' => $data['custom_field_4'] ?? $transaction->custom_field_4,
            ]);

            // Update sell lines if provided
            if (isset($data['sell_lines'])) {
                // Reverse stock if was final
                if ($oldStatus === Transaction::STATUS_FINAL) {
                    $this->stockService->reverseStockForSell($transaction);
                }

                // Delete old lines and create new ones
                $transaction->sellLines()->delete();
                $this->createSellLines($transaction, $data['sell_lines']);

                // Update stock if final
                if ($transaction->status === Transaction::STATUS_FINAL) {
                    $this->stockService->decreaseStockForSell($transaction);
                }
            }

            // Update payment status
            $this->updatePaymentStatus($transaction);

            // Update customer balance
            if ($transaction->contact_id) {
                $this->updateContactBalance($transaction->contact_id);
            }

            $this->logActivity('update_sell', $transaction);

            return $transaction->fresh(['sellLines', 'paymentLines', 'contact']);
        });
    }

    /**
     * Delete a sale transaction
     */
    public function deleteSell(Transaction $transaction): bool
    {
        return $this->executeInTransaction(function () use ($transaction) {
            // Reverse stock if final
            if ($transaction->status === Transaction::STATUS_FINAL) {
                $this->stockService->reverseStockForSell($transaction);
            }

            // Delete payments
            $transaction->paymentLines()->delete();

            // Delete sell lines
            $transaction->sellLines()->delete();

            // Delete transaction
            $transaction->delete();

            // Update customer balance
            if ($transaction->contact_id) {
                $this->updateContactBalance($transaction->contact_id);
            }

            $this->logActivity('delete_sell', $transaction);

            return true;
        });
    }

    /**
     * Create sell return
     */
    public function createSellReturn(Transaction $originalSell, array $data): Transaction
    {
        return $this->executeInTransaction(function () use ($originalSell, $data) {
            // Validate original sell
            if ($originalSell->type !== Transaction::TYPE_SELL ||
                $originalSell->status !== Transaction::STATUS_FINAL) {
                throw new \InvalidArgumentException('Can only return finalized sales');
            }

            // Check if return already exists
            if ($originalSell->returnTransaction) {
                throw new \InvalidArgumentException('Return already exists for this sale');
            }

            // Create return transaction
            $return = Transaction::create([
                'business_id' => $originalSell->business_id,
                'location_id' => $originalSell->location_id,
                'type' => Transaction::TYPE_SELL_RETURN,
                'status' => Transaction::STATUS_FINAL,
                'contact_id' => $originalSell->contact_id,
                'return_parent_id' => $originalSell->id,
                'invoice_no' => 'RET-' . $originalSell->invoice_no,
                'transaction_date' => $data['transaction_date'] ?? now(),
                'total_before_tax' => $data['total_before_tax'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'final_total' => $data['final_total'] ?? 0,
                'payment_status' => Transaction::PAYMENT_DUE,
                'additional_notes' => $data['additional_notes'] ?? null,
                'staff_note' => $data['staff_note'] ?? null,
                'created_by' => $data['created_by'],
                'is_created_from_api' => true,
            ]);

            // Create return lines
            if (!empty($data['return_lines'])) {
                $this->createReturnLines($return, $originalSell, $data['return_lines']);
            }

            // Increase stock (return items to inventory)
            $this->stockService->increaseStockForReturn($return);

            // Process refund payment
            if (!empty($data['refund_amount'])) {
                $this->paymentService->processRefund($return, $data['refund_amount'], $data['refund_method'] ?? 'cash', $data['created_by']);
            }

            // Update customer balance
            if ($return->contact_id) {
                $this->updateContactBalance($return->contact_id);
            }

            $this->logActivity('create_sell_return', $return);

            return $return->fresh(['sellLines', 'paymentLines']);
        });
    }

    /**
     * Create sell lines
     */
    protected function createSellLines(Transaction $transaction, array $lines): void
    {
        foreach ($lines as $line) {
            TransactionSellLine::create([
                'transaction_id' => $transaction->id,
                'product_id' => $line['product_id'],
                'variation_id' => $line['variation_id'],
                'quantity' => $line['quantity'],
                'secondary_unit_quantity' => $line['secondary_unit_quantity'] ?? 0,
                'unit_price_before_discount' => $line['unit_price_before_discount'] ?? $line['unit_price'],
                'unit_price' => $line['unit_price'],
                'unit_price_inc_tax' => $line['unit_price_inc_tax'] ?? $line['unit_price'],
                'line_discount_type' => $line['line_discount_type'] ?? null,
                'line_discount_amount' => $line['line_discount_amount'] ?? 0,
                'item_tax' => $line['item_tax'] ?? 0,
                'tax_id' => $line['tax_id'] ?? null,
                'sell_line_note' => $line['sell_line_note'] ?? null,
                'sub_unit_id' => $line['sub_unit_id'] ?? null,
                'lot_no_line_id' => $line['lot_no_line_id'] ?? null,
                'discount_id' => $line['discount_id'] ?? null,
                'quantity_returned' => 0,
                'mfg_waste_percent' => 0,
                'so_quantity_invoiced' => 0,
                'children_type' => '',
            ]);
        }
    }

    /**
     * Create return lines
     */
    protected function createReturnLines(Transaction $return, Transaction $originalSell, array $lines): void
    {
        foreach ($lines as $line) {
            // Find original sell line
            $originalLine = $originalSell->sellLines()->find($line['sell_line_id']);

            if (!$originalLine) {
                continue;
            }

            // Validate return quantity
            $availableQty = $originalLine->quantity - $originalLine->quantity_returned;
            $returnQty = min($line['quantity'], $availableQty);

            if ($returnQty <= 0) {
                continue;
            }

            // Create return line
            TransactionSellLine::create([
                'transaction_id' => $return->id,
                'product_id' => $originalLine->product_id,
                'variation_id' => $originalLine->variation_id,
                'quantity' => $returnQty,
                'unit_price' => $originalLine->unit_price,
                'unit_price_inc_tax' => $originalLine->unit_price_inc_tax,
                'item_tax' => $originalLine->item_tax,
                'tax_id' => $originalLine->tax_id,
                'sell_line_note' => $line['return_note'] ?? null,
                'parent_sell_line_id' => $originalLine->id,
                'quantity_returned' => 0,
                'unit_price_before_discount' => $originalLine->unit_price_before_discount,
                'line_discount_amount' => 0,
                'secondary_unit_quantity' => 0,
                'mfg_waste_percent' => 0,
                'so_quantity_invoiced' => 0,
                'children_type' => '',
            ]);

            // Update original line returned quantity
            $originalLine->update([
                'quantity_returned' => $originalLine->quantity_returned + $returnQty,
            ]);
        }
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Transaction $transaction): void
    {
        $totalPaid = $transaction->paymentLines()
            ->where('is_return', false)
            ->sum('amount');

        $totalRefunded = $transaction->paymentLines()
            ->where('is_return', true)
            ->sum('amount');

        $netPaid = $totalPaid - $totalRefunded;
        $status = $this->calculatePaymentStatus($transaction->final_total, $netPaid);

        $transaction->update(['payment_status' => $status]);
    }

    /**
     * Validate customer credit limit
     */
    protected function validateCreditLimit(int $contactId, float $amount): void
    {
        $contact = Contact::find($contactId);

        if (!$contact) {
            return;
        }

        if ($contact->isCreditLimitExceeded($amount)) {
            throw new \InvalidArgumentException('Customer credit limit exceeded');
        }
    }

    /**
     * Update contact balance
     */
    protected function updateContactBalance(int $contactId): void
    {
        $contact = Contact::find($contactId);

        if (!$contact) {
            return;
        }

        // Calculate total due from all transactions
        $totalDue = Transaction::where('contact_id', $contactId)
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
     * Get drafts
     */
    public function getDrafts(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->getTransactions([
            'business_id' => $businessId,
            'status' => Transaction::STATUS_DRAFT,
        ], $perPage);
    }

    /**
     * Get quotations
     */
    public function getQuotations(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return Transaction::query()
            ->with(['contact:id,name,mobile', 'createdBy:id,first_name,last_name'])
            ->forBusiness($businessId)
            ->quotations()
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    /**
     * Convert draft/quotation to final invoice
     */
    public function convertToInvoice(Transaction $transaction, int $userId): Transaction
    {
        if ($transaction->status === Transaction::STATUS_FINAL && !$transaction->is_quotation) {
            throw new \InvalidArgumentException('Transaction is already a final invoice');
        }

        return $this->updateSell($transaction, [
            'status' => Transaction::STATUS_FINAL,
            'is_quotation' => false,
        ]);
    }
}
