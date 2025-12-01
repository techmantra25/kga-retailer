<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;
use App\Models\CustomerPointService;
use App\Models\Repair;
use App\Models\ServicePartner;

class SpareReturn extends Model
{
    protected $table = "spare_returns";

    /**
     * Get the spare that owns the SpareReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spare(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'spare_id', 'id');
    }

    /**
     * Get the goods that owns the SpareReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'goods_id', 'id');
    }

    /**
     * Get the repair that owns the SpareReturn
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class, 'repair_id', 'id');
    }
    public function crp(): BelongsTo
    {
        return $this->belongsTo(CustomerPointService::class, 'crp_id', 'id');
    }

    
    /**
     * Get the service_partner that owns the SpareInventory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(ServicePartner::class, 'service_partner_id', 'id');
    }
}
