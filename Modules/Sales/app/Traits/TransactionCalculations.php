<?php

namespace Modules\Sales\Traits;

trait TransactionCalculations
{
    /**
     * Calculate line total before tax
     */
    protected function calculateLineTotal(float $unitPrice, float $quantity, float $discount = 0, string $discountType = 'fixed'): float
    {
        $lineTotal = $unitPrice * $quantity;

        if ($discount > 0) {
            $lineTotal -= $this->calculateDiscount($lineTotal, $discount, $discountType);
        }

        return round($lineTotal, 4);
    }

    /**
     * Calculate discount amount
     */
    protected function calculateDiscount(float $amount, float $discount, string $discountType = 'fixed'): float
    {
        if ($discountType === 'percentage') {
            return round(($amount * $discount) / 100, 4);
        }

        return round($discount, 4);
    }

    /**
     * Calculate tax amount
     */
    protected function calculateTax(float $amount, float $taxRate, bool $isInclusive = false): float
    {
        if ($isInclusive) {
            return round($amount - ($amount / (1 + ($taxRate / 100))), 4);
        }

        return round(($amount * $taxRate) / 100, 4);
    }

    /**
     * Calculate invoice totals
     */
    protected function calculateInvoiceTotals(array $lines, float $discount = 0, string $discountType = 'fixed', float $taxRate = 0, float $shippingCharges = 0): array
    {
        $totalBeforeTax = 0;
        $totalTax = 0;

        foreach ($lines as $line) {
            $lineTotal = $this->calculateLineTotal(
                $line['unit_price'] ?? 0,
                $line['quantity'] ?? 0,
                $line['line_discount_amount'] ?? 0,
                $line['line_discount_type'] ?? 'fixed'
            );

            $totalBeforeTax += $lineTotal;
            $totalTax += ($line['item_tax'] ?? 0) * ($line['quantity'] ?? 0);
        }

        $invoiceDiscount = $this->calculateDiscount($totalBeforeTax, $discount, $discountType);
        $invoiceTax = $this->calculateTax($totalBeforeTax - $invoiceDiscount, $taxRate);

        $finalTotal = $totalBeforeTax - $invoiceDiscount + $totalTax + $invoiceTax + $shippingCharges;

        return [
            'total_before_tax' => round($totalBeforeTax, 4),
            'discount_amount' => round($invoiceDiscount, 4),
            'tax_amount' => round($totalTax + $invoiceTax, 4),
            'shipping_charges' => round($shippingCharges, 4),
            'final_total' => round($finalTotal, 4),
        ];
    }

    /**
     * Calculate payment status
     */
    protected function calculatePaymentStatus(float $finalTotal, float $totalPaid): string
    {
        if ($totalPaid >= $finalTotal) {
            return 'paid';
        }

        if ($totalPaid > 0) {
            return 'partial';
        }

        return 'due';
    }

    /**
     * Round amount based on method
     */
    protected function roundAmount(float $amount, string $method = 'none'): float
    {
        return match ($method) {
            'round' => round($amount),
            'floor' => floor($amount),
            'ceil' => ceil($amount),
            default => $amount,
        };
    }
}
