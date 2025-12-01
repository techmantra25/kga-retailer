<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class GoodsWarranty extends Model
{
    //

    protected $table = "goods_warranty";


    /**
     * Get the goods that owns the GoodsWarranty
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'goods_id', 'id');
    }
}
