<?php

namespace Modules\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    protected $table = 'cash_registers';

    protected $guarded = ['id'];

    protected $casts = [
        'closing_amount' => 'decimal:4',
        'closed_at' => 'datetime',
        'total_card_slips' => 'integer',
        'total_cheques' => 'integer',
    ];

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSE = 'close';

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    public function registerTransactions(): HasMany
    {
        return $this->hasMany(CashRegisterTransaction::class, 'cash_register_id');
    }

    /**
     * Scopes
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSE);
    }

    /**
     * Check if register is open
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Get current balance
     */
    public function getCurrentBalance(): float
    {
        $credits = $this->registerTransactions()
            ->where('type', 'credit')
            ->sum('amount');

        $debits = $this->registerTransactions()
            ->where('type', 'debit')
            ->sum('amount');

        return $credits - $debits;
    }

    /**
     * Get balance by payment method
     */
    public function getBalanceByMethod(string $method): float
    {
        $credits = $this->registerTransactions()
            ->where('type', 'credit')
            ->where('pay_method', $method)
            ->sum('amount');

        $debits = $this->registerTransactions()
            ->where('type', 'debit')
            ->where('pay_method', $method)
            ->sum('amount');

        return $credits - $debits;
    }
}
