<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variation extends Model
{
    use SoftDeletes;

    protected $table = 'variations';

    protected $guarded = ['id'];

    protected $casts = [
        'default_purchase_price' => 'decimal:4',
        'dpp_inc_tax'            => 'decimal:4',
        'profit_percent'         => 'decimal:4',
        'default_sell_price'     => 'decimal:4',
        'sell_price_inc_tax'     => 'decimal:4',
        'combo_variations'       => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function locationDetails(): HasMany
    {
        return $this->hasMany(VariationLocationDetails::class, 'variation_id');
    }

    public function groupPrices(): HasMany
    {
        return $this->hasMany(VariationGroupPrice::class, 'variation_id');
    }

    public function getFullNameAttribute(): string
    {
        $product = $this->product;

        if ($this->name === 'DUMMY' || !$this->name) {
            return $product->name ?? '';
        }

        return ($product->name ?? '') . ' - ' . $this->name;
    }
}
