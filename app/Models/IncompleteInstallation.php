<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncompleteInstallation extends Model
{
    //
    protected $table = "incomplete_installation";

    /**
     * Get the product that owns the IncompleteInstallation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    /**
     * Get the installation that owns the IncompleteInstallation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function installation(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Installation::class, 'installation_id', 'id');
    }

    /**
     * Get the service_partner that owns the IncompleteInstallation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ServicePartner::class, 'service_partner_id', 'id');
    }
}
