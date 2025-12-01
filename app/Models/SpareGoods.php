<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class SpareGoods extends Model
{
    protected $table = "spare_goods";

    /**
     * Get the spare that owns the SpareGoods
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spare(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'spare_id', 'id');
    }

    /**
     * Get the goods that owns the SpareGoods
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'goods_id', 'id');
    }
}
