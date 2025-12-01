<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class BeforeAmcSubscription extends Model
{
    //
    protected $table = "before_amc_subscription";

    public function AmcLinkData(): BelongsTo
    {
        return $this->belongsTo(AmcPaymentLink::class, 'amc_unique_number', 'amc_unique_number');
    }
	
	public function kgaSaleData()
	{
		return $this->belongsTo(KgaSalesData::class, 'kga_sales_id');
	}
	
	public function productAmc()
	{
	   return $this->belongsTo(ProductAmc::class, 'amc_id');
	}

}
