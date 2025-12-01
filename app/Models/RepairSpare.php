<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairSpare extends Model
{
    //

    protected $table = "repair_spares";

    /**
     * Get the repair that owns the RepairSpare
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repair(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Repair::class, 'repair_id', 'id');
    }

    /**
     * Get the spares that owns the RepairSpare
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spares(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }
}
