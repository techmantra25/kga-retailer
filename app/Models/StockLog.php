<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;
use App\Models\Packingslip;
use App\Models\PurchaseOrder;

class StockLog extends Model
{
    //

    protected $table = "stock_logs";

    /**
     * Get the product that owns the StockLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get the packingslip that owns the StockLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function packingslip(): BelongsTo
    {
        return $this->belongsTo(Packingslip::class, 'packingslip_id', 'id');
    }

    /**
     * Get the purchaseorder that owns the StockLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchaseorder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id', 'id');
    }

    /**
     * Get the returnspares that owns the StockLog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function returnspares(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ReturnSpare::class, 'return_spare_id', 'id');
    }
}
