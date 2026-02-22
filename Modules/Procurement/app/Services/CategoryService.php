<?php

namespace Modules\Procurement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Procurement\Models\Category;

class CategoryService
{
    public function getCategories(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return Category::forBusiness($businessId)
            ->productCategories()
            ->mainCategories()
            ->with('subCategories')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getCategory(int $id, int $businessId): ?Category
    {
        return Category::where('id', $id)->where('business_id', $businessId)->with('subCategories')->first();
    }

    public function createCategory(array $data): Category
    {
        $data['category_type'] = $data['category_type'] ?? 'product';
        $data['parent_id']     = $data['parent_id'] ?? 0;
        return Category::create($data);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);
        return $category->fresh();
    }

    public function deleteCategory(Category $category): void
    {
        $category->delete();
    }
}
