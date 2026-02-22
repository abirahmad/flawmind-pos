<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariationTemplate extends Model
{
    protected $table = 'variation_templates';

    protected $guarded = ['id'];

    public function values(): HasMany
    {
        return $this->hasMany(VariationValueTemplate::class, 'variation_template_id');
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
