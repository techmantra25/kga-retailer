<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User;
use App\Models\Dealer;
use App\Models\DealerPurchaseOrderProduct;

class DealerPurchaseOrder extends Model
{
    //
    protected $table = "dealer_purchase_orders";

    /**
     * Get the created_by that owns the DealerPurchaseOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the dealer that owns the DealerPurchaseOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'dealer_id', 'id');
    }

    /**
     * Get all of the products for the DealerPurchaseOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(DealerPurchaseOrderProduct::class, 'dealer_purchase_order_id', 'id');
    }
}
