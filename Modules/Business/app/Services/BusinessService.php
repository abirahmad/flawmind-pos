<?php

namespace Modules\Business\Services;

use Illuminate\Support\Facades\Storage;
use Modules\Business\Models\Business;

class BusinessService
{
    public function getBusinessById(int $businessId): ?Business
    {
        return Business::find($businessId);
    }

    public function updateBusiness(Business $business, array $data): Business
    {
        if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
            if ($business->logo) {
                Storage::disk('public')->delete($business->logo);
            }
            $data['logo'] = $data['logo']->store('business/logos', 'public');
        }

        $business->update($data);
        $business->refresh();

        return $business;
    }

    public function getBusinessSettings(int $businessId): ?Business
    {
        return Business::with('locations')->find($businessId);
    }

    public function toggleActive(Business $business): Business
    {
        $business->update(['is_active' => !$business->is_active]);
        return $business->fresh();
    }
}
