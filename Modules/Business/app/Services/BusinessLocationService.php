<?php

namespace Modules\Business\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Business\Models\BusinessLocation;

class BusinessLocationService
{
    public function getLocations(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return BusinessLocation::forBusiness($businessId)
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function getLocation(int $id, int $businessId): ?BusinessLocation
    {
        return BusinessLocation::where('id', $id)
            ->where('business_id', $businessId)
            ->first();
    }

    public function createLocation(array $data): BusinessLocation
    {
        return BusinessLocation::create($data);
    }

    public function updateLocation(BusinessLocation $location, array $data): BusinessLocation
    {
        $location->update($data);
        return $location->fresh();
    }

    public function deleteLocation(BusinessLocation $location): void
    {
        $location->delete();
    }

    public function toggleActive(BusinessLocation $location): BusinessLocation
    {
        $location->update(['is_active' => !$location->is_active]);
        return $location->fresh();
    }

    public function isLocationIdUnique(int $businessId, string $locationId, ?int $excludeId = null): bool
    {
        $query = BusinessLocation::where('business_id', $businessId)
            ->where('location_id', $locationId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->doesntExist();
    }

    public function getActiveLocations(int $businessId): \Illuminate\Database\Eloquent\Collection
    {
        return BusinessLocation::forBusiness($businessId)->active()->orderBy('name')->get();
    }
}
