@extends('layouts.app')
@section('content')
@section('page', 'Product dap-barcode')
<section>
    <ul class="breadcrumb_menu">        
        <li>DAP BARCODE</li>
    </ul>    

            <div class="col-auto">
                <div class="row g-3 align-items-center">  
                    <div class="col-auto">
                        <a onclick='printResultHandler()' class="btn btn-outline-primary select-md">Download PDF</a>
                        <a href="{{ route('dap-services.list') }}" class="btn btn-outline-danger select-md">Back</a>
                        
                    </div>                  
                </div>
            </div>  
            <div class="" id="print_div">
                <div class="col-12 page_bar">
                    <div class="barcode_image" style="margin: 0 auto 4px">
                    <img class="" title="{{$data->unique_id}}" alt="Barcoded value {{$data->unique_id}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{$data->unique_id}}&height=6&textsize=14&scale=6&includetext">
                    <span>Repair product</span>
                    </div>
                </div>
            </div>
</section>
<script>
  

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