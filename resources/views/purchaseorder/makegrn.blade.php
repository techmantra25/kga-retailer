@extends('layouts.app')
@section('content')
@section('page', 'Generate GRN')
<style>
#bulk_archive_items{
    position: fixed;
    top: 83px;
    z-index: 9999;
    right: 21px;
}
#uncheck_archive_items{
    position: fixed;
    top: 112px;
    z-index: 9999;
    right: 21px;
}
   
</style>
<section>
    <a href="javascript:void(0)" id="bulk_archive_items" onclick="BulkArchived('{{$getQueryString}}','{{$goods_in_type}}');" class="btn btn-outline-danger select-md">Bulk Archive</button>
    <a href="javascript:void(0)" id="uncheck_archive_items" onclick="uncheckInputs();" style="display: none;" class="btn btn-outline-success select-md">Reset Archive</button>
    </a>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('purchase-order.list') }}?{{$getQueryString}}">PO</a> </li>
        <li>{{$order_no}}</li>
    </ul>
    @if (empty($goods_in_type))
    <div class="row">
        <form action="" method="GET">  
        <div class="row">
            <div class="col-md-4">
                    <div class="form-group d-flex align-items-center" style="height: 30px;">
                        <label for="">Goods In With  </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input choose-type" type="radio" name="goods_in_type" id="scan" value="scan">
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
                    <a href="{{ route('purchase-order.list') }}" class="btn btn-danger select-md">Back</a>
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

                <input type="hidden" name="goods_in_type" value="{{$goods_in_type}}">
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
    @if (Session::has('message'))
    <div class="alert alert-success" role="alert">
        {{ Session::get('message') }}
        {{ Session::forget('message') }}
    </div>
    @endif
    <div class="row">
        <form id="myForm" action="{{ route('purchase-order.generate-grn') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="browser_name" class="browser_name">
            <input type="hidden" name="navigator_useragent" class="navigator_useragent">
        <div class="col-sm-12">
            @php
                $i=1;
            @endphp
            @foreach ($data as $product_id => $barcodes)
            <input type="hidden" name="product_id[]" value="{{$product_id}}">
            <input type="hidden" name="count[]" value="{{ count($data[$product_id]) }}">
            @php
                $product_title = getSingleAttributeTable('products','id',$product_id,'title');
                $isBulkScanned = isBulkScanned($id,$product_id);
                // dd($data[$product_id]);
                
            @endphp
            <div class="card shadow-sm">
                <div class="row">
                    <h3 class="pb-5">  
                        @if($goods_in_type == 'bulk')
                        <label class="form-check-label check-bulk" for="bulk_scan_{{$product_id}}">
                            <input class="form-check-input data-check-{{$product_id}}" name=""  type="checkbox" value="1" id="bulk_scan_{{$product_id}}" 
                            onchange="bulkScan({{$product_id}});" 
                            @if($isBulkScanned) checked  @endif 
                            style="height: 15px; width: 15px; border-radius: 3px;">
                            Bulk In ({{count($data[$product_id])}})
                        </label>
                        @endif
                        <span class="ms-4">#{{$i}}:   {{$product_title}}</span> 
                                                        
                        <a href="{{ route('purchase-order.remove-item',[$id,$product_id]) }}" onclick="return confirm('Are you sure want to delete? All boxes will be removed with this item for order.');" class="btn btn-outline-danger select-md">Remove Item</button>
                        </a>
                    </h3>
                    @foreach ($barcodes as $barcode)
                    <div class="col-sm-3 border border-bottom-1 border-top-0 border-start-0 border-end-0 py-3">
                       
                        @php
                            $data_bulkable = "data-bulkable-".$product_id;
                            if(!empty($barcode->is_bulk_scanned)){
                                $data_bulkable = "";
                            }
                        @endphp
                        
                         {{-- <input class="form-check-input data-barcode {{$data_bulkable}}  data-check-{{$product_id}}" name="barcode_no[]" value="{{ $barcode->barcode_no ? $barcode->barcode_no : 1 }}" type="checkbox" id="barcode_no_{{$barcode->barcode_no}}" onclick="return false" @if(!empty($barcode->is_scanned) || !empty($barcode->is_bulk_scanned)) checked @endif> --}}
                        <div class="form-group">
                            <input type="checkbox" class="form-check-input data-barcode {{$data_bulkable}}  data-check-{{$product_id}}" name="barcode_no[]" value="{{ $barcode->barcode_no ? $barcode->barcode_no : 1 }}" type="checkbox" id="barcode_no_{{$barcode->barcode_no}}" @if(!empty($barcode->is_scanned) || !empty($barcode->is_bulk_scanned)) checked @endif>
                            
                                <div style="max-width: 175px;" class="d-inline-block single_bar_code">
                                    {{-- <img class="barcode_image ms-2" style="max-width:100%;" alt="Barcoded value {{$barcode->barcode_no}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&amp;text={{$barcode->barcode_no}}&amp;includetext&height=6"> --}}
                                    <label for="barcode_no_{{$barcode->barcode_no}}" onclick="SingleScan('{{$barcode->barcode_no}}', '{{$barcode->id}}');">
                                        <span title="{{$barcode->barcode_no}}">
                                            {!! $barcode->code_html !!}
                                            <span style="width: 100%; display: block; text-align: center; color: #000000;">{{$barcode->barcode_no}}</span>
                                        </span>  
                                    </label>  
                                    <label for="barcode_no_{{$barcode->barcode_no}}" id="barcode_label_{{$barcode->barcode_no}}" class="text-center d-block pt-2 fw-bold label-barcode-{{$product_id}}">
                                        @if (!empty($barcode->is_scanned))
                                            SCANNED
                                        @elseif (!empty($barcode->is_bulk_scanned))
                                            CHECKED
                                        @else

                                        @endif                                    
                                    </label>
                                    <div class="row">
                                        <div class="col-6 d-flex">
                                            @if (empty($barcode->is_bulk_scanned))
                                                {{-- <a href="{{ route('purchase-order.archive',[$type,$id,$product_id,$barcode->barcode_no,$goods_in_type,$getQueryString]) }}" onclick="return confirm('Are you sure want to archive {{$barcode->barcode_no}} ');" class="btn btn-outline-danger select-md archive-prod-{{$product_id}}" id="archive_prod{{$barcode->barcode_no}}">Archive</a> --}}
                                                    <input type="checkbox" class="form-check-input data-barcode bulk_archive_data" value="" id="bulk_archive_{{$barcode->barcode_no}}" data-type="{{$type}}" data-id="{{$id}}" data-product_id="{{$product_id}}" data-barcode_no="{{$barcode->barcode_no}}">
                                                    <label class="btn btn-outline-danger select-md archive-prod-{{$product_id}}" id="archive_prod{{$barcode->barcode_no}}" for="bulk_archive_{{$barcode->barcode_no}}">
                                                        Archive
                                                    </label>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-outline-primary select-md" onclick="downloadImage('{{$barcode->barcode_no}}')" >Download</button>
                                            
                                        </div>
                                    </div>
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
                <a href="{{route('purchase-order.list')}}" class="btn btn-sm btn-danger">Back</a>
                @if (!empty($search))
                    <p class="small">Please remove search text to proceed further</p>
                @else
                    @if ($goods_in_type == 'scan')
                        @if ($count_scanned == $count_barcodes)
                            <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Generate </button>
                        @else
                            <a href="" class="btn btn-sm btn-success">Check Scanning Status ({{$count_scanned}})</a>
                        @endif
                        
                    @else
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Generate </button>
                    @endif
                @endif
                
                
                <p class="small filter-waiting-text"></p>
            </div>
        </div> 
        </form>           
    </div>  
    @endif
    
