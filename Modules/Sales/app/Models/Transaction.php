<?php

namespace Modules\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $guarded = ['id'];

    protected $casts = [
        'transaction_date' => 'datetime',
        'delivery_date' => 'datetime',
        'total_before_tax' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'final_total' => 'decimal:4',
        'shipping_charges' => 'decimal:4',
        'round_off_amount' => 'decimal:4',
        'exchange_rate' => 'decimal:3',
        'is_direct_sale' => 'boolean',
        'is_suspend' => 'boolean',
        'is_quotation' => 'boolean',
        'is_recurring' => 'boolean',
        'is_export' => 'boolean',
        'is_created_from_api' => 'boolean',
        'sales_order_ids' => 'array',
        'purchase_order_ids' => 'array',
    ];

    // Transaction types
    public const TYPE_SELL = 'sell';
    public const TYPE_SELL_RETURN = 'sell_return';
    public const TYPE_PURCHASE = 'purchase';
    public const TYPE_PURCHASE_RETURN = 'purchase_return';
    public const TYPE_EXPENSE = 'expense';
    public const TYPE_STOCK_ADJUSTMENT = 'stock_adjustment';
    public const TYPE_SALES_ORDER = 'sales_order';

    // Statuses
    public const STATUS_DRAFT = 'draft';
    public const STATUS_FINAL = 'final';
    public const STATUS_PENDING = 'pending';
    public const STATUS_ORDERED = 'ordered';
    public const STATUS_COMPLETED = 'completed';

    // Payment statuses
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_DUE = 'due';
    public const PAYMENT_PARTIAL = 'partial';

    // Shipping statuses
    public const SHIPPING_ORDERED = 'ordered';
    public const SHIPPING_PACKED = 'packed';
    public const SHIPPING_SHIPPED = 'shipped';
    public const SHIPPING_DELIVERED = 'delivered';
    public const SHIPPING_CANCELLED = 'cancelled';

    /**
     * Relationships
     */
    public function sellLines(): HasMany
    {
        return $this->hasMany(TransactionSellLine::class, 'transaction_id');
    }

    public function paymentLines(): HasMany
    {
        return $this->hasMany(TransactionPayment::class, 'transaction_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function returnParent(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'return_parent_id');
    }

    public function returnTransaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'return_parent_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
    }

    /**
     * Scopes
     */
    public function scopeSells($query)
    {
        return $query->where('type', self::TYPE_SELL);
    }

    public function scopeSellReturns($query)
    {
        return $query->where('type', self::TYPE_SELL_RETURN);
    }

    public function scopeFinal($query)
    {
        return $query->where('status', self::STATUS_FINAL);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeQuotations($query)
    {
        return $query->where('is_quotation', true);
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeForLocation($query, int $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeForContact($query, int $contactId)
    {
        return $query->where('contact_id', $contactId);
    }

    public function scopePaymentStatus($query, string $status)
    {
        return $query->where('payment_status', $status);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Accessors
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->paymentLines()
            ->where('is_return', false)
            ->sum('amount') - $this->paymentLines()
            ->where('is_return', true)
            ->sum('amount');
    }

    public function getBalanceDueAttribute(): float
    {
        return $this->final_total - $this->total_paid;
    }

    /**
     * Static methods
     */
    public static function transactionTypes(): array
    {
        return [
            self::TYPE_SELL => 'Sell',
            self::TYPE_SELL_RETURN => 'Sell Return',
            self::TYPE_PURCHASE => 'Purchase',
            self::TYPE_PURCHASE_RETURN => 'Purchase Return',
            self::TYPE_EXPENSE => 'Expense',
            self::TYPE_STOCK_ADJUSTMENT => 'Stock Adjustment',
            self::TYPE_SALES_ORDER => 'Sales Order',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_DUE => 'Due',
            self::PAYMENT_PARTIAL => 'Partial',
        ];
    }

    public static function shippingStatuses(): array
    {
        return [
            self::SHIPPING_ORDERED => 'Ordered',
            self::SHIPPING_PACKED => 'Packed',
            self::SHIPPING_SHIPPED => 'Shipped',
            self::SHIPPING_DELIVERED => 'Delivered',
            self::SHIPPING_CANCELLED => 'Cancelled',
        ];
    }
}
