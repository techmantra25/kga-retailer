<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product;

class DapServiceSpare extends Model
{
    protected $table = "dap_service_spares";

    /**
     * Get the spares that owns the DapServiceSpare
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function spares(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'spare_id', 'id');
    }
}
