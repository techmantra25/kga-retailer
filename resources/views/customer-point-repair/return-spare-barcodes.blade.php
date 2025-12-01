@extends('layouts.app')
@section('content')
@section('page', 'Service Partner Return Spare Barcodes')
<section>
    <ul class="breadcrumb_menu">
        <li>
            Returned Spare Management
        </li>
        <li><a href="{{ route('return-spares.list') }}">Service Partner Return Spares</a></li>
        
        <li><a href="{{ route('customer-point-repair.add-spare', Crypt::encrypt($id)) }}">
            {{$crp_data->unique_id}}
        </a></li>      
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
                        <a onclick='printResultHandler()' id="downloadbtn" class="btn btn-outline-primary select-md">Download PDF</a>                      
                        <a href="{{ route('customer-point-repair.add-spare', Crypt::encrypt($id)) }}" class="btn btn-outline-danger select-md">Back</a>      
                    </div>                  
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">          
            <div class="col-auto">
                <p>{{$totalData}} Items</p>
            </div>
        </div>
    </div>    
    <div class="" id="print_div">
        <div>
        <div class="row">
            @foreach ($data as $item)
            <div class="col-12 page_bar">
                <div class="barcode_image" style="margin: 0 auto 20px">   
                    <span title="{{$item->new_barcode}}">
                    <img class="" alt="Barcoded value {{$item->new_barcode}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{$item->new_barcode}}&height=6&textsize=14&scale=6&includetext">
                    </span>
                    <span>{{$item->ProductData->title}} (DEFECTIVE)</span>
                </div>
            </div>
            @endforeach
        </div>
        </div>
    </div>  
</section>
<script>
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });

    function printResultHandler() {
        //Get the HTML of div
        var print_header = '';
        var divElements = document.getElementById("print_div").innerHTML;
        var print_footer = '';

        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML =
                "<html><head><title></title></head><body><font size='2'>" +
                divElements + "</font>" + print_footer + "</body>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
        //bindUnbind();
    }

    
</script>
@endsection 