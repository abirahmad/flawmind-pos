<?php

namespace Modules\Sales\Services;

use Illuminate\Support\Facades\DB;
use Modules\Sales\Models\Transaction;

class InvoiceService extends BaseService
{
    /**
     * Generate unique invoice number
     */
    public function generateInvoiceNumber(int $businessId, ?int $locationId = null, ?int $schemeId = null): string
    {
        // Get invoice scheme
        $scheme = $this->getInvoiceScheme($businessId, $schemeId);

        if (!$scheme) {
            // Fallback to simple format
            return $this->generateSimpleInvoiceNumber($businessId);
        }

        $prefix = $scheme->prefix ?? '';

        // Add year if scheme type is 'year'
        if ($scheme->scheme_type === 'year') {
            $prefix .= date('Y') . '-';
        }

        // Get next number
        $nextNumber = $scheme->start_number + $scheme->invoice_count;

        // Format with leading zeros
        $invoiceNo = $prefix . str_pad($nextNumber, $scheme->total_digits, '0', STR_PAD_LEFT);

        // Increment counter
        DB::table('invoice_schemes')
            ->where('id', $scheme->id)
            ->increment('invoice_count');

        return $invoiceNo;
    }

    /**
     * Get invoice scheme
     */
    protected function getInvoiceScheme(int $businessId, ?int $schemeId = null)
    {
        $query = DB::table('invoice_schemes')
            ->where('business_id', $businessId);

        if ($schemeId) {
            $query->where('id', $schemeId);
        } else {
            $query->where('is_default', true);
        }

        return $query->first();
    }

    /**
     * Generate simple invoice number
     */
    protected function generateSimpleInvoiceNumber(int $businessId): string
    {
        $lastInvoice = Transaction::where('business_id', $businessId)
            ->where('type', Transaction::TYPE_SELL)
            ->whereNotNull('invoice_no')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice && preg_match('/(\d+)$/', $lastInvoice->invoice_no, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return 'INV-' . date('Y') . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate invoice token for public access
     */
    public function generateInvoiceToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Get invoice by token
     */
    public function getInvoiceByToken(string $token): ?Transaction
    {
        return Transaction::where('invoice_token', $token)
            ->with(['sellLines.product', 'sellLines.variation', 'paymentLines', 'contact'])
            ->first();
    }

    /**
     * Set invoice token
     */
    public function setInvoiceToken(Transaction $transaction): string
    {
        $token = $this->generateInvoiceToken();
        $transaction->update(['invoice_token' => $token]);

        return $token;
    }

    /**
     * Get receipt data
     */
    public function getReceiptData(Transaction $transaction): array
    {
        $transaction->load([
            'sellLines.product:id,name,sku,type',
            'sellLines.variation:id,name,sub_sku',
            'paymentLines',
            'contact',
            'createdBy:id,first_name,last_name',
        ]);

        // Calculate totals
        $subtotal = $transaction->sellLines->sum(function ($line) {
            return $line->unit_price * $line->quantity;
        });

        $totalDiscount = $transaction->sellLines->sum(function ($line) {
            return $line->getDiscountAmount();
        });

        $totalTax = $transaction->sellLines->sum(function ($line) {
            return $line->item_tax * $line->quantity;
        });

        $totalPaid = $transaction->paymentLines->where('is_return', false)->sum('amount');
        $totalRefunded = $transaction->paymentLines->where('is_return', true)->sum('amount');

        return [
            'transaction' => $transaction,
            'invoice_no' => $transaction->invoice_no,
            'transaction_date' => $transaction->transaction_date,
            'customer' => $transaction->contact ? [
                'name' => $transaction->contact->full_name,
                'mobile' => $transaction->contact->mobile,
                'email' => $transaction->contact->email,
                'address' => $transaction->contact->full_address,
                'tax_number' => $transaction->contact->tax_number,
            ] : null,
            'items' => $transaction->sellLines->map(function ($line) {
                return [
                    'product_name' => $line->product->name ?? '',
                    'variation_name' => $line->variation->name ?? '',
                    'sku' => $line->product->sku ?? '',
                    'quantity' => $line->quantity,
                    'unit_price' => $line->unit_price,
                    'unit_price_inc_tax' => $line->unit_price_inc_tax,
                    'discount' => $line->getDiscountAmount(),
                    'tax' => $line->item_tax * $line->quantity,
                    'line_total' => $line->line_total,
                    'note' => $line->sell_line_note,
                ];
            }),
            'subtotal' => $subtotal,
            'total_discount' => $totalDiscount + $transaction->discount_amount,
            'discount_type' => $transaction->discount_type,
            'invoice_discount' => $transaction->discount_amount,
            'total_tax' => $totalTax + $transaction->tax_amount,
            'invoice_tax' => $transaction->tax_amount,
            'shipping_charges' => $transaction->shipping_charges,
            'round_off' => $transaction->round_off_amount,
            'final_total' => $transaction->final_total,
            'total_paid' => $totalPaid - $totalRefunded,
            'balance_due' => $transaction->final_total - ($totalPaid - $totalRefunded),
            'payment_status' => $transaction->payment_status,
            'payments' => $transaction->paymentLines->map(function ($payment) {
                return [
                    'method' => $payment->method,
                    'amount' => $payment->amount,
                    'paid_on' => $payment->paid_on,
                    'reference' => $payment->payment_ref_no,
                    'is_return' => $payment->is_return,
                ];
            }),
            'notes' => $transaction->additional_notes,
            'staff_note' => $transaction->staff_note,
            'created_by' => $transaction->createdBy ? $transaction->createdBy->first_name . ' ' . $transaction->createdBy->last_name : null,
            'shipping_address' => $transaction->shipping_address,
            'shipping_status' => $transaction->shipping_status,
        ];
    }

    /**
     * Get invoice layout
     */
    public function getInvoiceLayout(int $businessId, ?int $layoutId = null)
    {
        $query = DB::table('invoice_layouts')
            ->where('business_id', $businessId);

        if ($layoutId) {
            $query->where('id', $layoutId);
        } else {
            $query->where('is_default', true);
        }

        return $query->first();
    }
}
