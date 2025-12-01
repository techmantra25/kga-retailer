<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class CRPFinalSpare extends Model
{
    protected $table = "crp_final_spare";

    /**
     * Get the product that owns the CustomerAmcRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

     
    public function partsName(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'spare_id', 'id');
    }
    public function ProductData(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'spare_id', 'id');
    }

    
  
}
