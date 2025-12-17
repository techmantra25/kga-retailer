<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Product;

class CustomerPointService extends Model
{
    protected $table = "customer_point_services";

    /**
     * Get the product that owns the CustomerAmcRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

     protected $fillable = [
        'unique_id',
        'entry_date',
        'product_id',
        'branch_id',
        'dealer_id',
        'price',
        'customer_name',
        'mobile',
        'phone',
        'alternate_no',
        'address',
        'pincode',
        'issue',
        'item',
        'class_name',
        'bill_date',
        'bill_no',
        'barcode',
        'code_html',
        'code_base64_img',
        'serial',
        'repeat_call',
        'repeat_crp_id',
        'is_cancelled',
        'is_closed',
        'is_paid',
        'payment_method',
        'in_warranty',
        'is_spare_required',
        'repair_charge',
        'spare_charge',
        'total_service_charge',
        'assign_service_perter_id',
        'wearhouse_id',
        'vehicle_number',
        'created_by',
        'employee_id',
        'dispatch_by',
        'created_at',
        'updated_at'
    ];



    public function paymentData(): HasOne
    {
        return $this->HasOne(\App\Models\CrpServicePayment::class, 'crp_service_id', 'id');
    }
    public function servicePartner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ServicePartner::class, 'assign_service_perter_id', 'id');
    }
    public function SpareData(): HasMany
    {
        return $this->hasMany(\App\Models\CustomerPointServiceSpare::class, 'crp_id', 'id');
    }
    public function SpareFinalData(): HasMany
    {
        return $this->hasMany(\App\Models\CRPFinalSpare::class, 'crp_id', 'id');
    }

    public function replacementRequest(): HasOne
    {
        return $this->HasOne(\App\Models\ReplacementRequest::class, 'crp_id', 'id');
    }
    
  
}
