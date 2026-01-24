<?php

namespace Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionSellLine extends Model
{
    protected $table = 'transaction_sell_lines';

    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:4',
        'quantity_returned' => 'decimal:4',
        'secondary_unit_quantity' => 'decimal:4',
        'unit_price_before_discount' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'unit_price_inc_tax' => 'decimal:4',
        'line_discount_amount' => 'decimal:4',
        'item_tax' => 'decimal:4',
        'so_quantity_invoiced' => 'decimal:4',
    ];

    /**
     * Relationships
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }

    public function parentSellLine(): BelongsTo
    {
        return $this->belongsTo(TransactionSellLine::class, 'parent_sell_line_id');
    }

    public function childrenLines(): HasMany
    {
        return $this->hasMany(TransactionSellLine::class, 'parent_sell_line_id');
    }

    public function modifiers(): HasMany
    {
        return $this->hasMany(TransactionSellLine::class, 'parent_sell_line_id')
            ->where('children_type', 'modifier');
    }

    /**
     * Accessors
     */
    public function getLineTotalAttribute(): float
    {
        return ($this->unit_price_inc_tax * $this->quantity) - $this->getDiscountAmount();
    }

    public function getLineTotalExcTaxAttribute(): float
    {
        return ($this->unit_price * $this->quantity) - $this->getDiscountAmount();
    }

    public function getDiscountAmount(): float
    {
        if ($this->line_discount_type === 'percentage') {
            return ($this->unit_price * $this->quantity * $this->line_discount_amount) / 100;
        }

        return $this->line_discount_amount ?? 0;
    }

    public function getTotalTaxAttribute(): float
    {
        return $this->item_tax * $this->quantity;
    }

    public function getQuantityAvailableForReturnAttribute(): float
    {
        return $this->quantity - $this->quantity_returned;
    }
}
