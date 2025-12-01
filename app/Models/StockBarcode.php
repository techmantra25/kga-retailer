<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\StockProduct;
use App\Models\Stock;
use App\Models\Packingslip;

class StockBarcode extends Model
{
    // use HasFactory;

    protected $table = "stock_barcodes";

    /**
     * Get the product that owns the PurchaseOrderBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    /**
     * Get the stock_product that owns the StockBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock_product(): BelongsTo
    {
        return $this->belongsTo(StockProduct::class, 'stock_id', 'id');
    }

    /**
     * Get the stock that owns the StockBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    /**
     * Get the packingslip that owns the StockBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function packingslip(): BelongsTo
    {
        return $this->belongsTo(Packingslip::class, 'packingslip_id', 'id');
    }
}
