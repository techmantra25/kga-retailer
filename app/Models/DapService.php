<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\DapRequestReceives;
use App\Models\DapServiceSpare;

class DapService extends Model
{
    protected $table = "dap_services";

    /**
     * Get the branch that owns the DapService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected $fillable = [
        'unique_id',
        'entry_date',
        'product_id',
        'branch_id',
        'customer_name',
        'mobile',
        'phone',
        'address',
        'alternate_no',
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
        'repeat_dap_id',
        'is_cancelled',
        'is_closed',
        'quotation_status',
        'send_otp',
        'send_otp_time',
        'otp_verified',
        'is_paid',
        'payment_method',
        'in_warranty',
        'in_motor_warranty',
        'is_spare_required',
        'repair_charge',
        'spare_charge',
        'spear_part_qty',
        'total_amount',
        'discount_amount',
        'final_amount',
        'total_service_charge',
        'is_dispatched_from_branch',
        'is_reached_service_centre',
        'assign_service_perter_id',
        'wearhouse_id',
        'vehicle_number',
        'created_by',
        'employee_id',
        'dispatch_by',
        'return_road_challan',
        'return_branch_id',
        'return_vehicle_number',
        'created_at',
        'updated_at'
    ];
    public function branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id', 'id');
    }
    public function return_branch(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Branch::class, 'return_branch_id', 'id');
    }
    public function sales_data(): BelongsTo
    {
        return $this->belongsTo(\App\Models\KgaSalesData::class, 'barcode', 'barcode');
    }
    /**
     * Get the product that owns the DapService
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Product::class, 'product_id', 'id');
    }

    /**
     * Get the receive associated with the DapService
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function receive(): HasOne
    {
        return $this->hasOne(DapRequestReceives::class, 'dap_service_id', 'id');
    }

    /**
     * Get all of the spares for the DapService
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function spares(): HasMany
    {
        return $this->hasMany(DapServiceSpare::class, 'dap_service_id', 'id');
    }
    public function EstimateSpares(): HasMany
    {
        return $this->hasMany(DapSpearPartOrder::class, 'dap_id', 'id');
    }
    public function FinalSpareParts(): HasMany
    {
        return $this->hasMany(DapSpearPartFinalOrder::class, 'dap_id', 'id');
    }
    public function DiscoundData(): HasOne
    {
        return $this->hasOne(DapDiscountRequest::class, 'dap_id', 'id')->where('status',1);
    }

    public function servicePartner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ServicePartner::class, 'assign_service_perter_id', 'id');
    }
    public function serviceCentre(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ServiceCentre::class, 'wearhouse_id', 'id');
    }
    public function callBookedEmployee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DealerEmployee::class, 'employee_id', 'id');
    }
    public function dapProductDispatchedEmployee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DealerEmployee::class, 'dispatch_by', 'id');
    }


    //payment data 

    public function paymentData(): HasOne
    {
        return $this->HasOne(\App\Models\DapServicePayment::class, 'dap_service_id', 'id');
    }
}
