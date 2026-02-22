<?php

namespace Modules\Procurement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Procurement\Models\Unit;

class UnitService
{
    public function getUnits(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return Unit::forBusiness($businessId)->with('baseUnit:id,actual_name,short_name')->orderBy('actual_name')->paginate($perPage);
    }

    public function getUnit(int $id, int $businessId): ?Unit
    {
        return Unit::where('id', $id)->where('business_id', $businessId)->with('subUnits', 'baseUnit')->first();
    }

    public function createUnit(array $data): Unit
    {
        return Unit::create($data);
    }

    public function updateUnit(Unit $unit, array $data): Unit
    {
        $unit->update($data);
        return $unit->fresh();
    }

    public function deleteUnit(Unit $unit): void
    {
        $unit->delete();
    }
}
