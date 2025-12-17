<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Installation;
use App\Models\Repair;
use App\Models\Maintenance;
use App\Models\CreditNote;

class Ledger extends Model
{
    //
    protected $table = "ledgers";
    protected $fillable = [
        'type', 'amount', 'entry_date', 'user_type', 'user_id', 'service_partner_id', 'dealer_id', 'payment_id', 'installation_id', 'repair_id', 'dap_id', 'crp_id', 'amc_id', 'kga_sales_id', 'maintenance_id', 'credit_note_id', 'purpose', 'transaction_id'
    ];
    /**
     * Get the installation that owns the Ledger
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function installation(): BelongsTo
    {
        return $this->belongsTo(Installation::class, 'installation_id', 'id');
    }

    /**
     * Get the repair that owns the Ledger
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class, 'repair_id', 'id');
    }

    /**
     * Get the maintenance that owns the Ledger
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function maintenance(): BelongsTo
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id', 'id');
    }

    /**
     * Get the credit_note that owns the Ledger
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function credit_note(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class, 'credit_note_id', 'id');
    }
	
	public function amc_subscription()
	{
		return $this->belongsTo(AmcSubscription::class, 'amc_id');
	}
}
