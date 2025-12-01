<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Maintenance;

class CloseMaintenance extends Model
{
    protected $table = "close_maintenance";

    /**
     * Get the maintenance that owns the CloseMaintenance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id', 'id');
    }
}
