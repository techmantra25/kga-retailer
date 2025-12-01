<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Product;
use App\Models\ServicePartner;
use App\Models\Dealer;
use App\Models\MaintenanceSpare;

class Maintenance extends Model
{
    protected $table = "maintenances";

    /**
     * Get the product that owns the Maintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Get the service_partner that owns the Maintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(ServicePartner::class, 'service_partner_id', 'id');
    }

    /**
     * Get the dealer that owns the Maintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'dealer_id', 'id');
    }

    /**
     * Get all of the spares for the Maintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spares1(): HasMany
    {
        return $this->hasMany(MaintenanceSpare::class, 'maintenance_id', 'id');
    }
}
