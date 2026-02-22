<?php

namespace Modules\Procurement\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Procurement\Models\VariationTemplate;
use Modules\Procurement\Models\VariationValueTemplate;

class VariationTemplateService
{
    public function getTemplates(int $businessId, int $perPage = 15): LengthAwarePaginator
    {
        return VariationTemplate::forBusiness($businessId)->with('values')->orderBy('name')->paginate($perPage);
    }

    public function getTemplate(int $id, int $businessId): ?VariationTemplate
    {
        return VariationTemplate::where('id', $id)->where('business_id', $businessId)->with('values')->first();
    }

    public function createTemplate(array $data): VariationTemplate
    {
        $template = VariationTemplate::create([
            'name'        => $data['name'],
            'business_id' => $data['business_id'],
        ]);

        foreach ($data['values'] ?? [] as $value) {
            VariationValueTemplate::create([
                'name'                  => $value['name'],
                'variation_template_id' => $template->id,
            ]);
        }

        return $template->fresh('values');
    }

    public function updateTemplate(VariationTemplate $template, array $data): VariationTemplate
    {
        $template->update(['name' => $data['name']]);

        // Sync values: delete removed, add new
        if (isset($data['values'])) {
            $existingIds = collect($data['values'])->pluck('id')->filter();
            $template->values()->whereNotIn('id', $existingIds)->delete();

            foreach ($data['values'] as $value) {
                if (!empty($value['id'])) {
                    VariationValueTemplate::where('id', $value['id'])->update(['name' => $value['name']]);
                } else {
                    VariationValueTemplate::create([
                        'name'                  => $value['name'],
                        'variation_template_id' => $template->id,
                    ]);
                }
            }
        }

        return $template->fresh('values');
    }

    public function deleteTemplate(VariationTemplate $template): void
    {
        $template->values()->delete();
        $template->delete();
    }

    public function addValue(VariationTemplate $template, string $name): VariationValueTemplate
    {
        return VariationValueTemplate::create([
            'name'                  => $name,
            'variation_template_id' => $template->id,
        ]);
    }

    public function removeValue(VariationTemplate $template, int $valueId): void
    {
        $template->values()->where('id', $valueId)->delete();
    }
}
