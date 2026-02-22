<?php

namespace Modules\Procurement\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'name'                   => $this->name,
            'sku'                    => $this->sku,
            'type'                   => $this->type,
            'is_inactive'            => (bool) $this->is_inactive,
            'not_for_selling'        => (bool) $this->not_for_selling,
            'enable_stock'           => (bool) $this->enable_stock,
            'enable_sr_no'           => (bool) $this->enable_sr_no,
            'alert_quantity'         => (float) $this->alert_quantity,
            'barcode_type'           => $this->barcode_type,
            'tax_type'               => $this->tax_type,
            'tax'                    => $this->tax,
            'weight'                 => $this->weight,
            'product_description'    => $this->product_description,
            'expiry_period'          => $this->expiry_period,
            'expiry_period_type'     => $this->expiry_period_type,
            'woocommerce_disable_sync' => (bool) $this->woocommerce_disable_sync,

            // Relations
            'brand'        => $this->when($this->relationLoaded('brand'), fn() => [
                'id'   => $this->brand?->id,
                'name' => $this->brand?->name,
            ]),
            'unit'         => $this->when($this->relationLoaded('unit'), fn() => [
                'id'          => $this->unit?->id,
                'actual_name' => $this->unit?->actual_name,
                'short_name'  => $this->unit?->short_name,
            ]),
            'secondary_unit' => $this->when($this->relationLoaded('secondaryUnit') && $this->secondaryUnit, fn() => [
                'id'          => $this->secondaryUnit->id,
                'actual_name' => $this->secondaryUnit->actual_name,
                'short_name'  => $this->secondaryUnit->short_name,
            ]),
            'category'     => $this->when($this->relationLoaded('category'), fn() => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ]),
            'sub_category' => $this->when($this->relationLoaded('subCategory') && $this->subCategory, fn() => [
                'id'   => $this->subCategory->id,
                'name' => $this->subCategory->name,
            ]),
            'warranty'     => $this->when($this->relationLoaded('warranty') && $this->warranty, fn() => [
                'id'            => $this->warranty->id,
                'name'          => $this->warranty->name,
                'duration'      => $this->warranty->duration,
                'duration_type' => $this->warranty->duration_type,
            ]),

            'image_url'         => $this->image_url,
            'product_variations' => $this->when(
                $this->relationLoaded('productVariations'),
                fn() => ProductVariationResource::collection($this->productVariations)
            ),
            'locations'    => $this->when(
                $this->relationLoaded('locations'),
                fn() => $this->locations->pluck('id')
            ),
            'rack_details' => $this->when($this->relationLoaded('rackDetails'), fn() => $this->rackDetails->map(fn($r) => [
                'id'          => $r->id,
                'location_id' => $r->location_id,
                'rack'        => $r->rack,
                'row'         => $r->row,
                'position'    => $r->position,
            ])),

            // Custom fields
            'custom_fields' => $this->getCustomFields(),

            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function getCustomFields(): array
    {
        $fields = [];
        for ($i = 1; $i <= 20; $i++) {
            $key = "product_custom_field{$i}";
            if (!is_null($this->{$key})) {
                $fields[$key] = $this->{$key};
            }
        }
        return $fields;
    }
}
