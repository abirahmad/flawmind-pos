<?php

namespace Modules\Sales\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaymentCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = PaymentResource::class;

    /**
     * Transform the resource collection into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'summary' => $this->getSummary(),
        ];
    }

    /**
     * Get summary statistics for the collection.
     */
    protected function getSummary(): array
    {
        return [
            'total_count' => $this->collection->count(),
            'total_amount' => $this->collection->sum(fn($item) => (float) $item->amount),
            'by_method' => $this->collection->groupBy('method')->map(function ($items, $method) {
                return [
                    'count' => $items->count(),
                    'amount' => $items->sum(fn($item) => (float) $item->amount),
                ];
            }),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'api_version' => '1.0',
            ],
        ];
    }
}
