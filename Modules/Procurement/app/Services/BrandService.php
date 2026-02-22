<?php

namespace Modules\Procurement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Procurement\Models\Brand;

class BrandService
{
    public function getBrands(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return Brand::forBusiness($businessId)->orderBy('name')->paginate($perPage);
    }

    public function getBrand(int $id, int $businessId): ?Brand
    {
        return Brand::where('id', $id)->where('business_id', $businessId)->first();
    }

    public function createBrand(array $data): Brand
    {
        return Brand::create($data);
    }

    public function updateBrand(Brand $brand, array $data): Brand
    {
        $brand->update($data);
        return $brand->fresh();
    }

    public function deleteBrand(Brand $brand): void
    {
        $brand->delete();
    }
}
