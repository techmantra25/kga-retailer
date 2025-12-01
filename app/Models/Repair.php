<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\RepairSpareRequisitionNote;
use App\Models\SpareReturn;
use App\Models\CloseRepair;

class Repair extends Model
{
    //

    /**
     * Get the dealer that owns the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dealer::class, 'dealer_id', 'id');
    }

    /**
     * Get the service_partner that owns the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ServicePartner::class, 'service_partner_id', 'id');
    }

    /**
     * Get the product that owns the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    /**
     * Get all of the spares for the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spares(): HasMany
    {
        return $this->hasMany(\App\Models\RepairSpare::class, 'repair_id', 'id');
    }

    /**
     * Get the close associated with the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function close(): HasOne
    {
        return $this->hasOne(CloseRepair::class, 'repair_id', 'id');
    }

    /**
     * Get the req_note associated with the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function req_note(): HasOne
    {
        return $this->hasOne(RepairSpareRequisitionNote::class, 'repair_id', 'id');
    }

    /**
     * Get all of the spare_returns for the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spare_returns(): HasMany
    {
        return $this->hasMany(SpareReturn::class, 'repair_id', 'id');
    }

    /**
     * Get the user associated with the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function is_not_returnd_spare(): HasMany
    {
        return $this->hasMany(SpareReturn::class, 'repair_id', 'id')->where('is_returned', 0);
    }

    /**
     * Get the master_close_repair that owns the Repair
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function master_close_repair(): BelongsTo
    {
        return $this->belongsTo(CloseRepair::class, 'repair_id', 'id');
    }
}
