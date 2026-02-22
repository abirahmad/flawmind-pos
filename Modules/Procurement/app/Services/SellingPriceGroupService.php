<?php

namespace Modules\Procurement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Procurement\Models\SellingPriceGroup;

class SellingPriceGroupService
{
    public function getPriceGroups(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return SellingPriceGroup::forBusiness($businessId)->orderBy('name')->paginate($perPage);
    }

    public function getPriceGroup(int $id, int $businessId): ?SellingPriceGroup
    {
        return SellingPriceGroup::where('id', $id)->where('business_id', $businessId)->first();
    }

    public function createPriceGroup(array $data): SellingPriceGroup
    {
        return SellingPriceGroup::create(array_merge($data, ['is_active' => true]));
    }

    public function updatePriceGroup(SellingPriceGroup $group, array $data): SellingPriceGroup
    {
        $group->update($data);
        return $group->fresh();
    }

    public function deletePriceGroup(SellingPriceGroup $group): void
    {
        $group->delete();
    }

    public function toggleActive(SellingPriceGroup $group): SellingPriceGroup
    {
        $group->update(['is_active' => !$group->is_active]);
        return $group->fresh();
    }
}
