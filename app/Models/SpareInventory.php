<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\SpareReturn;
use App\Models\ServicePartner;

class SpareInventory extends Model
{
    //
    protected $table = "spare_inventory";

    /**
     * Get the spare_return that owns the SpareInventory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spare_return(): BelongsTo
    {
        return $this->belongsTo(SpareReturn::class, 'spare_return_id', 'id');
    }

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
     * Get the service_partner that owns the SpareInventory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(ServicePartner::class, 'service_partner_id', 'id');
    }

}
