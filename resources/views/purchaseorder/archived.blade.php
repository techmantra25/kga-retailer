@extends('layouts.app')
@section('content')
@section('page', 'Archived Barcodes')
<section>
    <ul class="breadcrumb_menu">
        @if ($status == 1)
        <li><a href="{{ route('purchase-order.list', ['po_type'=>'po']) }}">PO</a></li>
        @elseif ($status == 2)
        <li><a href="{{ route('purchase-order.list', ['po_type'=>'grn']) }}">GRN</a></li>
        @endif
        <li>{{$order_no}}</li>
        <li>Archived</li>
        
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
                        
                        @if($status == 2)
                        <a href="{{ route('purchase-order.list', ['po_type'=>'grn']) }}" class="btn btn-outline-danger select-md">Back</a>
                        @else 
                        <a href="{{ route('purchase-order.list', ['po_type'=>'po']) }}" class="btn btn-outline-danger select-md">Back</a>
                        @endif
                    </div>                  
                    <div class="col-auto">
                        
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
                <p>{{$totalData}} Items</p>
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
                    <span>{{$item->product->title}}</span>
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

    
</script>
@endsection 