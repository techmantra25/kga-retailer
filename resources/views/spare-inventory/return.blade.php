@extends('layouts.app')
@section('content')
@section('page', 'Return To Supplier')
{{-- @section('small', '(Dead Spare)') --}}
<section>
    <ul class="breadcrumb_menu">     
        <li>Dead Spare Inventory</li>      
        <li><a href="{{ route('spare-inventory.list') }}">All Dead Spare List</a></li>
        <li>Return To Supplier</li>
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
                {{-- <a href="{{route('product.add')}}" class="btn btn-outline-primary select-md">Add New</a>    
                <a href="{{route('product.csv-upload')}}" class="btn btn-outline-success select-md">Upload CSV</a> --}}
            </div>
            
        </div>
    </div>
    <div class="search__filter">
        <div class="row align-items-center ">
            <div class="col-4">
              <select name="supplier_id" class="form-control" id="supplier_id">
                <option value="" hidden selected>Choose Supplier</option>
                @forelse ($suppliers as $sp)
                  <option value="{{ $sp->id }}" @if($supplier_id == $sp->id) selected @endif>{{ $sp->public_name }}</option>
                @empty
                  
                @endforelse
              </select>
            </div>            
            
            
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <span class="small filter-waiting-text" id=""></span>                
            </div>
            
            <div class="col-auto">
                <p>Total {{ count($data->toArray()) }} Records</p>
            </div>
           
        </div>
    </div>
    </form>
    @if (empty($supplier_id))
            <span>Please choose supplier first</span>
    @else
    <form action="{{ route('spare-inventory.save-return') }}" method="POST">
      @csrf
      <input type="hidden" name="browser_name" id="browser_name">
      <input type="hidden" name="navigator_useragent" id="navigator_useragent">
    
    <div class="filter">
      <div class="row align-items-center justify-content-between">
        <div class="col">
          <button type="submit" class="btn btn-success" id="btnSuspend"  onclick="return confirm('Are you sure?');">Return</button>               
        </div>
      </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th class="check-column primary_column" width="10%">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                        <label class="form-check-label" for=""></label>
                    </div>
                </th>
                <th class="barcode">Barcode No</th>
                <th class="spare_desc">Spare Parts</th>
                
                
            </tr>
        </thead>
        <tbody>
        
        @forelse ($data as $item)
            <tr>
                <td data-colname="" class="check-column primary_column">
                    <div class="form-check">
                        <input name="ids[]" class="data-check" type="checkbox" value="{{$item->id}}">
                        <label class="form-check-label" for=""></label>
                    </div>
                </td>
                <td class="barcode">
                    {{$item->barcode_no}} &nbsp;&nbsp; 
                   
                    <button type="button" class="toggle_table">
                      
                    </button>
                </td>
                
                <td data-colname="spare_desc">
                    <span>{{$item->spare->title}}</span>
                                   
                </td>                
                
                             
            </tr>
            
        @empty
            <tr>
                <td colspan="12" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table> 
    </form>   
    @endif
    
</section>
<script>

    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        var navigator_useragent = navigator.userAgent;
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
        $('div.alert').delay(3000).slideUp(300);
        $('#btnSuspend').prop('disabled', true);        
        $("#checkAll").change(function () {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
            var checkAllStatus = $("#checkAll:checked").length;
            var total_data_length = "{{ count($data) }}";

            // alert('total_data_length:- '+total_data_length)
            
            // console.log(checkAllStatus)
            if(checkAllStatus == 1 && total_data_length > 0){
                $('#btnSuspend').prop('disabled', false);
            }else{
                $('#btnSuspend').prop('disabled', true);
            }
        });
        
        $('.data-check').change(function () {
            $('#btnSuspend').prop('disabled', false);
            var total_checkbox = $('input:checkbox.data-check').length;
            var total_checked = $('input:checkbox.data-check:checked').length;

            
            if(total_checked == 0 ){
                $('#btnSuspend').prop('disabled', true);
            }
          
            if(total_checkbox == total_checked){
                console.log('All checked')
                $('#checkAll').prop('checked', true);
            }else{
                console.log('Not All checked')
                $('#checkAll').prop('checked', false);
            }
        })
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