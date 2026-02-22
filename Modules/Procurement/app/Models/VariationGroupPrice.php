<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariationGroupPrice extends Model
{
    protected $table = 'variation_group_prices';

    protected $guarded = ['id'];

    protected $casts = [
        'price_inc_tax' => 'decimal:4',
    ];

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }

    public function priceGroup(): BelongsTo
    {
        return $this->belongsTo(SellingPriceGroup::class, 'price_group_id');
    }

    /**
     * Get the calculated price based on price_type.
     * - fixed: customer pays price_inc_tax directly
     * - percentage: customer pays variation's sell_price_inc_tax × (price_inc_tax / 100)
     */
    public function getCalculatedPriceAttribute(): float
    {
        if ($this->price_type === 'percentage') {
            $variation = $this->variation;
            return (float) ($variation->sell_price_inc_tax * ($this->price_inc_tax / 100));
        }

        return (float) $this->price_inc_tax;
    }
}
