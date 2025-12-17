<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReplacementDispatch extends Model{
	protected $table = "replacement_dispatch";
	protected $fillable = [
			'replacement_request_id','courier_name','tracking_no','shipped_at'
		];
	
	public function replacement_request_data(){
	  return $this->belongsTo(\App\Models\ReplacementRequest::class,'replacement_request_id','id');
	}
	
	
}