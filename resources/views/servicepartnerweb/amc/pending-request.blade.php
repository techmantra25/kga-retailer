@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Pending List Discount Request For AMC')
<section>
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

                </div>
                <div class="col-2">
                   <input type="text" name="search" value="{{$search}}"  class="form-control select-md"/>
                </div>
                <div class="col-auto">
                    <a href="{{ route('servicepartnerweb.amc.peding-discount-request-list')}}" class="btn btn-outline-warning select-md">Reset</a>   
                </div>
    
            </div>
        </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="sr_no">KGA-Sales-Id</th>
                <th class="sr_no">AMC Unique No.</th>
                <th class="sr_no">Package Details</th>
                <th class="primary_column">Actual Amount</th>
                <th>Approved Discount %</th>
                <th>Discount Request %</th>
                <th>Purchase Amount</th>
                <th>Status</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>

        @forelse($data as $item)
            @php
                    $amc_data = App\Models\ProductAmc::find($item->amc_id);  
                    $plan_data = App\Models\AmcPlanType::find($amc_data->plan_id);  
            @endphp        
            <tr>  
                <td class="sr_no">{{$loop->iteration}}</td>
                <td class="sr_no">{{$item->kga_sales_id}}</td>
                <td class="sr_no">{{$item->amc_unique_number}}</td>
                <td>
                    <p class="small text-muted mb-1">
                        <span>Plan Name: <strong>{{$plan_data->name}}</strong></span> <br/>
                        <span>Duration: <strong>{{$amc_data->duration}} Days</strong></span> <br/>
                        
                    </p>
                </td>
                <td class="sr_no">{{$item->actual_amount}}</td>
                <td class="sr_no">{{$item->discount}}</td>
                <td class="sr_no">{{$item->discount_request}}</td>
                <td class="sr_no">{{$item->purchase_amount}}</td>
                @if($item->status === 2)
                <td class="sr_no"><span class="bg-warning">Pending</span></td>
                @elseif($item->status === 1)
                <td class="sr_no"><span class="bg-success">Payment Success</span></td>
                @elseif($item->status === 3)
                <td class="sr_no"><span class="bg-success">Approved & Waiting for payment</span></td>
                @else
                @endif

                @if($item->status === 2)
                <td class="sr_no"><button class="btn btn-outline-secondary selected-sm">Pending</span></td>
                @elseif($item->status === 3)
                <td class="sr_no">
                    <form method="post" action="{{ route('servicepartnerweb.amc.after-discount-send-payment-link') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary select-md">Send Payment Link</button>
                        <input type="hidden" name="kga_sales_id" value="{{$item->kga_sales_id}}">
                    </form>
                </td>
                @else
                @endif
            </tr>

        @empty
            <tr>
                <td colspan="12" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse

    </table>
    {{$data->links()}}
   
</section>
<script>
    
</script>  
@endsection 