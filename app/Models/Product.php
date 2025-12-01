<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SpareGoods;

class Product extends Model
{
    // use HasFactory;

    /**
     * Get the category that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class, 'cat_id', 'id');
    }

    /**
     * Get the subcategory that owns the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class, 'subcat_id', 'id');
    }

    /**
     * Get all of the spare_goods for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spare_goods(): HasMany
    {
        return $this->hasMany(SpareGoods::class, 'spare_id', 'id');
    }

    /**
     * Get all of the goods_spares for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods_spares(): HasMany
    {
        return $this->hasMany(SpareGoods::class, 'goods_id', 'id');
    }

    /**
     * Get all of the amc for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function amc(): HasMany
    {
        return $this->hasMany(ProductAmc::class, 'product_id', 'id');
    }


    public function productWarranty(): HasMany
    {
        return $this->hasMany(ProductWarranty::class, 'id', 'product_id');
    }
}
