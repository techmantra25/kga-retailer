<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerPointServicePartnerPincode extends Model
{
    // use HasFactory;

    protected $table = "customer_point_service_partner_pincodes";

    /**
     * Get the service_partner that owns the CustomerPointServicePartnerPincode
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ServicePartner::class, 'service_partner_id', 'id');
    }
}
