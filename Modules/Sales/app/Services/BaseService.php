<?php

namespace Modules\Sales\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    /**
     * Execute callback within a database transaction
     */
    protected function executeInTransaction(callable $callback): mixed
    {
        try {
            return DB::transaction($callback);
        } catch (\Exception $e) {
            Log::error('Transaction failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Log activity
     */
    protected function logActivity(string $action, Model $model, ?array $oldValues = null, ?array $newValues = null): void
    {
        Log::info("Sales Module Activity: {$action}", [
            'model' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Generate a unique reference number
     */
    protected function generateReferenceNumber(string $prefix, int $length = 8): string
    {
        $timestamp = now()->format('ymdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, $length - strlen($timestamp)));

        return $prefix . $timestamp . $random;
    }

    /**
     * Format decimal value
     */
    protected function formatDecimal(mixed $value, int $precision = 4): float
    {
        return round((float) $value, $precision);
    }

    /**
     * Validate business ownership
     */
    protected function validateBusinessAccess(Model $model, int $businessId): bool
    {
        if (!isset($model->business_id)) {
            return true;
        }

        return $model->business_id === $businessId;
    }
}