</section>
<script>
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        var navigator_useragent = navigator.userAgent;
        $('.navigator_useragent').val(navigator_useragent);
        if (test(/opr\//i) || !!window.opr) {
            return 'Opera';
        } else if (test(/edg/i)) {
            return 'Microsoft Edge';
        } else if (test(/chrome|chromium|crios/i)) {
            return 'Google Chrome';
        } else if (test(/firefox|fxios/i)) {
            return 'Mozilla Firefox';
        } else if (test(/safari/i)) {
            return 'Apple Safari';
        } else if (test(/trident/i)) {
            return 'Microsoft Internet Explorer';
        } else if (test(/ucbrowser/i)) {
            return 'UC Browser';
        } else if (test(/samsungbrowser/i)) {
            return 'Samsung Browser';
        } else {
            return 'Unknown browser';
        }
    }
    const browserType = getBrowserType();
    console.log(browserType);
    $('.browser_name').val(browserType);


    var id = "{{ $id }}";
    var goods_in_type = "{{ $goods_in_type }}";

    $('.choose-type').on('click', function(){
        $('#nextBtn').attr('disabled',false);
    })
    // alert(goods_in_type);
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);        
        $('#nextBtn').attr('disabled',true);
        var total_barcodes = $('input:checkbox.data-barcode').length;
        var total_scanned = $('input:checkbox.data-barcode:checked').length;
       
        
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        $('.filter-waiting-text').text('Please Wait ... This Process Will Take A Few Minutes .');
        return true;
    });
    function bulkScan(product_id){
        var is_bulk_scanned = 0;
        if (document.getElementById('bulk_scan_'+product_id).checked == true) {
            
            var box = confirm("Are you sure want to bulk in?");
            // alert(box);
            if (box == true)  {
                $('input:checkbox.data-bulkable-'+product_id).prop('checked', true);
                $('input:checkbox#bulk_scan_'+product_id).prop('checked', true);                
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
            is_bulk_scanned = 0;   
            // console.log("DD !!!")         
        }

        // alert("is_scanned:- "+is_scanned);
        // alert("is_bulk_scanned:- "+is_bulk_scanned);

        console.log("Hello Arnab !!!")
        $.ajax({
            url: "{{ route('ajax.po-bulk-scan') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "purchase_order_id": id,
                "product_id": product_id,
                "is_bulk_scanned": is_bulk_scanned
            },
            success: function(data){
                
                var sucessData = data;
                // console.log(sucessData)   
                if(is_bulk_scanned == 0){
                    $('.label-barcode-'+product_id).html('');
                    $('#data-bulkable-'+product_id).prop('checked', false);
                    $('input:checkbox#bulk_scan_'+product_id).prop('checked', false);
                    var total_barcodes = $('input:checkbox.data-barcode').length;
                    var total_scanned = $('input:checkbox.data-barcode:checked').length;
                    $('.archive-prod-'+product_id).show();
                    
                } else if(is_bulk_scanned == 1){
                    // alert('All scanned');
                    $('.label-barcode-'+product_id).html('CHECKED');
                    $('#data-bulkable-'+product_id).prop('checked', true);
                    $('input:checkbox#bulk_scan_'+product_id).prop('checked', true);
                    var total_barcodes = $('input:checkbox.data-barcode').length;
                    var total_scanned = $('input:checkbox.data-barcode:checked').length;
                    $('.archive-prod-'+product_id).hide();
                }            
            }
        });
        var total_barcodes = $('input:checkbox.data-barcode').length;
        var total_scanned = $('input:checkbox.data-barcode:checked').length;
        
    }

    function SingleScan(barcode_no, barcode_id) {
        if (!$('#barcode_no_'+barcode_no).is(":checked")) {
            $('#barcode_no_'+barcode_no).prop('checked', false);
            var is_bulk_scanned = 1;
        }else{
            $('#barcode_no_'+barcode_no).prop('checked', true);
            var is_bulk_scanned = 0;
        }
        $.ajax({
            url: "{{ route('ajax.po-single-scan') }}",
            dataType: 'json',
            type: 'post',
            data: {
                "_token": "{{ csrf_token() }}",
                "purchase_order_id": id,
                "barcode_id": barcode_id,
                "is_bulk_scanned": is_bulk_scanned
            },
            success: function(data){
                var sucessData = data;
                // console.log(sucessData)   
                if(is_bulk_scanned == 0){
                    $('#barcode_label_'+barcode_no).html('');
                    $('#archive_prod'+barcode_no).show();
                    $('#bulk_archive_'+barcode_no).show();
                    
                } else if(is_bulk_scanned == 1){
                    $('#barcode_label_'+barcode_no).html('CHECKED');
                    $('#archive_prod'+barcode_no).hide();
                    $('#bulk_archive_'+barcode_no).hide();
                }            
            }
        });
    }
    function BulkArchived(getQueryString,goods_in_type) {
        
        var checkedItems = $('.bulk_archive_data:checked');
        var checkedCount = checkedItems.length;
        if (checkedCount === 0) {
            $('#uncheck_archive_items').hide();
            alert('Please check at least one item.');
        } else {
            if (confirm("Are you sure you want to proceed with "+checkedCount+" item?")) { // Confirmation dialog
                var data_item = [];
                checkedItems.each(function() {
                    var item = {};
                    item['id'] = $(this).data('id');
                    item['type'] = $(this).data('type');
                    item['product_id'] = $(this).data('product_id');
                    item['barcode_no'] = $(this).data('barcode_no');
                    data_item.push(item);
                });
                $('#uncheck_archive_items').show();
                // AJAX Call
                $.ajax({
                    url: "{{route('purchase-order.bulk_archive')}}", // Specify your endpoint URL here
                    method: 'POST', // Specify the HTTP method (POST, GET, etc.)
                    data: {
                        data: data_item,
                        getQueryString: getQueryString,
                        goods_in_type: goods_in_type,
                        _token: "{{ csrf_token() }}" // Include the CSRF token
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        console.error('AJAX request error:', error);
                    }
                });
            }
        }
    }
    function uncheckInputs() {
        $('.bulk_archive_data').prop('checked', false);
    }





    function downloadImage(name){
        var url = "https://bwipjs-api.metafloor.com/?bcid=code128&includetext&text="+name;

        fetch(url)
            .then(resp => resp.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                // the filename you want
                a.download = name;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(() => alert('An error sorry'));
    }
</script>  
@endsection 