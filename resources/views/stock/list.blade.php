@extends('layouts.app')
@section('content')
@section('page', 'Stock')
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <a href="{{ route('stock.product-by-barcode') }}" class="btn btn-outline-success select-md">Search Product By Barcode</a>
            </div>
            
        </div>
    </div>
    <form action="" id="searchForm">
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
           
            <div class="col-2">
                <select name="type" class="form-control select-md" id="type">
                    <option value="">All Types</option>
                    <option value="fg" @if($type == 'fg') selected @endif>Finshed Goods</option>
                    <option value="sp" @if($type == 'sp') selected @endif>Spare Parts</option>
                </select>     
            </div>
            @if (!empty($type) )
            <div class="col-2">
                <select name="cat_id" class="form-control select-md" id="cat_id">
                    <option value="">All Class</option>
                    @forelse ($category as $cat)
                    <option value="{{$cat->id}}" @if($cat_id == $cat->id) selected @endif>{{$cat->name}}</option>
                    @empty
                    <option value=""> -- No Class Found -- </option>
                    @endforelse
                </select>
            </div>            
            @endif
            @if (!empty($type) && ($type == 'sp') && !empty($cat_id))
            <div class="col-2">
                <select name="subcat_id" class="form-control select-md" id="subcat_id">
                    <option value="">All Group</option>
                    @forelse ($sub_category as $subcat)
                    <option value="{{$subcat->id}}" @if($subcat_id == $subcat->id) selected @endif>{{$subcat->name}}</option>
                    @empty
                    <option value=""> -- No Group Found -- </option>
                    @endforelse
                </select>
            </div>
            @endif
            <div class="col-4">
                <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">   
            </div>
            <div class="col-3">
                <input type="search" name="search_barcode" value="{{$search_barcode}}" class="form-control select-md" placeholder="Search barcode for defective..">   
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            
            <div class="col-auto">
                <a href="{{ route('stock.list') }}" class="btn btn-outline-warning select-md">Reset Page</a>
                <a href="{{ route('stock.stock-list-csv') }}?search={{$search}}&type={{$type}}&cat_id={{$cat_id}}&subcat_id={{$subcat_id}}" class="btn btn-outline-success select-md">CSV Export</a>
            </div>
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <!-- @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                    {{ Session::forget('message') }}
                </div>
                @endif -->
                Total Stock Barcodes : {{$stock_barodes_count}} &nbsp; | &nbsp;
                <a href="{{ route('stock.all-damage-stock-barcodes') }}" style="text-decoration: none;"> Stock Defective Barcodes : {{$stock_damage_barcodes_count}}</a>
            </div>
            <div class="col-auto">
                Number of rows:
            </div><div class="col-auto p-0">
                <select name="paginate" id="paginate" class="form-control select-md" id="">
                    <option value="25" @if($paginate == 25) selected @endif>25</option>
                    <option value="50" @if($paginate == 50) selected @endif>50</option>
                    <option value="100" @if($paginate == 100) selected @endif>100</option>
                    <option value="200" @if($paginate == 200) selected @endif>200</option>
                </select>
            </div>
            <div class="col-auto">
                <p>Total Result {{$totalResult}}</p>
            </div>
        </div>
    </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>                
                <th>Product</th>    
                <th>Type</th>    
                <th>Current Stock</th>
                <th>View Logs</th>
            </tr>
        </thead>
        <tbody>
        @php
            if(empty(Request::get('page')) || Request::get('page') == 1){
                $i=1;
            } else {
                $i = (((Request::get('page')-1)*$paginate)+1);
            } 
        @endphp
        @forelse ($data as $item)
            <tr>
                <td>{{$i}}</td>
                <td>
                    {{$item->product->unique_id}} - {{$item->product->title }}                    
                </td>
                <td>
                    @if ($item->product->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                <td> 
                    <span class="showdetails">{{ $item->quantity }}</span>
                </td>
                <td>
                    <a href="{{ route('stock.logs', [Crypt::encrypt($item->product_id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">View</a>
                    <a href="{{ route('stock.barcodes', [Crypt::encrypt($item->product_id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Barcodes</a>
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="5" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table>
    {{$data->links()}}
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#type').on('change', function(){
        $('#cat_id').val('');
        $('#subcat_id').val('');
        $('#searchForm').submit();
    })
    $('#cat_id').on('change', function(){   
        $('.filter-waiting-text').text('Please wait ... ');     
        $('#searchForm').submit();
    })
    $('#subcat_id').on('change', function(){
        $('.filter-waiting-text').text('Please wait ... ');
        $('#searchForm').submit();
    })
    $('#paginate').on('change',function(){
        $('#searchForm').submit();
    })
</script>  
@endsection 