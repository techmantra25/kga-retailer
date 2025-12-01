<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Packingslip;
use App\Models\Product;

class PackingslipBarcode extends Model
{
    //

    /**
     * Get the packingslip that owns the PackingslipBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function packingslip(): BelongsTo
    {
        return $this->belongsTo(Packingslip::class, 'packingslip_id', 'id');
    }

    /**
     * Get the product that owns the PurchaseOrderBarcode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
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
