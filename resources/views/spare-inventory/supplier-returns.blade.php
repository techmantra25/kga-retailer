@extends('layouts.app')
@section('content')
@section('page', 'Suplier Returned Spare')
{{-- @section('small', '(Dead Spare)') --}}
<section>
    <ul class="breadcrumb_menu">     
        <li>Dead Spare Inventory</li>      
        <li><a href="{{ route('spare-inventory.list') }}">All Dead Spare List</a></li>
        <li>Suplier Returned Spare</li>
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
            
            
        </div>
    </div>
    <div class="search__filter">
        <div class="row align-items-center ">
            <div class="col-2">
              <select name="supplier_id" class="form-control" id="supplier_id">
                <option value="" hidden selected>Choose Supplier</option>
                @forelse ($suppliers as $sp)
                  <option value="{{ $sp->id }}" @if($supplier_id == $sp->id) selected @endif>{{ $sp->public_name }}</option>
                @empty
                  
                @endforelse
              </select>
            </div>  
            <div class="col-auto">
                @if(!empty($supplier_id))
                    <a href="{{route('spare-inventory.supplier-return-list')}}" class="btn btn-outline-warning">Reset Supplier</a>
                @endif
            </div>          
            
            
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <span class="small filter-waiting-text" id=""></span>                
            </div>
            
            <div class="col-auto">
                <p>Total {{ $totalResult }} Records</p>
            </div>
            <div class="col-auto">
              @if ($totalResult > 0)
              <a href="{{ route('spare-inventory.supplier-return-csv') }}?supplier_id={{$supplier_id}}" class="btn btn-outline-success select-md">Export CSV</a>
              @endif
              
            </div> 
           
        </div>
    </div>
    </form>
    @if (empty($supplier_id))
            <span>Please choose supplier first</span>
    @else
    
    <table class="table">
        <thead>
            <tr>
              <th class="sr_no">#</th>
              <th class="date_val">Date</th>
              <th class="barcode">Barcode No</th>
              <th class="spare_desc">Spare Parts</th>  
              <th class="goods_desc">Goods</th>  
            </tr>
        </thead>
        <tbody>
          @php
              // echo $request->page; die;
              // $page = Request::get('page')?Request::get('page'):1;   
                    
              // if(empty($page) || $page == 1){                
                  $i=1;
              // } else {
              //     $i = ((($page-1)*$paginate)+1);
              // } 
          @endphp
        @forelse ($data as $item)
            <tr>
              <td class="sr_no">{{$i}}</td>
              <td data-colname="date_val"> {{date('j M Y, l', strtotime($item->updated_at))}} </td>              
              
                <td class="barcode">
                    {{$item->barcode_no}} &nbsp;&nbsp; 
                   
                    <button type="button" class="toggle_table">
                      
                    </button>
                </td>
                
                <td data-colname="spare_desc">
                    <span>{{$item->spare->title}}</span>
                                   
                </td>                
                <td data-colname="goods_desc">
                    <span>{{$item->goods->title}}</span>
                                  
                </td>  
                             
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
    
    @endif
    
</section>
<script>

   
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
        
        
    })

    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('.filter-waiting-text').text('Please wait ... ');
        $('#searchForm').submit();
    });
    
    $('#supplier_id').on('change', function(){
        $('#searchForm').submit();
    });
    
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 