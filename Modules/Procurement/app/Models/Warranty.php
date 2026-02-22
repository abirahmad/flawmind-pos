<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Warranty extends Model
{
    protected $table = 'warranties';

    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'warranty_id');
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->duration} {$this->duration_type})";
    }

    public function getEndDate(string $startDate): string
    {
        $date = Carbon::parse($startDate);

        return match ($this->duration_type) {
            'days'   => $date->addDays($this->duration)->toDateString(),
            'months' => $date->addMonths($this->duration)->toDateString(),
            'years'  => $date->addYears($this->duration)->toDateString(),
            default  => $date->toDateString(),
        };
    }
}
