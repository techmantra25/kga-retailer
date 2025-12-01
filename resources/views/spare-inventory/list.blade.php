@extends('layouts.app')
@section('content')
@section('page', 'All Dead Spare List')
{{-- @section('small', '(Dead Spare)') --}}
<section>
    <ul class="breadcrumb_menu">     
        <li>Dead Spare Inventory</li>      
        <li>All Dead Spare List</li>
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
                 
            <div class="col-2">
                <select name="return_type" class="form-control" id="return_type">
                    <option value=""  @if(empty($return_type)) selected @endif>All</option>
                    <option value="no" @if($return_type == 'no') selected @endif>Yet To return</option>
                    <option value="yes" @if($return_type == 'yes') selected @endif>Returned</option>
                </select>                
            </div>
            <div class="col-4">
                <input type="search" placeholder="Search barcode,spare,goods ... " name="search" class="form-control" id="search" autocomplete="off" value="{{ $search }}">                
            </div>
            <div class="col-4">
                                
            </div>
            <div class="col-2">
                <a href="{{ route('spare-inventory.list-return') }}" class="btn btn-success ">Spare Return To Supplier</a>
             </div>
            
        </div>
        <div class="row align-items-right justify-content-between ">
            <div class="col">
                
            </div>
            
            
            
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <span class="small filter-waiting-text" id=""></span>                
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
                <p>Total {{$totalResult}} Records</p>
            </div>
            {{-- @if (!empty($type))
                
            <div class="col-auto">
                <a href="{{ route('product.csv-export') }}?type={{$type}}&cat_id={{$cat_id}}&subcat_id={{$subcat_id}}" class="btn btn-outline-success select-md">Export CSV</a>
            </div>
            @endif --}}
        </div>
    </div>
    </form>
    
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="date_val">Date</th>
                <th class="barcode">Barcode No</th>
                <th class="spare_desc">Spare Parts</th>
                <th class="warranty_status">Warranty Status</th>
                <th class="return_status">Supplier Return Status</th>
                <th class="goods_desc">Goods</th>
                <th class="action">Action</th>
                
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
                <td data-colname="date_val"> {{date('j M Y, l', strtotime($item->created_at))}} </td>
                <td class="barcode">
                    {{$item->barcode_no}} &nbsp;&nbsp; 
                    
                    <button type="button" class="toggle_table">
                      
                    </button>
                </td>
                
                <td data-colname="spare_desc">
                  <span>{{$item->spare->title}}</span>
                </td>
                <td data-colname="warranty_status">
                  @if (!empty($item->spare_return->in_warranty))
                  <span class="badge bg-success">In Warranty</span> <br>
                  @else
                  <span class="badge bg-danger">Out of Warranty</span> <br>
                  @endif            
                </td>
                <td data-colname="return_status">
                @if (!empty($item->is_warranty))
                    <span class="badge bg-danger">Not Returnable</span> <br>
                @else
                    @if (!empty($item->is_returned))
                    <span class="badge bg-success">Returned</span> <br>
                    @else
                    <span class="badge bg-warning">Yet To Return</span> <br>
                    @endif  
                    
                @endif
                            
                </td>
                <td data-colname="goods_desc">
                  <span>{{$item->goods->title}}</span>                                 
                </td>
                <td data-colname="action">
                    <!-- Details modal -->
                    <button type="button" class="btn btn-outline-info select-md" data-bs-toggle="modal" data-bs-target="#detailsData{{$item->id}}" title="View Details">
                        Details
                    </button> 
                    {{-- {{dd($item)}} --}}
                    <!-- Modal Details -->
                    <div class="modal fade" id="detailsData{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabelDetails" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabelDetails">
                                        {{$item->barcode_no}}  &nbsp;&nbsp;
                                        @if (!empty($item->spare_return->in_warranty))
                                        <span class="badge bg-success">In Warranty</span> <br>
                                        @else
                                        <span class="badge bg-danger">Out Of Warranty</span> <br>
                                        @endif
                                        
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <div class="modal-body">                                        
                                    <div class="row">
                                        <div class="form-group mb-3">
                                            <span class="text-muted">Date : </span>{{date('j M Y, l', strtotime($item->created_at))}}
                                        </div>  
                                        <div class="form-group mb-3">
                                        <span class="text-muted">Spare Description : </span>{{$item->barcode_no }} - {{$item->spare->title}}
                                        </div>
                                        <div class="form-group mb-3">
                                            <span class="text-muted">Return Status : </span>
                                            @if (!empty($item->is_warranty))
                                                <span class="badge bg-danger">Not Returnable</span> <br>
                                            @else
                                                @if (!empty($item->is_returned))
                                                <span class="badge bg-success">Returned</span> <br>
                                                @else
                                                <span class="badge bg-warning">Yet To Return</span> <br>
                                                @endif  
                                                
                                            @endif
                                        </div>
                                        @if($item->spare_return->repair_id)
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Repair ID : </span> {{$item->spare_return->repair->unique_id}}
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Item Description : </span>{{$item->spare_return->repair->product_name}}
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted"> Serial No  : </span>{{$item->spare_return->repair->product_sl_no}}
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Customer Details : </span>{{$item->spare_return->repair->customer_name}}   ({{$item->spare_return->repair->customer_phone}})
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Address : </span>{{$item->spare_return->repair->address}} 
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Pincode : </span>{{$item->spare_return->repair->pincode}} 
                                            </div>
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Service Partner : </span>{{$item->service_partner->person_name}}  - {{$item->service_partner->company_name}} 
                                            </div> 
                                        @endif
                                       
                                        @if($item->spare_return->crp_id)
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Customer Point Repair ID : </span> {{$item->spare_return->crp->unique_id}}
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Item Description : </span>{{$item->spare_return->goods->title}}
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted"> Serial No  : </span>{{$item->spare_return->crp->serial}}
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Customer Details : </span>{{$item->spare_return->crp->customer_name}}  ({{$item->spare_return->crp->alternate_no}})
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Address : </span>{{$item->spare_return->crp->address}} 
                                            </div> 
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Pincode : </span>{{$item->spare_return->crp->pincode}} 
                                            </div>
                                            <div class="form-group mb-3">
                                                <span class="text-muted">Service Partner : </span>{{$item->service_partner->person_name}}  - {{$item->service_partner->company_name}} 
                                            </div> 
                                        @endif
                                    </div>                                   
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    
                                </div>
                            
                            </div>
                        </div>
                    </div>
                   
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
    
    {{$data->links()}}
    
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
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('.filter-waiting-text').text('Please wait ... ');
        $('#searchForm').submit();
    });
    
    $('#service_partner_id').on('change', function(){
        $('#searchForm').submit();
    });
    $('#return_type').on('change', function(){
        $('#searchForm').submit();
    });
    
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 