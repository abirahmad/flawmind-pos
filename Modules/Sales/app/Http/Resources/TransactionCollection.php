<?php

namespace Modules\Sales\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TransactionCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = TransactionResource::class;

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
            'total_amount' => $this->collection->sum(fn($item) => (float) $item->final_total),
            'total_paid' => $this->collection->sum(function ($item) {
                if ($item->relationLoaded('paymentLines')) {
                    return (float) $item->paymentLines->where('is_return', false)->sum('amount');
                }
                return 0;
            }),
            'total_due' => $this->collection->sum(function ($item) {
                $finalTotal = (float) $item->final_total;
                $paid = 0;
                if ($item->relationLoaded('paymentLines')) {
                    $paid = (float) $item->paymentLines->where('is_return', false)->sum('amount');
                }
                return max(0, $finalTotal - $paid);
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
