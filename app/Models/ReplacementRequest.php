<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReplacementRequest extends Model{
	 protected $table = "replacement_requests";
	protected $fillable = [
			'crp_id', 'report_file', 'report_uploaded', 'report_required_till', 'approval1_by', 'approval1_at', 'approval2_by', 'approval2_at', 'status', 'remarks'
		];
	
	public function crp_data(){
	   return $this->belongsTo(\App\Models\CustomerPointService::class,'crp_id','id');
	}
	
	public function approval_1(){
	   return $this->belongsTo(\App\Models\User::class,'approval1_by','id');
	}
	
	public function approval_2(){
	   return $this->belongsTo(\App\Models\User::class,'approval2_by','id');
	}
}