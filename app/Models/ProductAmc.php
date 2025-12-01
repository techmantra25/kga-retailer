<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class ProductAmc extends Model
{
    protected $table = "product_amcs";

    /**
     * Get the product that owns the ProductAmc
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function productData(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function AmcDurationData(): BelongsTo
    {
        return $this->belongsTo(AmcDuration::class, 'duration_id', 'id');
    }
    public function AmcPlanData(): BelongsTo
    {
        return $this->belongsTo(AmcPlanType::class, 'plan_id', 'id');
    }
    
}
