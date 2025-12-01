<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class AmcPlanType extends Model
{
    protected $table = "amc_plan_type";


    public function AmcDurationData(): HasMany
    {
        return $this->hasMany(AmcDuration::class, 'amc_id', 'id');
    }
	
	  public function PlanAssets(): HasMany
    {
        return $this->hasMany(PlanAsset::class, 'plan_asset_id', 'id');
    }
	
	public function getPlanAssetNamesAttribute()
	{
		$ids = explode(',', $this->plan_asset_id);
		return \App\Models\PlanAsset::whereIn('id', $ids)->orderBy('name','asc')->pluck('name')->toArray();
	}

}
