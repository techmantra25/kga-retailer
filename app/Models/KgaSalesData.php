<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class KgaSalesData extends Model
{
    //
    protected $table = "kga_sales_data";

    /**
     * Get the product that owns the KgaSalesData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    /**
     * Get the category that owns the KgaSalesData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class, 'cat_id', 'id');
    }
    public function dapCategory(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class, 'cat_id', 'id')->select('id', 'name')->where('name', 'LIKE', 'DAP%');;
    }
    public function goodsWarranty(): BelongsTo
    {
        return $this->belongsTo(\App\Models\GoodsWarranty::class, 'product_id', 'goods_id');
    }
    public function productWarranty(): HasMany
    {
        return $this->hasMany(\App\Models\ProductWarranty::class, 'goods_id','product_id')->where('dealer_type', 'khosla')->where('warranty_type','comprehensive');
    }
    public function AmcSubscription(): HasMany
    {
        return $this->hasMany(\App\Models\AmcSubscription::class, 'kga_sales_id','id');
    }
    public function Before_Amc_Subscription(): HasMany
    {
        return $this->hasMany(\App\Models\BeforeAmcSubscription::class, 'kga_sales_id','id')->orderBy('id','DESC');
    }
    // public function CRPgoodsWarranty(): BelongsTo
    // {
    //     return $this->belongsTo(\App\Models\GoodsWarranty::class, 'product_id', 'goods_id')-;
    // }
    public function CallHistoryData(): HasMany
    {
        return $this->hasMany(\App\Models\AmcCallHistory::class, 'kga_sale_id', 'id');
    }
	
	public function pendingPayments()
	{
		return $this->hasMany(BeforeAmcSubscription::class, 'kga_sales_id')
					->where('status', 0);
	}
	
	public function latestCallHistory()
	{
		return $this->hasOne(AmcCallHistory::class, 'kga_sale_id')->latest();
	}

}
