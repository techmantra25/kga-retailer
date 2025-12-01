@extends('layouts.app')
@section('content')
@section('page', 'Search Stock Barcodes')
<section>
    <ul class="breadcrumb_menu">
        <li>
            Product Nane: <storng>{{$data->product->title}}</storng>
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
                        <!-- <a onclick='printResultHandler()' id="downloadbtn" class="btn btn-outline-primary select-md">Download PDF</a>                       -->
                        <a href="{{ route('stock.list') }}" class="btn btn-outline-danger select-md">Back</a>      
                    </div>                  
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">          
            <div class="col-auto">
            </div>
        </div>
    </div>    
        <table class="table">
            <thead>
                <tr>
                    <th>Barcode</th>
                    <th>Defective</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td id="print_td_{{ $data->id }}">
                        <div class="barcode_image" style="margin: 0 auto 20px; height:auto;">   
                            <span title="{{$data->barcode_no}}">
                                <img class="" alt="Barcoded value {{$data->barcode_no}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{$data->barcode_no}}&height=6&textsize=14&scale=6&includetext">
                            </span>
                            <div style="text-align:center;">{{$data->product->title ? $data->product->title : ""}}@if($data->is_damage == 1)<strong>(DEFECTIVE)</strong>@endif</div>
                        </div>
                    </td>
                    <td>
                        <input class="form-check-input" type="checkbox" id="$data->id" 
                        onchange="handleCheckboxChange({{ $data->id }}, this.checked)" {{$data->is_damage==1?"checked":""}} style="width:30px;">

                        @if($data->is_damage==1)
                        <a onclick="printResultHandler({{ $data->id }})" id="downloadbtn" class="btn btn-outline-primary select-md no-print">Download</a> 
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
</section>
<script>
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });

    // function printResultHandler() {
    //     //Get the HTML of div
    //     var print_header = '';
    //     var divElements = document.getElementById("print_div").innerHTML;
    //     var print_footer = '';

    //     //Get the HTML of whole page
    //     var oldPage = document.body.innerHTML;
    //     //Reset the page's HTML with div's HTML only
    //     document.body.innerHTML =
    //             "<html><head><title></title></head><body><font size='2'>" +
    //             divElements + "</font>" + print_footer + "</body>";
    //     //Print Page
    //     window.print();
    //     //Restore orignal HTML
    //     document.body.innerHTML = oldPage;
    //     //bindUnbind();
    // }


    function printResultHandler(id) {
        var printTrId = 'print_td_' + id;
        var trElements = document.getElementById(printTrId).innerHTML;

        var oldPage = document.body.innerHTML;
        document.body.innerHTML = "<html><head><title></title></head><body><font size='2'>" + trElements + "</font></body>";
        window.print();
        document.body.innerHTML = oldPage;
    }

    function performAction(id,product_id) {
        // Add the desired action functionality here
        alert('Perform action for barcode: ' + id);
    }
    function handleCheckboxChange(id, isChecked) {
        // Define the base route URL
        var route = '{{ route("stock.barcode-damage-check", [":id", ":status"]) }}';
        
        // Replace the placeholder with actual values
        route = route.replace(':id', id).replace(':status', isChecked ? 1 : 0);

        // Redirect to the route (which triggers a page load)
        window.location.href = route;
    }
    
</script>
@endsection 