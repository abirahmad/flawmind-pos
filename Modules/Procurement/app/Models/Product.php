<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $table = 'products';

    protected $guarded = ['id'];

    protected $casts = [
        'alert_quantity'             => 'decimal:4',
        'expiry_period'              => 'decimal:2',
        'enable_stock'               => 'boolean',
        'enable_sr_no'               => 'boolean',
        'is_inactive'                => 'boolean',
        'not_for_selling'            => 'boolean',
        'woocommerce_disable_sync'   => 'boolean',
    ];

    // Product types
    public const TYPE_SINGLE   = 'single';
    public const TYPE_VARIABLE = 'variable';
    public const TYPE_COMBO    = 'combo';
    public const TYPE_MODIFIER = 'modifier';

    // Tax types
    public const TAX_INCLUSIVE = 'inclusive';
    public const TAX_EXCLUSIVE = 'exclusive';

    // Barcode types
    public const BARCODE_TYPES = ['C39', 'C128', 'EAN-13', 'EAN-8', 'UPC-A', 'UPC-E', 'ITF-14'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function productVariations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(Variation::class, 'product_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function secondaryUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'secondary_unit_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class, 'warranty_id');
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(BusinessLocation::class, 'product_locations', 'product_id', 'location_id');
    }

    public function rackDetails(): HasMany
    {
        return $this->hasMany(ProductRack::class, 'product_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_inactive', false);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_inactive', true);
    }

    public function scopeForSelling($query)
    {
        return $query->where('not_for_selling', false);
    }

    public function scopeNotForSelling($query)
    {
        return $query->where('not_for_selling', true);
    }

    public function scopeWithStock($query)
    {
        return $query->where('enable_stock', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Static helpers
    |--------------------------------------------------------------------------
    */

    public static function productTypes(): array
    {
        return [
            self::TYPE_SINGLE   => 'Single',
            self::TYPE_VARIABLE => 'Variable',
            self::TYPE_COMBO    => 'Combo',
            self::TYPE_MODIFIER => 'Modifier',
        ];
    }
}
