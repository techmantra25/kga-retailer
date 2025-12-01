<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPointServiceLog extends Model
{
    protected $table = 'customer_point_service_logs';
    public function Service(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CustomerPointService::class, 'service_id', 'id');
    }
}
