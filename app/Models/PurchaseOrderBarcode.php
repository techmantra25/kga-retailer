<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PurchaseOrderBarcode extends Model
{
    // use HasFactory;

    /**
     * Get the product that owns the PurchaseOrderBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id')->orderBy('title');
    }

    /**
     * Get the order that owns the PurchaseOrderBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }
    public function goodsWarranty(): HasMany
    {
        return $this->hasMany(GoodsWarranty::class, 'goods_id', 'product_id');
    }
    public function productWarranty(): HasMany
    {
        return $this->hasMany(ProductWarranty::class, 'goods_id', 'product_id');
    }

   
}
