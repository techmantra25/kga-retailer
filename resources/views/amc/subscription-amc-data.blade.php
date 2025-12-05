@extends('layouts.app')
@section('content')
@section('page', 'AMC SUBSCRIPTION DATA')
@section('small', '(AMC Subscriptions Data With Customer Details)')

<section>
	@php
	 $authId = auth()->user()->role_id;
	@endphp
	@if($authId != 4)
	<div class="col mb-3">
         <a href="{{ route('amc.ho-sale') }}" class="btn btn-outline-danger select-md">Back to Ho-sale</a>
    </div>
	@endif
    <div class="col mb-2 mb-sm-0">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            <strong>{{ Session::get('message') }}</strong>
        </div>
        @endif
        @if (Session::has('error'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('error') }}
        </div>
        @endif
    </div>
    <form action="" id="searchForm">
        <div class="search__filter">
            <div class="row align-items-center justify-content-between">
                <div class="col">
                    <p class="mb-0">Total {{$totalResult}} Records</p>
                </div>
                
				<div class="col">
					<input type="search" name="search" value="{{$search}}" class="form-control select-md" 
							placeholder="Search by amc unique number,serial,customername,mobile,phone,branch etc..">         
                </div>
                <div class="col-2" id="search_date">
                    <input type="date" value="{{$date}}" class="form-control select-md" name="date" placeholder="Search by date" />
                </div>
				<div class="col-2">
					<a href="{{ route('amc.subscription.csv',['search' => $search , 'date' => $date] )}}" class="btn btn-outline-success select-md">CSV</a>
				</div> 
                <div class="col-auto">
                    <a href="{{ route('amc.subscription-amc-data') }}" class="btn btn-outline-warning select-md">Reset Page</a>   
                </div>
            </div>
        </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="sr_no">KGA-Sales-Id</th>
                <th class="primary_column">Amc Unique Number</th>
                <th>Serial No</th>
                <th>Customer Details</th>
                <th>Amc Details</th>
				<th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($data as $item)
        @php
        $plan_data = App\Models\AmcPlanType::find(optional($item->AmcData)->plan_id);
        @endphp
        <tr>
            <td class="sr_no">{{$loop->iteration}}</td>
            <td class="sr_no">{{$item->kga_sales_id}}</td>
            <td class="sr_no">{{$item->amc_unique_number}}</td>
            <td class="sr_no">{{$item->serial}}</td>
            <td>
                <p class="small text-muted mb-1">
                    <span>Name: <strong>{{ $item->SalesData?$item->SalesData->customer_name:"" }}</strong></span> <br/>
                    <span>Mobile: <strong>{{ $item->SalesData?$item->SalesData->mobile:"" }}</strong></span> <br/>
                    <span>Phone: <strong>{{ $item->SalesData?$item->SalesData->phone:"" }}</strong></span> <br/>
                    <span style="word-break:break-word; white-space:normal;">Address: <strong>{{ $item->SalesData?$item->SalesData->address:"" }}</strong></span> <br/>
                    <span>Pin: <strong>{{ $item->SalesData?$item->SalesData->pincode:"" }}</strong></span> <br/>
                    <span>Product Name: <strong>{{ $item->SalesData?$item->SalesData->item:"" }}</strong></span> <br/>
                    <span>Bill No: <strong>{{ $item->SalesData?$item->SalesData->bill_no:"" }}</strong></span> <br/>
                    <span>Bill Date: <strong>{{ $item->SalesData?$item->SalesData->bill_date:"" }}</strong></span> <br/>

                </p>
            </td>
            <td>
                <p class="small text-muted mb-1">
                    <span>Purchase Date: <strong>{{ $item->purchase_date }}</strong></span> <br/>
                    <span>Actual Amount: <strong>{{ $item->actual_amount }}</strong></span> <br/>
                    <span>Discount: <strong>{{ $item->discount }}</strong></span> <br/>
                    <span>Purchase Amount: <strong>{{ $item->purchase_amount }}</strong></span><br/>
                    <span>Plan Name: <strong>{{ optional($plan_data)->name ?? 'N/A' }}</strong></span><br/>
                    <span>Plan Duration: <strong>{{ $item->AmcData?$item->AmcData->duration:"" }} days</strong></span><br/>
                    <span>Amc Start Date: <strong>{{ $item->amc_start_date }}</strong></span><br/>
                    <span>Amc End Date: <strong>{{ $item->amc_end_date }}</strong></span><br/>
                </p>
            </td>
			<td>
				<div class="gap-2">

                    <a href="{{ route('amc.subscription-amc-data-view', $item->id) }}" 
                    class="btn btn-sm btn-outline-primary select-md"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="View Details">
                        View
                    </a>

                    <a href="{{ route('amc.subscription-amc-data-pdf', $item->id) }}" 
                    class="btn btn-sm btn-outline-success select-md"
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Download Invoice">
                        Download
                    </a>

                    <a href="{{ route('amc.send-whatsapp-amc-invoice', $item->id) }}" 
                    class="btn btn-sm btn-outline-success select-md"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Send Invoice on WhatsApp"
                    onclick="return confirm('Are you sure you want to send invoice on WhatsApp?');">
                         WhatsApp
                    </a>

                </div>
			</td>
        </tr>

        @empty
            <tr>
                <td colspan="6" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    {{$data->links()}}

    
</section>
<script>
      $('#search_date').on('change', function(){
        $('#searchForm').submit();
    })
    
</script>  
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

@endsection 