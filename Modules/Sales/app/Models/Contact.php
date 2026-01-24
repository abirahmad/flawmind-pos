<?php

namespace Modules\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $table = 'contacts';

    protected $guarded = ['id'];

    protected $casts = [
        'credit_limit' => 'decimal:4',
        'balance' => 'decimal:4',
        'dob' => 'date',
        'converted_on' => 'datetime',
        'is_default' => 'boolean',
        'is_export' => 'boolean',
        'total_rp' => 'integer',
        'total_rp_used' => 'integer',
        'total_rp_expired' => 'integer',
        'shipping_address' => 'array',
        'shipping_custom_field_details' => 'array',
    ];

    // Contact types
    public const TYPE_CUSTOMER = 'customer';
    public const TYPE_SUPPLIER = 'supplier';
    public const TYPE_BOTH = 'both';
    public const TYPE_LEAD = 'lead';

    // Contact statuses
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Relationships
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'contact_id');
    }

    public function sellTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'contact_id')
            ->where('type', Transaction::TYPE_SELL);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    /**
     * Scopes
     */
    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeCustomers($query)
    {
        return $query->whereIn('type', [self::TYPE_CUSTOMER, self::TYPE_BOTH]);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('type', [self::TYPE_SUPPLIER, self::TYPE_BOTH]);
    }

    public function scopeActive($query)
    {
        return $query->where('contact_status', self::STATUS_ACTIVE);
    }

    public function scopeLeads($query)
    {
        return $query->where('type', self::TYPE_LEAD);
    }

    /**
     * Accessors
     */
    public function getFullNameAttribute(): string
    {
        $name = trim(($this->prefix ?? '') . ' ' . ($this->first_name ?? '') . ' ' . ($this->middle_name ?? '') . ' ' . ($this->last_name ?? ''));
        return $name ?: ($this->name ?? '');
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function getAvailableRewardPointsAttribute(): int
    {
        return $this->total_rp - $this->total_rp_used - $this->total_rp_expired;
    }

    public function getAvailableCreditAttribute(): float
    {
        if (!$this->credit_limit) {
            return PHP_FLOAT_MAX;
        }

        return $this->credit_limit - $this->balance;
    }

    /**
     * Check if credit limit is exceeded
     */
    public function isCreditLimitExceeded(float $additionalAmount = 0): bool
    {
        if (!$this->credit_limit) {
            return false;
        }

        return ($this->balance + $additionalAmount) > $this->credit_limit;
    }

    /**
     * Static methods
     */
    public static function contactTypes(): array
    {
        return [
            self::TYPE_CUSTOMER => 'Customer',
            self::TYPE_SUPPLIER => 'Supplier',
            self::TYPE_BOTH => 'Both',
            self::TYPE_LEAD => 'Lead',
        ];
    }
}
