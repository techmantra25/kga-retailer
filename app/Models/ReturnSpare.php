<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnSpare extends Model
{
    //
    protected $table = "return_spare";

    /**
     * Get the service_partner that owns the ReturnSpareItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ServicePartner::class, 'service_partner_id', 'id');
    }
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dealer::class, 'dealer_id', 'id');
    }

    /**
     * Get all of the items for the ReturnSpare
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(\App\Models\ReturnSpareItem::class, 'return_spare_id', 'id');
    }
}
