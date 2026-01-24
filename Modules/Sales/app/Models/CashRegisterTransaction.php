<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegisterTransaction extends Model
{
    protected $table = 'cash_register_transactions';

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:4',
    ];

    public const TYPE_CREDIT = 'credit';
    public const TYPE_DEBIT = 'debit';

    public const TRANSACTION_TYPE_INITIAL = 'initial';
    public const TRANSACTION_TYPE_SELL = 'sell';
    public const TRANSACTION_TYPE_TRANSFER = 'transfer';
    public const TRANSACTION_TYPE_REFUND = 'refund';

    /**
     * Relationships
     */
    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
     * Scopes
     */
    public function scopeCredits($query)
    {
        return $query->where('type', self::TYPE_CREDIT);
    }

    public function scopeDebits($query)
    {
        return $query->where('type', self::TYPE_DEBIT);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('pay_method', $method);
    }
}
