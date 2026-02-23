<?php

namespace Modules\Business\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessLocation extends Model
{
    use SoftDeletes;

    protected $table = 'business_locations';

    protected $fillable = [
        'business_id',
        'location_id',
        'name',
        'landmark',
        'country',
        'state',
        'city',
        'zip_code',
        'invoice_scheme_id',
        'sale_invoice_scheme_id',
        'invoice_layout_id',
        'sale_invoice_layout_id',
        'selling_price_group_id',
        'print_receipt_on_invoice',
        'receipt_printer_type',
        'printer_id',
        'mobile',
        'alternate_number',
        'email',
        'website',
        'featured_products',
        'is_active',
        'default_payment_accounts',
        'custom_field1',
        'custom_field2',
        'custom_field3',
        'custom_field4',
        'accounting_default_map',
    ];

    protected $casts = [
        'featured_products'       => 'array',
        'default_payment_accounts' => 'array',
        'accounting_default_map'  => 'array',
        'is_active'               => 'boolean',
        'print_receipt_on_invoice' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'business_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeForBusiness($query, int $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function getLocationAddressAttribute(): string
    {
        $parts = array_filter([
            $this->landmark,
            $this->city,
            $this->state,
            $this->zip_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
