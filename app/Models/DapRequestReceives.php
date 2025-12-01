<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DapService;

class DapRequestReceives extends Model
{
    protected $table = "dap_request_receives";

    /**
     * Get the dap_request that owns the DapRequestReceives
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dap_request(): BelongsTo
    {
        return $this->belongsTo(DapService::class, 'dap_service_id', 'id');
    }

    /**
     * Get the dap_request_receive_drops that owns the DapRequestReceives
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dap_request_receive_drops(): BelongsTo
    {
        return $this->belongsTo(DapRequestReceiveDrop::class, 'dap_request_receive_drop_id', 'id');
    }
}
