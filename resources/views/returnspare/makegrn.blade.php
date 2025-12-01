@extends('layouts.app')
@section('content')
@section('page', 'Generate GRN')
<section>
    <ul class="breadcrumb_menu">
        <li>Returned Spare Management</li>
        <li><a href="{{ route('return-spares.list') }}?{{$getQueryString}}">Returned Spares</a> </li>
        <li>{{$transaction_id}}</li>
    </ul>
    @if (empty($goods_in_type))
    <div class="row">
        <form action="" method="GET">  
        <div class="row">
            <div class="col-md-4">
                    <div class="form-group d-flex align-items-center" style="height: 30px;">
                        <label for="">Goods In With  </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input choose-type" type="radio"  name="goods_in_type" id="scan" value="scan">
                            <label class="form-check-label" for="scan">Scan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input choose-type" type="radio" name="goods_in_type" id="bulk" value="bulk">
                            <label class="form-check-label" for="bulk">Bulk</label>
                        </div>
                    </div>
                
            </div>
            <div class="col-md-4">
                <div class="d-flex flex-row">
                    <a href="{{ route('return-spares.list') }}" class="btn btn-danger select-md">Back</a>
                    <button type="submit" class="btn btn-success select-md" id="nextBtn">Next</button>
                </div>
            </div>
        </div>          
       
        </form>
    </div>
    @endif
    
    @if (!empty($goods_in_type))
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if ($goods_in_type == 'bulk')
                <span class="badge bg-success">
                    BULK GOODS IN
                </span> 
                @else
                <span class="badge bg-success">
                    SCAN GOODS IN
                </span> 
                @endif
                    
            </div>
            <div class="col-auto">
                    
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        {{-- <a href="{{ route('purchase-order.download', Crypt::encrypt($id)) }}" class="btn btn-outline-primary btn-sm">Download Barcodes </a> --}}
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
    @if($errors->any())
        {!! implode('', $errors->all('<p class="small text-danger">:message</p>')) !!}
    @endif
    <div class="row">
        <form id="myForm" action="{{ route('return-spares.generate-grn') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$id}}">
        <div class="col-sm-12">
            @php
                $i=1;
            @endphp
            @foreach ($data as $product_id => $barcodes)
            <input type="hidden" name="product_id[]" value="{{$product_id}}">
            <input type="hidden" name="count[]" value="{{ count($data[$product_id]) }}">
            @php
                $product_title = getSingleAttributeTable('products','id',$product_id,'title');
                $isBulkScannedReturnSpare = isBulkScannedReturnSpare($id,$product_id);
                // dd($data[$product_id]);
                
            @endphp
            <div class="card shadow-sm">
                <div class="row">
                    <h3 class="pb-5">  
                        @if($goods_in_type == 'bulk')
                        <label class="form-check-label check-bulk" for="bulk_scan_{{$product_id}}">
                            <input class="form-check-input data-check-{{$product_id}}" name=""  type="checkbox" value="1" id="bulk_scan_{{$product_id}}" 
                            onchange="bulkScan({{$product_id}});" 
                            @if($isBulkScannedReturnSpare) checked  @endif 
                            style="height: 15px; width: 15px; border-radius: 3px;">
                            Bulk In
                        </label>
                        @endif
                        <span class="ms-4">#{{$i}}:   {{$product_title}}</span>
                        
                    </h3>
                    @foreach ($barcodes as $barcode)
                    <div class="col-sm-3 border border-bottom-1 border-top-0 border-start-0 border-end-0 py-3">
                        
                        <div class="form-group">
                            @php
                                $data_bulkable = "data-bulkable-".$product_id;
                                if(!empty($barcode->is_bulk_scanned)){
                                    $data_bulkable = "";
                                }
                            @endphp
                            <input class="form-check-input data-barcode {{$data_bulkable}}  data-check-{{$product_id}}" name="barcode_no[]" value="{{$barcode->barcode_no}}" type="checkbox" value="1" id="barcode_no_{{$barcode->barcode_no}}" onclick="return false" @if(!empty($barcode->is_stock_in)) checked @endif >
                            
                            <div style="max-width: 175px;" class="d-inline-block">
                                {{-- <img class="barcode_image ms-2" style="max-width:100%;" alt="Barcoded value {{$barcode->barcode_no}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&amp;text={{$barcode->barcode_no}}&amp;includetext&height=6"> --}}
                                <span title="{{$barcode->barcode_no}}">
                                    {!! $barcode->code_html !!}
                                    <span style="width: 100%; display: block; text-align: center; color: #000000;">{{$barcode->barcode_no}}</span>
                                </span>    
                                <label for="barcode_no_{{$barcode->barcode_no}}" id="barcode_label_{{$barcode->barcode_no}}" class="text-center d-block pt-2 fw-bold label-barcode-{{$product_id}}">
                                    @if (!empty($barcode->is_scanned))
                                        SCANNED
                                    @elseif (!empty($barcode->is_bulk_scanned))
                                        NA
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
                <a href="{{route('return-spares.list')}}" class="btn btn-sm btn-danger">Back</a>
                <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Generate </button>
            </div>
        </div> 
        </form>           
    </div>  
    @endif
    
