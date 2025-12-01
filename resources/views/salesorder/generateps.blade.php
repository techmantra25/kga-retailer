@extends('layouts.app')
@section('content')
@section('page', 'Generate Packing Slip')
<section>
    <ul class="breadcrumb_menu"> 
        <li>Service Partner Spare Order Management</li>
        <li><a href="{{ route('sales-order.list') }}?{{$getQueryString}}">Service Partner Spare Orders</a> </li>
        {{-- <li> {{ $data[0]->order->order_no }} </li> --}}
        <li>Generate Spare Packing Slip</li>
    </ul>
    
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                    {{ Session::forget('message') }}
                </div>
                @endif
            </div>            
            <div class="col-auto">

            </div>
        </div>
    </div>

    <div>
        <h5>Order Preview</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>                            
                    <th>Product</th>
                    <th>Required Quantity</th>
                    <th>Stock In Quantity</th>
                </tr>
            </thead>
            <tbody>
            @php
                $i=1;
                $isBtnDisabledArr = array();
            @endphp
            @forelse ($data as $item)
            @php
                $details = json_decode($item->details);  
                $stockinqty = getStockInventoryProduct($item->product_id);  
            @endphp
            
            <tr>
                <td>{{$i}}</td>
                <td>{{$item->product->title}}</td>
                <td>{{$item->quantity}}</td>
                <td>{{ $stockinqty }}</td>
                
            </tr>                                       
            
            @php
                $i++;
            @endphp
            @empty
                <tr>
                    <td>
                        No data found
                    </td>
                </tr>
            @endforelse
                
            </tbody>
        </table>
    </div>

    <form id="myForm" action="{{ route('sales-order.save-packing-slip', [$idStr,$getQueryString]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="sales_orders_id" value="{{ Crypt::decrypt($idStr) }}">
        
        @php
            
            $slipno = 'PS'.genAutoIncreNoYearWiseOrder(4,'packingslips',date('Y'),date('m'));
        @endphp        
        <input type="hidden" name="slipno" class="form-control" value="{{ $slipno }}" id="">
        <div class="row">
           
            

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="form-group">
                            <label for="">Upload CSV</label>
                            <input type="file" name="csv" class="form-control" id="" accept=".csv">
                            @error('csv') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div> 
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-end">
                            <a href="{{ url('/samplecsv/barcodes/sample-barcode.csv') }}" class="btn btn-outline-primary">Download Sample CSV</a>
                            <a href="{{route('sales-order.list')}}?{{$getQueryString}}" class="btn  btn-outline-danger">Back</a>
                            <button type="submit" id="submitBtn"  class="btn  btn-success" >Submit & Goods Out </button>
                        </div>
                    </div> 
                </div>
            </div>
        </div>        
    </form>    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    });   
    $('#type').on('change', function(){
        $('#searchForm').submit();
    });
    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });
    
    $(".quantity").on('keypress keyup keydown', function (evt) {
        evt.preventDefault();
    });
   
   
</script>  
@endsection 