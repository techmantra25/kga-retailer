<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    // use HasFactory;

    protected $table = "stock";

    /**
     * Get the purchase_order that owns the Stock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase_order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    /**
     * Get all of the products for the Stock
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(\App\Models\StockProduct::class, 'stock_id', 'id');
    }

    /**
     * Get the return_spare that owns the Stock
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function return_spare(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ReturnSpare::class, 'return_spare_id', 'id');
    }
}