</section>
<script>
    var id = "{{ $id }}";
    var goods_in_type = "{{ $goods_in_type }}";

    $('.choose-type').on('click', function(){
        $('#nextBtn').attr('disabled',false);
    })
    // alert(goods_in_type);
    $(document).ready(function(){
        $('#nextBtn').attr('disabled',true);
        var total_barcodes = $('input:checkbox.data-barcode').length;
        var total_scanned = $('input:checkbox.data-barcode:checked').length;
        // alert(total_barcodes+'  '+total_scanned);

        if(goods_in_type != '' && goods_in_type == 'scan'){
            if(total_scanned < total_barcodes){
                $('#submitBtn').prop('disabled', true);
                // const interval = setInterval(() => {        
                //     getScannedImages(id);
                // }, 10000);
            }
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
    function bulkScan(product_id){
        var is_scanned = 0;
        var is_bulk_scanned = 0;
        if (document.getElementById('bulk_scan_'+product_id).checked == true) {
            
            var box = confirm("Are you sure want to bulk in?");
            // alert(box);
            if (box == true)  {
                $('input:checkbox.data-bulkable-'+product_id).prop('checked', true);
                $('input:checkbox#bulk_scan_'+product_id).prop('checked', true);
                is_scanned = 1;
                is_bulk_scanned = 1;
                console.log("AA !!!")
            }  else  {
                $('input:checkbox.data-bulkable-'+product_id).prop('checked', false);
                document.getElementById('bulk_scan_'+product_id).checked = false;        
                console.log("BB !!!")        
            }

            // $('input:checkbox#bulk_scan_'+product_id).not(this).prop('checked', false); 

            // console.log("CC !!!")
       
        } else {
            $('input:checkbox.data-bulkable-'+product_id).prop('checked', false);
            is_scanned = 0;
            is_bulk_scanned = 0;   
            // console.log("DD !!!")         
        }

        // alert("is_scanned:- "+is_scanned);
        // alert("is_bulk_scanned:- "+is_bulk_scanned);

        console.log("Hello Arnab !!!")
        $.ajax({
            url: "{{ route('ajax.return-spare-bulk-scan') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "return_spare_id": id,
                "product_id": product_id,
                "is_bulk_scanned": is_bulk_scanned,
                "is_scanned": is_scanned
            },
            success: function(data){
                
                var sucessData = data;
                // console.log(sucessData)   
                if(is_scanned == 0){
                    $('.label-barcode-'+product_id).html('YET TO SCAN');
                    $('#data-bulkable-'+product_id).prop('checked', false);
                    $('input:checkbox#bulk_scan_'+product_id).prop('checked', false);
                    var total_barcodes = $('input:checkbox.data-barcode').length;
                    var total_scanned = $('input:checkbox.data-barcode:checked').length;
                    
                } else if(is_scanned == 1){
                    // alert('All scanned');
                    $('.label-barcode-'+product_id).html('NA');
                    $('#data-bulkable-'+product_id).prop('checked', true);
                    $('input:checkbox#bulk_scan_'+product_id).prop('checked', true);
                    var total_barcodes = $('input:checkbox.data-barcode').length;
                    var total_scanned = $('input:checkbox.data-barcode:checked').length;
                }            
            }
        });
        var total_barcodes = $('input:checkbox.data-barcode').length;
        var total_scanned = $('input:checkbox.data-barcode:checked').length;
        if(total_scanned == total_barcodes) {
            // console.log("Hi");
            $('#submitBtn').prop('disabled', false);
        } else if (total_scanned < total_barcodes) {
            $('#submitBtn').prop('disabled', true);
        }
    }

    function getScannedImages(id){
        $.ajax({
            url: "{{ route('ajax.check-po-scanned-boxes') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "purchase_order_id": id
            },
            success: function(data){
                
                var sucessData = data;
                console.log(sucessData)
                
                // console.log(sucessData);
                for(var i = 0; i < sucessData.length; i++) {
                    
                    $('#barcode_no_'+sucessData[i].barcode_no).attr('checked', 'checked');
                    $('#barcode_label_'+sucessData[i].barcode_no).html('SCANNED');
                    var total_barcodes = $('input:checkbox.data-barcode').length;
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