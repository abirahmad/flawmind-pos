<?php

namespace Modules\Procurement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\Procurement\Models\BusinessLocation;
use Modules\Procurement\Models\Product;
use Modules\Procurement\Models\ProductRack;
use Modules\Procurement\Models\ProductVariation;
use Modules\Procurement\Models\Variation;
use Modules\Procurement\Models\VariationLocationDetails;
use Modules\Procurement\Models\VariationValueTemplate;

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
     * Create a new product with variations, rack details, and location stock records.
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $skuType = $data['sku_type'] ?? 'with_out_variation';

            $productData = collect($data)->except([
                'variations', 'product_variations', 'combo_variations',
                'location_ids', 'product_racks', 'sku_type',
                'item_level_purchase_price_total', 'purchase_price_inc_tax',
                'selling_price', 'selling_price_inc_tax',
            ])->toArray();

            // Insert with empty SKU placeholder if not provided; update after we have the ID
            $skuProvided = !empty($productData['sku']);
            if (!$skuProvided) {
                $productData['sku'] = '';
            }

            $product = Product::create($productData);

            // Auto-generate SKU: P + zero-padded product ID (e.g. P0001)
            if (!$skuProvided) {
                $product->sku = 'P' . str_pad($product->id, 4, '0', STR_PAD_LEFT);
                $product->save();
            }

            // Assign product to locations
            if (!empty($data['location_ids'])) {
                $product->locations()->sync($data['location_ids']);
            }

            // Save warehouse rack positions
            if (!empty($data['product_racks'])) {
                $this->saveRackDetails($product, $data['product_racks']);
            }

            // Build variations based on product type
            if ($product->type === Product::TYPE_SINGLE || $product->type === Product::TYPE_MODIFIER) {
                $this->createSingleVariation($product, $data['variations'][0] ?? [], $skuType);
            } elseif ($product->type === Product::TYPE_VARIABLE) {
                $this->createVariableVariations($product, $data['product_variations'] ?? [], $skuType);
            } elseif ($product->type === Product::TYPE_COMBO) {
                $this->createComboVariation($product, $data['combo_variations'] ?? [], $data);
            }

            return $product->fresh(['productVariations.variations', 'brand', 'unit', 'category', 'rackDetails']);
        });
    }

    /**
     * Update an existing product's fields, rack details, and variation prices.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $productData = collect($data)->except([
                'variations', 'product_variations', 'combo_variations',
                'location_ids', 'product_racks', 'sku_type',
            ])->toArray();

            $product->update($productData);

            if (isset($data['location_ids'])) {
                $product->locations()->sync($data['location_ids']);
            }

            // Replace rack details if provided
            if (isset($data['product_racks'])) {
                $product->rackDetails()->delete();
                if (!empty($data['product_racks'])) {
                    $this->saveRackDetails($product, $data['product_racks']);
                }
            }

            // Update single/modifier variation prices
            if (!empty($data['variations'])) {
                foreach ($data['variations'] as $varData) {
                    $varId       = $varData['id'];
                    $updateFields = collect($varData)->except(['id'])->toArray();
                    Variation::where('id', $varId)
                        ->where('product_id', $product->id)
                        ->update($updateFields);
                }
            }

            // Update variable product variation prices
            if (!empty($data['product_variations'])) {
                foreach ($data['product_variations'] as $pvData) {
                    foreach ($pvData['variations'] ?? [] as $varData) {
                        $varId        = $varData['id'];
                        $updateFields = collect($varData)->except(['id'])->toArray();
                        Variation::where('id', $varId)
                            ->where('product_id', $product->id)
                            ->update($updateFields);
                    }
                }
            }

            return $product->fresh(['productVariations.variations', 'brand', 'unit', 'category', 'rackDetails']);
        });
    }

    /**
     * Delete a product and all associated records.
     */
    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->productVariations()->each(function ($pv) {
                $pv->variations()->each(function ($v) {
                    $v->locationDetails()->delete();
                    $v->groupPrices()->delete();
                    $v->delete();
                });
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

    /**
     * Generate a sub-SKU for a variation.
     *
     * with_variation:     sku-{sanitized_value_name}   (e.g. P0001-Red)
     * with_out_variation: sku-{counter}                (e.g. P0001-1)
     */
    private function generateSubSku(string $productSku, string $variationName, string $skuType, int $counter = 1): string
    {
        if ($skuType === 'with_variation') {
            $sanitized = preg_replace('/[^A-Za-z0-9\-]/', '', $variationName);
            return $productSku . '-' . ($sanitized ?: $counter);
        }

        return $productSku . '-' . $counter;
    }

    /**
     * Create a single DUMMY variation for single/modifier products.
     */
    private function createSingleVariation(Product $product, array $variationData, string $skuType): void
    {
        $productVariation = ProductVariation::create([
            'name'       => 'DUMMY',
            'product_id' => $product->id,
            'is_dummy'   => true,
        ]);

        $subSku = !empty($variationData['sub_sku'])
            ? $variationData['sub_sku']
            : $this->generateSubSku($product->sku, 'DUMMY', $skuType, 1);

        $variation = Variation::create(array_merge(
            collect($variationData)->except(['sub_sku'])->toArray(),
            [
                'name'                 => 'DUMMY',
                'product_id'           => $product->id,
                'product_variation_id' => $productVariation->id,
                'sub_sku'              => $subSku,
            ]
        ));

        $this->seedVariationLocationDetails($product, $variation);
    }

    /**
     * Create variable product variations.
     * Handles sub-SKU generation and auto-creation of VariationValueTemplate records.
     */
    private function createVariableVariations(Product $product, array $productVariations, string $skuType): void
    {
        foreach ($productVariations as $pvData) {
            $productVariation = ProductVariation::create([
                'name'                  => $pvData['name'],
                'product_id'            => $product->id,
                'variation_template_id' => $pvData['variation_template_id'] ?? null,
                'is_dummy'              => false,
            ]);

            $counter = 1;
            foreach ($pvData['variations'] ?? [] as $variationData) {
                $variationName = $variationData['name'];

                // Auto-create VariationValueTemplate if a template is linked but no value ID given
                $variationValueId = $variationData['variation_value_id'] ?? null;
                if (!$variationValueId && !empty($pvData['variation_template_id'])) {
                    $vvt = VariationValueTemplate::firstOrCreate([
                        'variation_template_id' => $pvData['variation_template_id'],
                        'name'                  => $variationName,
                    ]);
                    $variationValueId = $vvt->id;
                }

                $subSku = !empty($variationData['sub_sku'])
                    ? $variationData['sub_sku']
                    : $this->generateSubSku($product->sku, $variationName, $skuType, $counter);

                $variation = Variation::create(array_merge(
                    collect($variationData)->except(['sub_sku', 'variation_value_id'])->toArray(),
                    [
                        'product_id'           => $product->id,
                        'product_variation_id' => $productVariation->id,
                        'variation_value_id'   => $variationValueId,
                        'sub_sku'              => $subSku,
                    ]
                ));

                $this->seedVariationLocationDetails($product, $variation);
                $counter++;
            }
        }
    }

    /**
     * Create a DUMMY variation for combo products.
     * The combo composition is stored as JSON in the combo_variations column.
     */
    private function createComboVariation(Product $product, array $comboVariations, array $data): void
    {
        $productVariation = ProductVariation::create([
            'name'       => 'DUMMY',
            'product_id' => $product->id,
            'is_dummy'   => true,
        ]);

        $variation = Variation::create([
            'name'                   => 'DUMMY',
            'product_id'             => $product->id,
            'product_variation_id'   => $productVariation->id,
            'sub_sku'                => $product->sku . '-1',
            'default_purchase_price' => $data['item_level_purchase_price_total'] ?? 0,
            'dpp_inc_tax'            => $data['purchase_price_inc_tax'] ?? 0,
            'default_sell_price'     => $data['selling_price'] ?? 0,
            'sell_price_inc_tax'     => $data['selling_price_inc_tax'] ?? 0,
            'profit_percent'         => 0,
            'combo_variations'       => $comboVariations,
        ]);

        $this->seedVariationLocationDetails($product, $variation);
    }

    /**
     * Create VariationLocationDetails rows (qty = 0) for every business location.
     * This initialises stock tracking so purchases/sales can update the correct rows.
     */
    private function seedVariationLocationDetails(Product $product, Variation $variation): void
    {
        $locationIds = BusinessLocation::where('business_id', $product->business_id)->pluck('id');

        foreach ($locationIds as $locationId) {
            VariationLocationDetails::firstOrCreate(
                [
                    'product_id'           => $product->id,
                    'variation_id'         => $variation->id,
                    'location_id'          => $locationId,
                ],
                [
                    'product_variation_id' => $variation->product_variation_id,
                    'qty_available'        => 0,
                ]
            );
        }
    }

    /**
     * Save product rack details, replacing any existing entries.
     */
    private function saveRackDetails(Product $product, array $racks): void
    {
        $product->rackDetails()->delete();

        foreach ($racks as $rack) {
            ProductRack::create([
                'product_id'  => $product->id,
                'location_id' => $rack['location_id'],
                'rack'        => $rack['rack'] ?? null,
                'row'         => $rack['row'] ?? null,
                'position'    => $rack['position'] ?? null,
            ]);
        }
    }
}
