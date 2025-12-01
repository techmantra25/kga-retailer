@extends('layouts.app')
@section('content')
@section('page', 'Defective Stock Barcodes')

<style>
    @media print {
        .no-print {
            display: none;
        }
        body {
            font-size: 12px; /* Adjust font size for print */
        }
        /* Additional print styles can go here */
    }
</style>

<script>
    var navigator_useragent = '';
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        navigator_useragent = navigator.userAgent;
                
        $('#navigator_useragent').val(navigator_useragent);
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
    $('#browser_name').val(browserType);
    
    $(document).ready(function(){
        $('#browser_name').val(browserType);
        $('#navigator_useragent').val(navigator_useragent);
        

    })
</script>

<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col"></div>
            <div class="col-auto"></div>
            <div class="col-4">
                <form action="" id="searchForm">
                    <div class="row g-3 align-items-center">  
                        <div class="col d-flex justify-content-between">
                            <input class="form-control select-md" type="text" name="search" value="{{$search}}" placeholder="Search Barcode here..."> 
                            <a href="{{ route('stock.all-damage-stock-barcodes') }}" class="btn btn-outline-warning select-md">Reset</a>      
                            <a href="{{ route('stock.list') }}" class="btn btn-outline-danger select-md">Back</a>      
                        </div>                  
                    </div>
                    <input type="hidden" name="browser_name" id="browser_name">
                    <input type="hidden" name="navigator_useragent" id="navigator_useragent">
                </form>
            </div>
        </div>
    </div>

    <div class="filter">
        <div class="row align-items-center justify-content-between">          
            <div class="col-auto">
                <p>{{$count}} Barcodes</p>
            </div>
        </div>
    </div>    
    <table class="table">
        <thead>
            <tr>
                <th>Barcode</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $item)
                <tr>
                    <td id="print_td_{{ $item->id }}">
                        <div class="barcode_image" style="margin: 0 auto 20px; height:auto;">   
                            <span title="{{$item->barcode_no}}">
                                <img alt="Barcoded value {{$item->barcode_no}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{$item->barcode_no}}&height=6&textsize=14&scale=6&includetext">
                            </span>
                            <div style="text-align: center;">{{$item->product->title ? $item->product->title : ""}}@if($item->is_damage == 1)<strong>(DEFECTIVE)</strong>@endif</div>
                        </div>
                    </td>
                    <td>
                        <span class="badge rounded-pill bg-danger no-print">Defective</span>
                    </td>
                    <td>
                        <a onclick="printResultHandler({{ $item->id }})" id="downloadbtn" class="btn btn-outline-primary select-md no-print">Download</a> 
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center;">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{$data->links()}}
</section>
<script>
    $('input[type=search]').on('search', function () {
        $('#searchForm').submit();
    });

    function printResultHandler(id) {
        var printTrId = 'print_td_' + id;
        var trElements = document.getElementById(printTrId).innerHTML;

        var oldPage = document.body.innerHTML;
        document.body.innerHTML = "<html><head><title></title></head><body><font size='2'>" + trElements + "</font></body>";
        window.print();
        document.body.innerHTML = oldPage;
    }

    function performAction(id, product_id) {
        alert('Perform action for barcode: ' + id);
    }

    function handleCheckboxChange(id, isChecked) {
        var browserName = $('#browser_name').val();
        var userAgent = $('#navigator_useragent').val();
        var route = '{{ route("stock.barcode-damage-check", [":id", ":status"]) }}';
        route = route.replace(':id', id).replace(':status', isChecked ? 1 : 0);

        route += '?browser_name=' + browserName + '&navigator_useragent=' + navigatorUserAgent;
        window.location.href = route;
    }
</script>
@endsection
