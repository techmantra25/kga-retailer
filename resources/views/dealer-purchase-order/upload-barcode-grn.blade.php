@extends('layouts.app')
@section('content')
@section('page', 'Generate GRN')
<section>
    <ul class="breadcrumb_menu"> 
        <li>Dealer Purchase Order Management</li>
        <li><a href="{{ route('dealer-purchase-order.show', Crypt::encrypt($id)) }}">{{$order->order_no}}</a></li>        
        <li>Generate GRN</li>
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
        <p>Dealer: {{ $order->dealer->name }}</p>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>                            
                    <th>Product</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
            @php
                $i=1;
            @endphp
            @forelse ($data as $item)            
            <tr>
                <td>{{$i}}</td>
                <td>{{$item->product->title}}</td>
                <td>{{ $item->quantity }} pcs</td>
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
    <form id="myForm" action="{{ route('dealer-purchase-order.save-grn') }}" method="POST" enctype="multipart/form-data">
        @csrf     
        <input type="hidden" name="id" value="{{$id}}">
        <input type="hidden" name="dealer_id" value="{{$order->dealer_id}}">        
        <div class="row">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="form-group">
                            <label for="">Upload Barcode CSV</label>
                            <input type="file" name="csv" class="form-control" id="" accept=".csv">
                            @error('csv') <p class="small text-danger">{{ $message }}</p> @enderror
                        </div>
                    </div> 
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-end">
                            <a href="{{ url('/samplecsv/barcodes/sample-barcode.csv') }}" class="btn btn-outline-primary">Download Sample CSV</a>
                            <a href="{{route('dealer-purchase-order.list')}}?{{$getQueryString}}" class="btn  btn-outline-danger">Back</a>
                            <button type="submit" id="submitBtn"  class="btn  btn-success" >Submit & Goods In </button>
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