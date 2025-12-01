@extends('layouts.app')
@section('content')
@section('page', 'AMC SUBSCRIPTION DETAILS')
@section('small', '(Complete AMC Subscription Information)')

<section>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <a href="{{ route('amc.subscription-amc-data') }}" class="btn btn-outline-danger select-md">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card shadow">
            
            
            <div class="card-body">
                <div class="row">
                    <!-- Customer Details -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6>Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-4">Customer Name:</dt>
                                    <dd class="col-sm-8">{{ $subscription->SalesData->customer_name ?? 'N/A' }}</dd>

                                    <dt class="col-sm-4">Contact Numbers:</dt>
                                    <dd class="col-sm-8">
                                        {{ $subscription->SalesData->mobile ?? 'N/A' }} / 
                                        {{ $subscription->SalesData->phone ?? 'N/A' }}
                                    </dd>

                                    <dt class="col-sm-4">Address:</dt>
                                    <dd class="col-sm-8">
                                        {{ $subscription->SalesData->address ?? 'N/A' }}<br>
                                        {{ $subscription->SalesData->near_location ?? '' }}<br>
                                        PIN: {{ $subscription->SalesData->pincode ?? 'N/A' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- AMC Details -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6>AMC Information</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-4">AMC Number:</dt>
                                    <dd class="col-sm-8">{{ $subscription->amc_unique_number }}</dd>

                                    <dt class="col-sm-4">Purchase Date:</dt>
                                    <dd class="col-sm-8">{{ $subscription->purchase_date }}</dd>

                                    <dt class="col-sm-4">AMC Period:</dt>
                                    <dd class="col-sm-8">
                                        {{ $subscription->amc_start_date }} to 
                                        {{ $subscription->amc_end_date }}
                                    </dd>

                                    <dt class="col-sm-4">Plan Details:</dt>
                                    <dd class="col-sm-8">
										@php
											$plan_data = optional($subscription->AmcData)?->plan_id 
												? App\Models\AmcPlanType::find($subscription->AmcData->plan_id) 
												: null;
											$amc_plan = $subscription->AmcData->AmcPlanData ?? null;
										  $durationData = $amc_plan->AmcDurationData
											->where('duration', optional($subscription->AmcData)->duration)
											->first();
										@endphp
                                        {{ optional($plan_data)->name  }}<br>
										 Duration: {{ optional($subscription->AmcData)->duration }} days <br>
										{{ implode(' + ', $amc_plan->plan_asset_names) }} <br>
										@if($durationData)
										@php
											$cleaning_type = [];
											if($durationData->deep_cleaning) {
												$cleaning_type[] = $durationData->deep_cleaning . ' Deep Cleaning';
											}
											if($durationData->normal_cleaning) {
												$cleaning_type[] = $durationData->normal_cleaning . ' Normal Cleaning';
											}
										@endphp
										{{ implode(' + ', $cleaning_type) }}
										@else
											N/A
										@endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6>Product Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <dt>Product Name:</dt>
                                <dd>{{ $subscription->SalesData->item ?? 'N/A' }}</dd>
                            </div>
                            <div class="col-md-4">
                                <dt>Serial Number:</dt>
                                <dd>{{ $subscription->serial }}</dd>
                            </div>
                            <div class="col-md-4">
                                <dt>Barcode:</dt>
                                <dd>{{ $subscription->SalesData->barcode ?? 'N/A' }}</dd>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <dt>Bill Number:</dt>
                                <dd>{{ $subscription->SalesData->bill_no ?? 'N/A' }}</dd>
                            </div>
                            <div class="col-md-4">
                                <dt>Bill Date:</dt>
                                <dd>{{ $subscription->SalesData->bill_date ?? 'N/A' }}</dd>
                            </div>
                            <div class="col-md-4">
                                <dt>Branch:</dt>
                                <dd>{{ $subscription->SalesData->branch ?? 'N/A' }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Details -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6>Financial Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <dt>Actual Amount:</dt>
                                <dd>₹{{ number_format($subscription->actual_amount, 2) }}</dd>
                            </div>
                            <div class="col-md-3">
                                <dt>Discount Percentage:</dt>
                                <dd>{{ number_format($subscription->discount) }}%</dd>
                            </div>
                            <div class="col-md-3">
                                <dt>Final Amount:</dt>
                                <dd>₹{{ number_format($subscription->purchase_amount, 2) }}</dd>
                            </div>
                            <div class="col-md-3">
                                <dt>Sell By:</dt>
                                <dd>{{ $subscription->type }}</dd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection