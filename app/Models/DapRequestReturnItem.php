<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DapRequestReturn;
use App\Models\DapService;

class DapRequestReturnItem extends Model
{
    protected $table = "dap_request_return_items";

    /**
     * Get the return that owns the DapRequestReturnItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function return(): BelongsTo
    {
        return $this->belongsTo(DapRequestReturn::class, 'dap_request_return_id', 'id');
    }

    /**
     * Get the dap_request that owns the DapRequestReturnItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dap_request(): BelongsTo
    {
        return $this->belongsTo(DapService::class, 'dap_service_id', 'id');
    }
}
