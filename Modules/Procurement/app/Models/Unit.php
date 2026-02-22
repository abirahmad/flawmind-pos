<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use SoftDeletes;

    protected $table = 'units';

    protected $guarded = ['id'];

    protected $casts = [
        'allow_decimal'        => 'boolean',
        'base_unit_multiplier' => 'decimal:4',
    ];

    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function subUnits(): HasMany
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->base_unit_id && $this->base_unit_multiplier) {
            return "{$this->actual_name} ({$this->base_unit_multiplier} {$this->short_name})";
        }
        return $this->actual_name;
    }
}
