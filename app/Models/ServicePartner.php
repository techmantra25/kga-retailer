<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;


class ServicePartner extends Authenticatable
{
    // use HasFactory;

    protected $guard = "servicepartner";

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get all of the comments for the ServicePartner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pincodes(): HasMany
    {
        return $this->hasMany(\App\Models\ServicePartnerPincode::class);
    }
    public function customerpointpincodes(): HasMany
    {
        return $this->hasMany(\App\Models\CustomerPointServicePartnerPincode::class);
    }

    /**
     * Get all of the products for the ServicePartner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(\App\Models\ServicePartnerCharge::class);
    }

}
