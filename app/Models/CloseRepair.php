<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CloseRepair extends Model
{
    //
    protected $table = "close_repair";

    /**
     * Get the repair that owns the CloseRepair
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repair(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Repair::class, 'repair_id', 'id');
    }
}
