<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pincode extends Model
{
    // use HasFactory;

    protected $table = "pincodes";

    /**
     * Get the servicepartners associated with the Pincode
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function servicepartners(): HasOne
    {
        return $this->hasOne(\App\Models\ServicePartnerPincode::class, 'pincode_id', 'id');
    }
}
