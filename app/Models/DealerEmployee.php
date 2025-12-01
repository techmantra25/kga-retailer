<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DealerEmployee extends Model
{
	protected $table = 'dealer_employee';


	public function branchData()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id', 'id');
    }
}
