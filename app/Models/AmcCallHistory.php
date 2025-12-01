<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AmcCallHistory extends Model
{
    protected $table = "amc_call_history";


    public function KgaSaleData(): BelongsTo
    {
        return $this->belongsTo(KgaSalesData::class, 'kga_sale_id', 'id');
    }
    public function userData(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auth_id', 'id');
    }
}
