<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class AmcSubscription extends Model
{
    //
    protected $table = "amc_subscription";

    public function SalesData(): BelongsTo
    {
        return $this->belongsTo(KgaSalesData::class, 'kga_sales_id', 'id');
    }
    public function AmcData(): BelongsTo
    {
        return $this->belongsTo(ProductAmc::class, 'amc_id', 'id');
    }
	
	public function servicePayments(): HasMany
	{
		return $this->hasMany(AmcServicePayment::class, 'kga_sales_id', 'kga_sales_id');
	}
	
	public function Sell_by() : BelongsTo 
	{
		return $this->belongsTo(User::class, 'sell_by', 'id');
	}

}
