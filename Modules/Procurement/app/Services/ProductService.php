<?php

namespace Modules\Procurement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Procurement\Models\Product;
use Modules\Procurement\Models\ProductVariation;
use Modules\Procurement\Models\Variation;
use Modules\Procurement\Models\VariationLocationDetails;

class ProductService
{
    /**
     * Get paginated list of products with optional filters.
     */
    public function getProducts(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['brand:id,name', 'unit:id,actual_name,short_name', 'category:id,name', 'subCategory:id,name', 'warranty:id,name,duration,duration_type']);

        if (!empty($filters['business_id'])) {
            $query->forBusiness($filters['business_id']);
        }

        if (isset($filters['is_inactive'])) {
            $query->where('is_inactive', (bool) $filters['is_inactive']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        if (!empty($filters['location_id'])) {
            $query->whereHas('locations', fn($q) => $q->where('location_id', $filters['location_id']));
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $sortBy    = $filters['sort_by']    ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get single product with all relations.
     */
    public function getProduct(int $id, int $businessId): ?Product
    {
        return Product::with([
            'brand',
            'unit',
            'secondaryUnit',
            'category',
            'subCategory',
            'warranty',
            'locations',
            'rackDetails',
            'productVariations.variations.locationDetails',
            'productVariations.variations.groupPrices.priceGroup',
        ])
        ->where('id', $id)
        ->where('business_id', $businessId)
        ->first();
    }

    /**
     * Create a new product with variations.
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $productData = collect($data)->except(['variations', 'product_variations', 'location_ids'])->toArray();
            $product     = Product::create($productData);

            // Assign locations
            if (!empty($data['location_ids'])) {
                $product->locations()->sync($data['location_ids']);
            }

            // Build variations based on product type
            if ($product->type === Product::TYPE_SINGLE || $product->type === Product::TYPE_MODIFIER) {
                $this->createSingleVariation($product, $data['variations'][0] ?? []);
            } elseif ($product->type === Product::TYPE_VARIABLE) {
                $this->createVariableVariations($product, $data['product_variations'] ?? []);
            }

            return $product->fresh(['productVariations.variations', 'brand', 'unit', 'category']);
        });
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $productData = collect($data)->except(['variations', 'product_variations', 'location_ids'])->toArray();
            $product->update($productData);

            if (isset($data['location_ids'])) {
                $product->locations()->sync($data['location_ids']);
            }

            return $product->fresh(['productVariations.variations', 'brand', 'unit', 'category']);
        });
    }

    /**
     * Delete a product.
     */
    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->productVariations()->each(function ($pv) {
                $pv->variations()->delete();
                $pv->delete();
            });
            $product->locations()->detach();
            $product->rackDetails()->delete();
            $product->delete();
        });
    }

    /**
     * Bulk deactivate products.
     */
    public function massDeactivate(array $ids, int $businessId): int
    {
        return Product::whereIn('id', $ids)->where('business_id', $businessId)->update(['is_inactive' => true]);
    }

    /**
     * Bulk delete products.
     */
    public function massDelete(array $ids, int $businessId): int
    {
        return Product::whereIn('id', $ids)->where('business_id', $businessId)->delete();
    }

    /**
     * Activate a product.
     */
    public function activateProduct(int $id, int $businessId): bool
    {
        return (bool) Product::where('id', $id)->where('business_id', $businessId)->update(['is_inactive' => false]);
    }

    /**
     * Get stock per location for a product.
     */
    public function getStock(int $productId, int $businessId): array
    {
        $product = Product::where('id', $productId)->where('business_id', $businessId)->first();
        if (!$product) {
            return [];
        }

        return VariationLocationDetails::query()
            ->where('product_id', $productId)
            ->with('variation:id,name,sub_sku')
            ->get()
            ->toArray();
    }

    /**
     * Check SKU uniqueness within a business.
     */
    public function isSkuUnique(string $sku, int $businessId, ?int $excludeProductId = null): bool
    {
        $query = Product::where('sku', $sku)->where('business_id', $businessId);

        if ($excludeProductId) {
            $query->where('id', '!=', $excludeProductId);
        }

        return $query->doesntExist();
    }

    /**
     * Get all variations for a product.
     */
    public function getVariations(int $productId, int $businessId): array
    {
        $product = Product::where('id', $productId)->where('business_id', $businessId)->first();
        if (!$product) {
            return [];
        }

        return Variation::where('product_id', $productId)
            ->with(['locationDetails', 'groupPrices.priceGroup'])
            ->get()
            ->toArray();
    }

    /**
     * Get price group prices for a product.
     */
    public function getGroupPrices(int $productId, int $businessId): array
    {
        $product = Product::where('id', $productId)->where('business_id', $businessId)->first();
        if (!$product) {
            return [];
        }

        return Variation::where('product_id', $productId)
            ->with('groupPrices.priceGroup')
            ->get()
            ->flatMap(fn($v) => $v->groupPrices)
            ->toArray();
    }

    /**
     * Update price group prices for a product.
     */
    public function updateGroupPrices(int $productId, int $businessId, array $groupPrices): void
    {
        $product = Product::where('id', $productId)->where('business_id', $businessId)->firstOrFail();

        DB::transaction(function () use ($product, $groupPrices) {
            foreach ($groupPrices as $gp) {
                \Modules\Procurement\Models\VariationGroupPrice::updateOrCreate(
                    ['variation_id' => $gp['variation_id'], 'price_group_id' => $gp['price_group_id']],
                    ['price_inc_tax' => $gp['price_inc_tax'], 'price_type' => $gp['price_type'] ?? 'fixed']
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Private helpers
    |--------------------------------------------------------------------------
    */

    private function createSingleVariation(Product $product, array $variationData): void
    {
        $productVariation = ProductVariation::create([
            'name'       => 'DUMMY',
            'product_id' => $product->id,
            'is_dummy'   => true,
        ]);

        Variation::create(array_merge([
            'name'                 => 'DUMMY',
            'product_id'           => $product->id,
            'product_variation_id' => $productVariation->id,
        ], $variationData));
    }

    private function createVariableVariations(Product $product, array $productVariations): void
    {
        foreach ($productVariations as $pvData) {
            $productVariation = ProductVariation::create([
                'name'                    => $pvData['name'],
                'product_id'              => $product->id,
                'variation_template_id'   => $pvData['variation_template_id'] ?? null,
                'is_dummy'                => false,
            ]);

            foreach ($pvData['variations'] ?? [] as $variationData) {
                Variation::create(array_merge($variationData, [
                    'product_id'           => $product->id,
                    'product_variation_id' => $productVariation->id,
                ]));
            }
        }
    }
}
