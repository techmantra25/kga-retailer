@extends('layouts.app')
@section('content')
@section('page', 'Service Partner Return Spare Barcodes')
<section>
    <ul class="breadcrumb_menu">
        <li>
            Returned Spare Management
        </li>
        <li><a href="{{ route('return-spares.list') }}">Service Partner Return Spares</a></li>
        
        <li><a href="{{ route('return-spares.show', Crypt::encrypt($id)) }}">
            {{$data[0]->return_spare->transaction_id}}
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

                        <a href="{{ route('return-spares.barcode-csv',Crypt::encrypt($id)) }}?search={{$search}}" id="" disabled class="btn btn-outline-primary select-md">Export Barcode CSV</a>                       
                        
                        <a href="{{ route('return-spares.list') }}" class="btn btn-outline-danger select-md">Back</a>
                        
                    </div>                  
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
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
                    {{ Session::forget('message') }}
                </div>
                @endif
            </div>
            
            <div class="col-auto">
                {{-- <p>{{$totalData}} Items</p> --}}
            </div>
        </div>
    </div>    
    <div class="" id="print_div">
        <div>
        <div class="row">
            @foreach ($data as $item)
            <div class="col-12">
                <div class="barcode_image" style="margin: 0 auto 4px">
                    
                    <span title="{{$item->barcode_no}}">
                        {!! $item->code_html !!}
                        <span style="width: 100%; display: block; text-align: center; color: #000000;">{{$item->barcode_no}}</span>
                    </span>
                    <span>{{$item->products->title}}</span>
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