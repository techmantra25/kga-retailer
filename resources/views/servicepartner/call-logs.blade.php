@extends('layouts.app')
@section('content')
@section('page', 'Call Logs / '.$person_name.' | '.$company_name)
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
               <ul>
                    <li @if($type == 'installation' ) class="active" @endif><a href="{{route('service-partner.call-logs',[$service_partner_idStr,'installation'])}}?{{ Request::getQueryString()}}">Installation </a></li>
                    <li @if($type == 'repair' ) class="active" @endif><a href="{{route('service-partner.call-logs',[$service_partner_idStr,'repair'])}}?{{ Request::getQueryString()}}">Repair </a></li>
               </ul>
            </div>
            <div class="col-auto">
                              
            </div>
            <div class="col-auto">
                         
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="">From</label>
                        <input type="date" name="from_date" class="form-control" id="" value="{{ $from_date }}" max="{{ $to_date }}">
                    </div>
                    <div class="col-auto">
                        <label for="">To</label>
                        <input type="date" name="to_date" value="{{ $to_date }}" class="form-control" id="" max="{{ date('Y-m-d') }}" min="{{ $from_date }}">
                    </div>
                    <div class="col-auto align-self-end">
                        <input type="submit" value="Search" class="btn btn-success">
                    </div>
                    <div class="col-auto align-self-end">
                        <a href="{{ route('service-partner.list') }}" class="btn btn-danger">Back</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif
            </div>
            
            <div class="col-auto">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    @if ($type == 'installation')
    <table class="table" id="installationTable">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="primary_column">Date</th>
                <th>Bill No</th>
                <th>Pincode</th>
                <th>Product Details</th>
                <th>Customer Details</th>
                <th>Charge</th>
            </tr>
        </thead>
        <tbody>       
        @php
            if(empty(Request::get('page')) || Request::get('page') == 1){
                $i=1;
            } else {
                $i = (((Request::get('page')-1)*$paginate)+1);
            } 
        @endphp 
        @forelse ($data as $item)
            <tr>
                <td data-colname="" class="sr_no">
                    {{$i}}
                </td>
                <td data-colname="Date" class="primary_column">
                    {{ date('d/m/Y', strtotime($item->created_at)) }} <button type="button" class="toggle_table"></button>
                </td>
                <td data-colname="Bill No">
                    {{ $item->bill_no }}
                </td>
                <td data-colname="Pincode">
                    {{ $item->pincode }}
                </td>
                <td data-colname="Product Details">
                    <p class="small text-muted mb-1">
                        <span>Serial No: <strong>{{$item->product_sl_no}}</strong></span> <br/>
                        <span>Name: <strong>{{$item->product_name}}</strong></span> <br/>
                    </p>
                </td>
                <td data-colname="Customer Details">
                    <p class="small text-muted mb-1">
                        <span>Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                        <span>Mobile: <strong>{{$item->mobile_no}}</strong></span> <br/>
                    </p>
                </td>
                <td data-colname="Charge">
                    Rs. {{ number_format((float)$item->service_charge, 2, '.', '') }}
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
        <tr>
            <td colspan="7" style="text-align: center;">
                No data found
            </td>
        </tr>
        @endforelse
            
        </tbody>
    </table>
    @elseif ($type == 'repair')
    <table class="table" id="repairTable">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="primary_column">Date</th>
                <th>Bill No</th>
                <th>Pincode</th>
                <th>Product Details</th>
                <th>Customer Details</th>
                <th>Charge</th>
            </tr>
        </thead>
        <tbody>       
        @php
            if(empty(Request::get('page')) || Request::get('page') == 1){
                $i=1;
            } else {
                $i = (((Request::get('page')-1)*$paginate)+1);
            } 
        @endphp 
        @forelse ($data as $item)
            <tr>
                <td data-colname="" class="sr_no">
                    {{$i}}
                </td>
                <td data-colname="Date" class="primary_column">
                    {{ date('d/m/Y', strtotime($item->created_at)) }} <button type="button" class="toggle_table"></button>
                </td>
                <td data-colname="Bill No">
                    {{ $item->bill_no }}
                </td>
                <td data-colname="Pincode">
                    {{ $item->pincode }}
                </td>
                <td data-colname="Product Details">
                    <p class="small text-muted mb-1">
                        <span>Serial No: <strong>{{$item->product_sl_no}}</strong></span> <br/>
                        <span>Name: <strong>{{$item->product_name}}</strong></span> <br/>
                    </p>
                </td>
                <td data-colname="Customer Details">
                    <p class="small text-muted mb-1">
                        <span>Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                        <span>Mobile: <strong>{{$item->customer_phone}}</strong></span> <br/>
                    </p>
                </td>
                <td data-colname="Charge">
                    Rs. {{ number_format((float)$item->service_charge, 2, '.', '') }}
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
        <tr>
            <td colspan="7" style="text-align: center;">
                No data found
            </td>
        </tr>
        @endforelse
            
        </tbody>
    </table>
    @endif
    
    {{$data->links()}}
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 