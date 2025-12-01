<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;
use App\Models\DealerPurchaseOrder;

class DealerPurchaseOrderProduct extends Model
{
    //

    protected $table = "dealer_purchase_order_products";


    /**
     * Get the product that owns the DealerPurchaseOrderBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get the order that owns the DealerPurchaseOrderBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(DealerPurchaseOrder::class, 'dealer_purchase_order_id', 'id');
    }


}
