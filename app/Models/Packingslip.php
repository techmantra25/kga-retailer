<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SalesOrder;
use App\Models\PackingslipProduct;

class Packingslip extends Model
{
    /**
     * Get the sales_order that owns the Packingslip
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sales_order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id', 'id');
    }

    /**
     * Get all of the packingslip_products for the Packingslip
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function packingslip_products(): HasMany
    {
        return $this->hasMany(PackingslipProduct::class, 'packingslip_id', 'id');
    }
}
