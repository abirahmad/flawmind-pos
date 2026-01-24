<?php

namespace Modules\Sales\Services;

use Illuminate\Support\Facades\DB;
use Modules\Sales\Models\Transaction;
use Modules\Sales\Models\TransactionSellLine;
use Modules\Sales\Models\Product;

class StockService extends BaseService
{
    /**
     * Decrease stock for a sell transaction
     */
    public function decreaseStockForSell(Transaction $transaction): void
    {
        foreach ($transaction->sellLines as $line) {
            $this->decreaseProductStock(
                $line->product_id,
                $line->variation_id,
                $transaction->location_id,
                $line->quantity
            );
        }
    }

    /**
     * Reverse stock decrease for a sell transaction
     */
    public function reverseStockForSell(Transaction $transaction): void
    {
        foreach ($transaction->sellLines as $line) {
            $this->increaseProductStock(
                $line->product_id,
                $line->variation_id,
                $transaction->location_id,
                $line->quantity
            );
        }
    }

    /**
     * Increase stock for a return transaction
     */
    public function increaseStockForReturn(Transaction $return): void
    {
        foreach ($return->sellLines as $line) {
            $this->increaseProductStock(
                $line->product_id,
                $line->variation_id,
                $return->location_id,
                $line->quantity
            );
        }
    }

    /**
     * Decrease product stock at a location
     */
    public function decreaseProductStock(int $productId, int $variationId, ?int $locationId, float $quantity): void
    {
        $product = Product::find($productId);

        if (!$product || !$product->enable_stock) {
            return;
        }

        // Update variation_location_details table
        DB::table('variation_location_details')
            ->where('product_id', $productId)
            ->where('variation_id', $variationId)
            ->where('location_id', $locationId)
            ->decrement('qty_available', $quantity);
    }

    /**
     * Increase product stock at a location
     */
    public function increaseProductStock(int $productId, int $variationId, ?int $locationId, float $quantity): void
    {
        $product = Product::find($productId);

        if (!$product || !$product->enable_stock) {
            return;
        }

        // Check if record exists
        $exists = DB::table('variation_location_details')
            ->where('product_id', $productId)
            ->where('variation_id', $variationId)
            ->where('location_id', $locationId)
            ->exists();

        if ($exists) {
            DB::table('variation_location_details')
                ->where('product_id', $productId)
                ->where('variation_id', $variationId)
                ->where('location_id', $locationId)
                ->increment('qty_available', $quantity);
        } else {
            DB::table('variation_location_details')->insert([
                'product_id' => $productId,
                'variation_id' => $variationId,
                'location_id' => $locationId,
                'qty_available' => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Get available stock for a product variation at a location
     */
    public function getAvailableStock(int $productId, int $variationId, ?int $locationId = null): float
    {
        $query = DB::table('variation_location_details')
            ->where('product_id', $productId)
            ->where('variation_id', $variationId);

        if ($locationId) {
            $query->where('location_id', $locationId);
        }

        return (float) $query->sum('qty_available');
    }

    /**
     * Check if stock is available
     */
    public function isStockAvailable(int $productId, int $variationId, ?int $locationId, float $quantity): bool
    {
        $product = Product::find($productId);

        if (!$product || !$product->enable_stock) {
            return true;
        }

        $available = $this->getAvailableStock($productId, $variationId, $locationId);

        return $available >= $quantity;
    }

    /**
     * Validate stock for sell lines
     */
    public function validateStockForSell(array $lines, ?int $locationId): array
    {
        $errors = [];

        foreach ($lines as $index => $line) {
            $product = Product::find($line['product_id']);

            if (!$product || !$product->enable_stock) {
                continue;
            }

            $available = $this->getAvailableStock(
                $line['product_id'],
                $line['variation_id'],
                $locationId
            );

            if ($available < $line['quantity']) {
                $errors[] = [
                    'line_index' => $index,
                    'product_id' => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'requested' => $line['quantity'],
                    'available' => $available,
                    'message' => "Insufficient stock for product. Available: {$available}, Requested: {$line['quantity']}",
                ];
            }
        }

        return $errors;
    }

    /**
     * Get stock levels for products
     */
    public function getStockLevels(int $businessId, ?int $locationId = null, ?int $categoryId = null): array
    {
        $query = DB::table('variation_location_details as vld')
            ->join('products as p', 'p.id', '=', 'vld.product_id')
            ->join('variations as v', 'v.id', '=', 'vld.variation_id')
            ->where('p.business_id', $businessId)
            ->where('p.enable_stock', true)
            ->select([
                'p.id as product_id',
                'p.name as product_name',
                'p.sku',
                'p.alert_quantity',
                'v.id as variation_id',
                'v.name as variation_name',
                'v.sub_sku',
                'vld.location_id',
                'vld.qty_available',
            ]);

        if ($locationId) {
            $query->where('vld.location_id', $locationId);
        }

        if ($categoryId) {
            $query->where('p.category_id', $categoryId);
        }

        return $query->get()->toArray();
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $businessId, ?int $locationId = null): array
    {
        $query = DB::table('variation_location_details as vld')
            ->join('products as p', 'p.id', '=', 'vld.product_id')
            ->join('variations as v', 'v.id', '=', 'vld.variation_id')
            ->where('p.business_id', $businessId)
            ->where('p.enable_stock', true)
            ->whereNotNull('p.alert_quantity')
            ->whereRaw('vld.qty_available <= p.alert_quantity')
            ->select([
                'p.id as product_id',
                'p.name as product_name',
                'p.sku',
                'p.alert_quantity',
                'v.id as variation_id',
                'v.name as variation_name',
                'vld.location_id',
                'vld.qty_available',
            ]);

        if ($locationId) {
            $query->where('vld.location_id', $locationId);
        }

        return $query->get()->toArray();
    }
}
