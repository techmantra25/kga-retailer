<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class ProductWarranty extends Model
{
    //

    protected $table = "product_warranty";
    protected $fillable = [
		  'goods_id', 'dealer_type', 'warranty_type', 'additional_warranty_type', 'spear_id', 'warranty_period', 'number_of_cleaning', 'number_of_deep_cleaning', 'created_by', 'updated_by'
		];

    /**
     * Get the goods that owns the ProductWarranty
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'goods_id', 'id');
    }
    public function spear_goods(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'spear_id', 'id');
    }
}
