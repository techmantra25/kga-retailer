@extends('layouts.app')
@section('content')
@section('page', 'KGA Stock')
@section('small', '(Date & Showroom)')
<section>
    <ul class="breadcrumb_menu">     
        <li>Home</li>      
        <li>KGA Daily Stock</li>
    </ul> 
    
    @if (Session::has('message'))
    <div class="alert alert-success" role="alert">
        {{ Session::get('message') }}
        {{ Session::forget('message') }}
    </div>
    @endif
    <form action="" id="searchForm">
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                
            </div>            
        </div>
    </div>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <a href="{{ route('home') }}" style="text-decoration: none; color: #dc3545">
                    <i class="fi-br-arrow-alt-circle-left"></i>
                    Back To Home
                </a>
            </div>            
            <div class="col-2">
                <input type="date" name="stock_date" class="form-control" id="stock_date" value="{{$stock_date}}" max="{{ date('Y-m-d',strtotime("-1 day")) }}" >
            </div>
            <div class="col-5">
                <div class="input-group">
                    <input type="text" name="branch_name" class="form-control" placeholder="Search showroom ..." onkeyup="searchBranch(this.value);" id="branch_name" value="{{ $branch_name }}" autocomplete="off">
                    <input type="hidden" name="branch_id" id="branch_id" value="{{ $branch_id }}">
                    
                    <div class="input-group-append">
                      <a href="{{ route('kga-daily-stock') }}?stock_date={{$stock_date}}&product_id={{$product_id}}&product_name={{$product_name}}&paginate={{$paginate}}" class="btn btn-outline-secondary" id="allbankothers">Reset</a>
                    </div>                    
                </div>
                <div class="respBranch" id="respBranch" style="position: relative;"></div>
            </div>
            
                       
        </div>
    </div>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">

            </div>            
            
            <div class="col-7">

                <div class="input-group">
                    <input type="text" name="product_name" class="form-control" placeholder="Search product ..." onkeyup="getProducts(this.value);" id="product_name" value="{{ $product_name }}" autocomplete="off">
                    <input type="hidden" name="product_id" id="product_id" value="{{ $product_id }}">
                    <div class="input-group-append">
                      <a href="{{ route('kga-daily-stock') }}?stock_date={{$stock_date}}&branch_id={{$branch_id}}&branch_name={{$branch_name}}&paginate={{$paginate}}" class="btn btn-outline-secondary" id="allbankothers">Reset</a>
                    </div>                    
                </div>
                <div class="respProduct" id="respProduct" style="position: relative;"></div>
            </div>
            
            
                      
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <span class="small filter-waiting-text" id="smallTextMsg"></span>                
            </div>
            <div class="col-auto">
                Number of rows:
            </div>
            <div class="col-auto p-0">
                <select name="paginate" id="paginate" class="form-control select-md" id="">
                    <option value="25" @if($paginate == 25) selected @endif>25</option>
                    <option value="50" @if($paginate == 50) selected @endif>50</option>
                    <option value="100" @if($paginate == 100) selected @endif>100</option>
                    <option value="200" @if($paginate == 200) selected @endif>200</option>
                </select>
            </div>
            <div class="col-auto">
                <p>Total {{$totalResult}} Records</p>
            </div>
            <div class="col-auto">
                <a href="{{ route('csv-daily-stock') }}?stock_date={{$stock_date}}&product_id={{$product_id}}&product_name={{$product_name}}&branch_id={{$branch_id}}&branch_name={{$branch_name}}" class="btn btn-outline-success select-md">Export CSV</a>
            </div>
        </div>
    </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                @if (empty($product_id))
                <th>Product</th>
                @endif                
                @if (empty($branch_id))
                <th>Showroom</th>
                @endif                
                <th>Opening</th>
                <th>Received</th>
                <th>Issued</th>
                <th>Closing</th>
                <th>Available</th>
                <th>Defective</th>
                <th>Display</th>
                <th>Transit</th>
                <th>Defective Transit</th>
            </tr>
        </thead>
        <tbody>
        @php
            $page = Request::get('page');
            if(empty($page) || $page == 1){                
                $i=1;
            } else {
                $i = ((($page-1)*$paginate)+1);
            } 
        @endphp
        @forelse ($data as $item)
            <tr>
                <td>{{$i}}</td>
                @if (empty($product_id))
                <td data-colname="Product">{{$item->itemcode}}</td>    
                @endif                
                @if (empty($branch_id))
                <td data-colname="Showroom">{{$item->sitecode_info}}</td>                    
                @endif
                <td data-colname="Opening">{{$item->opening}}</td>
                <td data-colname="Received">{{$item->received}}</td>
                <td data-colname="Issued">{{$item->issued}}</td>
                <td data-colname="Closing">{{$item->closing}}</td>
                <td data-colname="Available">{{$item->available}}</td>
                <td data-colname="Defective">{{$item->defective}}</td>
                <td data-colname="Display">{{$item->display}}</td>
                <td data-colname="Transit">{{$item->transit}}</td>
                <td data-colname="Defective Transit">{{$item->defective_transit}}</td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="12" style="text-align: center;">
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
    
    $('#branch_id').on('change', function(){
        $('#searchForm').submit();
    });
    $('#paginate').on('change', function(){
        $('#searchForm').submit();
    });
    $('#stock_date').on('change', function(){     
        $('#searchForm').submit();
    });
    $('#stock_date').on('keydown', function(){
        $('#smallTextMsg').text('Please select date by clicking on calender icon ... ');
        $('#smallTextMsg').delay(3000).fadeOut('slow');
        return false;
    });
    function searchBranch(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-branches') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function(result) {
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  branch-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchBranch(${value.id},'${value.name}')">${value.name} </a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  branch-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No branch found</li></div>`;
                    }
                    $('#respBranch').html(content);
                }
            });
        } else {
            $('.branch-dropdown').hide()
        }
        
    }

    function fetchBranch(id,name) {
        $('.branch-dropdown').hide()
        $('#branch_id').val(id);
        $('#branch_name').val(name);
        $('#searchForm').submit();
    }

    function getProducts(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    type: 'fg'
                },
                success: function(result) {
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct(${value.id})">${value.title}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respProduct').html(content);
                }
            });
        } else {
            $('.product-dropdown').hide()
        }
        
    }

    function fetchProduct(id) {
        $('.product-dropdown').hide()

        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id
            },
            success: function(result) {
                var title = result.title;

                $('#product_name').val(title);
                $('#product_id').val(id);
                $('#searchForm').submit();
                
            }
        });             
    }
    
</script>  
@endsection 