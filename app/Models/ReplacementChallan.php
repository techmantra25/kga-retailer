<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReplacementChallan extends Model{
	 protected $table = "replacement_challans";
	protected $fillable = [
			'replacement_request_id', 'challan_no', 'customer_details', 'product_details'
		];
	
	public function replacement_request_data(){
	  return $this->belongsTo(\App\Models\ReplacementRequest::class,'replacement_request_id','id');
	}
	
	
}