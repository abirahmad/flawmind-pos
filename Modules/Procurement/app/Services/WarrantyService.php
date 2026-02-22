<?php

namespace Modules\Procurement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Procurement\Models\Warranty;

class WarrantyService
{
    public function getWarranties(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return Warranty::forBusiness($businessId)->orderBy('name')->paginate($perPage);
    }

    public function getWarranty(int $id, int $businessId): ?Warranty
    {
        return Warranty::where('id', $id)->where('business_id', $businessId)->first();
    }

    public function createWarranty(array $data): Warranty
    {
        return Warranty::create($data);
    }

    public function updateWarranty(Warranty $warranty, array $data): Warranty
    {
        $warranty->update($data);
        return $warranty->fresh();
    }

    public function deleteWarranty(Warranty $warranty): void
    {
        $warranty->delete();
    }
}
