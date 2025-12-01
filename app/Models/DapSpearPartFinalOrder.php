<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class DapSpearPartFinalOrder extends Model
{
    //

    protected $table = "dap_spear_part_final_orders";

    public function ProductData(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }
}
















