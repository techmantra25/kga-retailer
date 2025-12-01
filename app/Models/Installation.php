<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\CloseInstallation;
use App\Models\Product;

class Installation extends Model
{
    // use HasFactory;

    protected $table = "installations";

    /**
     * Get the service_partner that owns the Installation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ServicePartner::class, 'service_partner_id', 'id');
    }


    /**
     * Get the dealer that owns the Installation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Dealer::class, 'dealer_id', 'id');
    }

    /**
     * Get the close associated with the Installation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function close(): HasOne
    {
        return $this->hasOne(CloseInstallation::class, 'installation_id', 'id');
    }

    /**
     * Get the product that owns the Installation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
