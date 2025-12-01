<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DapServicePayment extends Model
{
    //

    protected $table = "dap_service_payments";

    /**
     * Get the dap_service that owns the DapServicePayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dap_request(): BelongsTo
    {
        return $this->belongsTo(DapService::class, 'dap_service_id', 'id');
    }
}
