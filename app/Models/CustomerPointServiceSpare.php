<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class CustomerPointServiceSpare extends Model
{
    protected $table = "customer_point_services_spare";

    /**
     * Get the product that owns the CustomerAmcRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ProductData(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'sp_id', 'id');
    }
    
  
}
