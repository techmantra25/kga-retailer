<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;
use App\Models\CustomerAmcRequest;

class CustomerAmcPackage extends Model
{
    protected $table = "customer_amc_packages";

    /**
     * Get the product that owns the CustomerAmcPackage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get the request that owns the CustomerAmcPackage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(CustomerAmcRequest::class, 'request_id', 'id');
    }
}
