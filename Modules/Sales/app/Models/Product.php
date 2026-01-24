<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';

    protected $guarded = ['id'];

    protected $casts = [
        'alert_quantity' => 'decimal:4',
        'expiry_period' => 'decimal:2',
        'enable_stock' => 'boolean',
        'enable_sr_no' => 'boolean',
        'is_inactive' => 'boolean',
        'not_for_selling' => 'boolean',
        'sub_unit_ids' => 'array',
    ];

    // Product types
    public const TYPE_SINGLE = 'single';
    public const TYPE_VARIABLE = 'variable';
    public const TYPE_COMBO = 'combo';
    public const TYPE_MODIFIER = 'modifier';

    // Tax types
    public const TAX_INCLUSIVE = 'inclusive';
    public const TAX_EXCLUSIVE = 'exclusive';

    /**
     * Relationships
     */
    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class, 'product_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'tax');
    }

    /**
     * Scopes
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_inactive', false);
    }

    public function scopeForSelling($query)
    {
        return $query->where('not_for_selling', false);
    }

    public function scopeWithStock($query)
    {
        return $query->where('enable_stock', true);
    }

    /**
     * Accessors
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /**
     * Static methods
     */
    public static function productTypes(): array
    {
        return [
            self::TYPE_SINGLE => 'Single',
            self::TYPE_VARIABLE => 'Variable',
            self::TYPE_COMBO => 'Combo',
            self::TYPE_MODIFIER => 'Modifier',
        ];
    }
}
