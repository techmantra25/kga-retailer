<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ServicePartner;

class Payment extends Model
{
    protected $table = "payments";

    /**
     * Get the service_partner that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(ServicePartner::class, 'service_partner_id', 'id');
    }
	
	public function ho_sale(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ho_sale_id', 'id');
    }


}
