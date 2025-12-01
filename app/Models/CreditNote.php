<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Installation;
use App\Models\Repair;
use App\Models\ServicePartner;
use App\User;

class CreditNote extends Model
{
    //

    protected $table = "credit_note";

    /**
     * Get the installation that owns the CreditNote
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function installation(): BelongsTo
    {
        return $this->belongsTo(Installation::class, 'installation_id', 'id');
    }

    /**
     * Get the repair that owns the CreditNote
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class, 'repair_id', 'id');
    }

    /**
     * Get the created that owns the CreditNote
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get the service_partner that owns the CreditNote
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function service_partner(): BelongsTo
    {
        return $this->belongsTo(ServicePartner::class, 'service_partner_id', 'id');
    }
	
	public function ho_sale(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ho_sale_id');
    }
}
