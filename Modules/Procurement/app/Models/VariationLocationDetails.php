<?php

namespace Modules\Procurement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariationLocationDetails extends Model
{
    protected $table = 'variation_location_details';

    protected $guarded = ['id'];

    protected $casts = [
        'qty_available' => 'decimal:4',
    ];

    public function variation(): BelongsTo
    {
        return $this->belongsTo(Variation::class, 'variation_id');
    }
}
