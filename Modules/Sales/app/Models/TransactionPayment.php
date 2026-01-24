<?php

namespace Modules\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionPayment extends Model
{
    protected $table = 'transaction_payments';

    protected $guarded = ['id'];

    /**
     * Default attribute values for required fields without DB defaults.
     */
    protected $attributes = [
        'is_return' => false,
        'paid_through_link' => false,
        'is_advance' => false,
        'amount' => 0,
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'paid_on' => 'datetime',
        'is_return' => 'boolean',
        'is_advance' => 'boolean',
        'paid_through_link' => 'boolean',
    ];

    // Payment methods
    public const METHOD_CASH = 'cash';
    public const METHOD_CARD = 'card';
    public const METHOD_CHEQUE = 'cheque';
    public const METHOD_BANK_TRANSFER = 'bank_transfer';
    public const METHOD_OTHER = 'other';
    public const METHOD_ADVANCE = 'advance';

    // Card types
    public const CARD_VISA = 'visa';
    public const CARD_MASTER = 'master';
    public const CARD_AMEX = 'amex';
    public const CARD_DISCOVER = 'discover';
    public const CARD_OTHER = 'other';

    /**
     * Relationships
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paymentFor(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'payment_for');
    }

    public function parentPayment(): BelongsTo
    {
        return $this->belongsTo(TransactionPayment::class, 'parent_id');
    }

    public function childPayments(): HasMany
    {
        return $this->hasMany(TransactionPayment::class, 'parent_id');
    }

    /**
     * Scopes
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', $method);
    }

    public function scopeNonReturns($query)
    {
        return $query->where('is_return', false);
    }

    public function scopeReturns($query)
    {
        return $query->where('is_return', true);
    }

    public function scopeAdvancePayments($query)
    {
        return $query->where('is_advance', true);
    }

    /**
     * Static methods
     */
    public static function paymentMethods(): array
    {
        return [
            self::METHOD_CASH => 'Cash',
            self::METHOD_CARD => 'Card',
            self::METHOD_CHEQUE => 'Cheque',
            self::METHOD_BANK_TRANSFER => 'Bank Transfer',
            self::METHOD_OTHER => 'Other',
            self::METHOD_ADVANCE => 'Advance',
        ];
    }

    public static function cardTypes(): array
    {
        return [
            self::CARD_VISA => 'Visa',
            self::CARD_MASTER => 'Mastercard',
            self::CARD_AMEX => 'American Express',
            self::CARD_DISCOVER => 'Discover',
            self::CARD_OTHER => 'Other',
        ];
    }
}
