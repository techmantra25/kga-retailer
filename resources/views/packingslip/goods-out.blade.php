@extends('layouts.app')
@section('content')
@section('page', 'Goods Scan Out')
<section>
    <ul class="breadcrumb_menu">        
        <li>Order Management</li>
        <li><a href="{{ route('packingslip.list') }}">Packing Slip</a></li>
    </ul>
    <ul class="pincodeclass">
        <li>
            <a href="{{ route('sales-order.list') }}?{{$getQueryString}}">{{ $packingslip->sales_order->order_no }}</a>
        </li>
        <li>
            <a href="{{ route('packingslip.list') }}?search={{$packingslip->slipno}}">{{ $packingslip->slipno }}</a>
        </li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                             
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        
                    </div>
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control" placeholder="Search items..">
                    </div>
                    <div class="col-auto">
                        
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div> 
    <div class="row">
        <form id="myForm" action="{{ route('packingslip.save-scan-out',$packingslip_id) }}" method="POST">
            @csrf
            {{-- <input type="hidden" name="id" value="{{$id}}"> --}}
        <div class="col-sm-12">
            @php
                $i=1;
            @endphp
            @foreach ($data as $product_id => $barcodes)
            
            @php
                // dd($packingslip_id);
                $product_title = getSingleAttributeTable('products','id',$product_id,'title');
                $required_quantity = getPSProductQuantity($packingslip_id,$product_id); 
                
                
            @endphp
            {{-- <input type="hidden" name="product_id[]" value="{{$product_id}}">
            <input type="hidden" name="count[]" value="{{ $required_quantity }}"> --}}
            <div class="card shadow-sm">
                <div class="row">
                    <h3 class="pb-5">                       
                        <span class="ms-4">#{{$i}}:   {{$product_title}}</span>
                    </h3>
                    <h6>Required Quantity: <span>{{$required_quantity}}</span></h6>
                    @foreach ($barcodes as $barcode)
                    <div class="col-sm-3 border border-bottom-1 border-top-0 border-start-0 border-end-0 py-3">
                        
                        <div class="form-group">
                            
                            <input class="form-check-input data-barcode  data-check-{{$product_id}}" name="barcodes[]" value="{{$barcode->barcode_no}}" type="checkbox" value="1" id="barcode_no_{{$barcode->barcode_no}}" onclick="return false" @if(!empty($barcode->is_scanned)) checked @endif >
                            
                            <div style="max-width: 175px;" class="d-inline-block">
                                <span title="{{$barcode->barcode_no}}">
                                    {!! $barcode->code_html !!}
                                    <strong style="width: 100%; display: block; text-align: center; color: #000000;">{{$barcode->barcode_no}}</strong>
                                </span>
                                {{-- <img class="barcode_image ms-2" style="max-width:100%;" alt="Barcoded value {{$barcode->barcode_no}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&amp;text={{$barcode->barcode_no}}&amp;includetext&height=6"> --}}
                                <label for="barcode_no_{{$barcode->barcode_no}}" id="barcode_label_{{$barcode->barcode_no}}" class="text-center d-block pt-2 fw-bold label-barcode-{{$product_id}}">
                                    @if (!empty($barcode->is_scanned))
                                        SCANNED                                   
                                    @else
                                        YET TO SCAN
                                    @endif
                                </label>
                            </div>
                        </div>
                        
                    </div>
                    @endforeach                   
                    
                </div>
            </div>   
            @php
                $i++;
            @endphp   
            @endforeach                                             
        </div>  
        <div class="card shadow-sm">
            <div class="card-body text-end">
                <a href="{{route('packingslip.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Proceed </button>
            </div>
        </div> 
        </form>           
    </div>  
</section>
<script>
    var packingslip_id = "{{$packingslip_id}}";
    var total_barcodes = "{{$total_products}}";
    $(document).ready(function(){
        var total_scanned = $('input:checkbox.data-barcode:checked').length;
        console.log('total_barcodes:- '+total_barcodes+' total_scanned:-  '+total_scanned);
        if(total_scanned < total_barcodes){
            $('#submitBtn').prop('disabled', true);
            const interval = setInterval(() => {        
                getScannedImages(packingslip_id);
            }, 10000);
        }
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });

    
    function getScannedImages(packingslip_id){
        $.ajax({
            url: "{{ route('ajax.check-ps-scanned-boxes') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "packingslip_id": packingslip_id
            },
            success: function(data){
                
                var sucessData = data;
                console.log(sucessData)
                
                for(var i = 0; i < sucessData.length; i++) {
                    
                    $('#barcode_no_'+sucessData[i].barcode_no).attr('checked', 'checked');
                    $('#barcode_label_'+sucessData[i].barcode_no).html('SCANNED');
                    
                    var total_scanned = $('input:checkbox.data-barcode:checked').length;
                    $('#barcode_no_'+sucessData[i].barcode_no).removeClass('data-bulkable-'+sucessData[i].product_id);

                    if(total_barcodes == total_scanned){
                        $('#submitBtn').prop('disabled', false);
                    }

                }
               
            }
        });
    }
    

    
</script>  
@endsection 