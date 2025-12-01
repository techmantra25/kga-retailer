@extends('layouts.app')
@section('content')
@section('page', 'Product')
@section('small', '(Goods & Spares)')
<section>
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
                <ul>
                    <li @if(!Request::get('status') || (Request::get('status') == 'all')) class="active" @endif><a href="{{route('product.list')}}">All <span class="count">({{$total}})</span></a></li>
                    <li @if(Request::get('status') == 'active' ) class="active" @endif><a href="{{route('product.list',['status'=>'active'])}}">Active <span class="count">({{$totalActive}})</span></a></li>
                    <li @if(Request::get('status') == 'inactive' ) class="active" @endif><a href="{{route('product.list',['status'=>'inactive'])}}">Inactive <span class="count">({{$totlInactive}})</span></a></li>
                </ul>
            </div>
            <div class="col-auto">
                <a href="{{route('product.add')}}" class="btn btn-outline-primary select-md">Add New</a>    
                <a href="{{route('product.csv-upload')}}" class="btn btn-outline-success select-md">Upload CSV</a>
            </div>
            
        </div>
    </div>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
            </div>
            <input type="hidden" name="status" value="{{$status}}">
            <div class="col-2">
                <select name="type" class="form-control select-md" id="type">
                    <option value="">All Product Types</option>
                    <option value="fg" @if($type == 'fg') selected @endif>Type - Finished Goods</option>
                    <option value="sp" @if($type == 'sp') selected @endif>Type - Spare Parts</option>
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
            <div class="col-auto">
                <a href="{{ route('product.list') }}" class="btn btn-outline-warning select-md">Reset Page</a>
                
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
            @if (!empty($type))
                
            <div class="col-auto">
                <a href="{{ route('product.csv-export') }}?type={{$type}}&cat_id={{$cat_id}}&subcat_id={{$subcat_id}}" class="btn btn-outline-success select-md">Export CSV</a>
            </div>
            @endif
        </div>
    </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th class="sr_no">#</th>
                <th class="primary_column">ID</th>
                <th>Created At</th>
                <th>Item Name</th>
                <th>Class & Group</th>
                <th>MOP</th>
                <th>Type</th>
                @if ($type == 'fg')
                <th>Installable</th>
                @elseif ($type == 'sp')
                <th>Spare Type</th>
                @endif
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @php
            // echo $request->page; die;
            // $page = Request::get('page')?Request::get('page'):1;   
                  
            if(empty($page) || $page == 1){                
                $i=1;
            } else {
                $i = ((($page-1)*$paginate)+1);
            } 
        @endphp
        @forelse ($data as $item)
            <tr>
                <td class="sr_no">{{$i}}</td>
                <td class="primary_column">
                    {{$item->unique_id}}
                    <button type="button" class="toggle_table"></button>
                </td>
                <td data-colname="Created At"> {{date('j M Y, l', strtotime($item->created_at))}} </td>
                <td data-colname="Item Name">
                    <span>{{$item->title}}</span>
                    @if (!empty($item->is_test_product))
                    <ul class="pincodeclass"><li>Test Product</li></ul>
                    @endif                
                </td>
                <td data-colname="Category > Subcategory">
                    <ul class="pincodeclass">
                        @if (!empty($item->cat_id))
                        <li>
                            <a href="{{ route('category.show', Crypt::encrypt($item->cat_id)) }}">CLASS:- {{$item->category->name}}</a>
                        </li>
                        @endif
                        
                        @if (!empty($item->subcat_id))
                        <li>
                            <a href="{{ route('category.show', Crypt::encrypt($item->subcat_id)) }}">GROUP:- {{$item->subcategory->name}}</a>
                        </li>
                        @endif
                        
                    </ul>
                </td>
                <td data-colname="MOP">
                    Rs. {{ number_format((float)$item->mop, 2, '.', '') }}
                </td>
                <td data-colname="Type">
                    @if ($item->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                @if ($type == 'fg')
                
                <td data-colname="Installable">
                    @if(!empty($item->is_installable))
                    <span class="badge bg-success">Yes</span>
                    @else
                    <span class="badge bg-danger">No</span>
                    @endif
                </td>
                
                
                @elseif ($type == 'sp')
                <td data-colname="Spare Type">
                    <span class="badge bg-success">{{ ucwords($item->spare_type) }}</span>
                </td>
                @endif
                <td data-colname="Status">
                    @if(!empty($item->status))
                    <span class="badge bg-success" id="proStatusBadge{{$item->id}}">Active</span>
                    @else
                    <span class="badge bg-danger" id="proStatusBadge{{$item->id}}">Inactive</span>
                    @endif
                    
                </td>
                <td data-colname="Action">
                    <a href="{{route('product.edit', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Edit</a>
                    <a href="{{route('product.copy', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">Copy</a>
                    <a href="{{route('product.show', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-primary select-md">View</a>

                    {{-- <p>Assign Goods</p> --}}
                    @if ($item->type == 'sp')
                        @php
                            $get_spare_goods_names = get_spare_goods_names($item->id);
                        @endphp
                        <a href="{{ route('product.assign-spare-goods', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md" title="{{$get_spare_goods_names}}">Assign Goods ({{count($item->spare_goods)}})</a>
                    @endif

                    @if ($item->type == 'fg')
                        <a href="{{ route('product.view-amc',  [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">AMC Offers ({{count($item->amc)}})</a>
                    @endif

                    @if ($item->type == 'fg' && in_array($item->goods_type,['general','chimney']))
                        <a href="{{ route('product.add-goods-warranty',  [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Add Warranty</a>
                        <a href="{{ route('product.list-goods-warranty',  [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Show Warranty</a>
                    @endif


                    @if(!empty($item->status))
                        <form action="{{route('product.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" method="GET">
                            <input type="hidden" name="browser_name" id="browser_name">
                            <input type="hidden" name="navigator_useragent" id="navigator_useragent">
                            <button type="submit"  class="btn btn-outline-danger select-md">Inactive</button>
                        </form>
                        
                    @else
                        <form action="{{route('product.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" method="GET">
                            <input type="hidden" name="browser_name" id="browser_name">
                            <input type="hidden" name="navigator_useragent" id="navigator_useragent">
                            <button type="submit"  class="btn btn-outline-success select-md">Active</button>
                        </form>
                        
                    @endif



                    {{-- @if(!empty($item->status))
                        <a href="{{route('product.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-danger select-md">Inactive</a>
                    @else
                        <a href="{{route('product.toggle-status', [Crypt::encrypt($item->id),Request::getQueryString()])}}" class="btn btn-outline-success select-md">Active</a>
                    @endif --}}
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
    $('#type').on('change', function(){
        $('.filter-waiting-text').text('Please wait ... ');
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

    function changeStatus(id){
        // alert(id);
        $.ajax({
            url: "{{ route('ajax.toggle-status') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id: id,
                table_name: 'products'
            },
            success: function(result) {
                console.log(result);
                console.log(result.status);
                if(result.status == 'active'){
                    $('#proStatusBadge'+id).removeClass('badge bg-danger');
                    $('#proStatusBadge'+id).addClass('badge bg-success');
                    $('#proStatusBadge'+id).text('Active');
                    $('#proStatusBtn'+id).removeClass('btn btn-outline-success select-md');
                    $('#proStatusBtn'+id).addClass('btn btn-outline-danger select-md');
                    $('#proStatusBtn'+id).text('Inactive');
                } else {
                    $('#proStatusBadge'+id).removeClass('badge bg-success');
                    $('#proStatusBadge'+id).addClass('badge bg-danger');
                    $('#proStatusBadge'+id).text('Inactive');
                    $('#proStatusBtn'+id).removeClass('btn btn-outline-danger select-md');
                    $('#proStatusBtn'+id).addClass('btn btn-outline-success select-md');
                    $('#proStatusBtn'+id).text('Active');
                }
                
            }
        });
    }
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 