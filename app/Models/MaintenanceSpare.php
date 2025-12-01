<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Maintenance;
use App\Models\Product;

class MaintenanceSpare extends Model
{
    protected $table = "maintenance_spares";

    /**
     * Get the maintenance that owns the MaintenanceSpare
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id', 'id');
    }

    /**
     * Get the spares that owns the MaintenanceSpare
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spares(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
