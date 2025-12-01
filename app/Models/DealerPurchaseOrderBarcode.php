<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;
use App\Models\DealerPurchaseOrder;

class DealerPurchaseOrderBarcode extends Model
{
    //

    protected $table = "dealer_purchase_order_barcodes";


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

    /**
     * Get the barcode that owns the DealerPurchaseOrderBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barcode(): BelongsTo
    {
        return $this->belongsTo(StockBarcode::class, 'barcode_no', 'barcode_no');
    }
}
