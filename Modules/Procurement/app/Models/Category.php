<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;

    protected $table = 'categories';

    protected $guarded = ['id'];

    // parent_id = 0 means main category; otherwise it's the parent's ID
    public function subCategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeProductCategories($query)
    {
        return $query->where('category_type', 'product');
    }

    public function scopeMainCategories($query)
    {
        return $query->where('parent_id', 0);
    }
}
