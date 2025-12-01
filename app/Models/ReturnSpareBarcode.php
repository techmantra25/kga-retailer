<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class ReturnSpareBarcode extends Model
{
    protected $table = "return_spare_barcodes";

    /**
     * Get the products that owns the ReturnSpareBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    /**
     * Get the return_spare that owns the ReturnSpareBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function return_spare(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ReturnSpare::class, 'return_spare_id', 'id');
    }
}
