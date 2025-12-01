@extends('layouts.app')
@section('content')
@section('page', 'Invoices')
<section>
    <ul class="breadcrumb_menu">    
        <li>Service Partner Spare Order Management</li>
        <li>Service Partner Spare Invoices</li>
    </ul>
    <form action="" id="searchForm">
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                <select name="partner_type" class="form-control select-md" id="partner_type">
                    <option value="">All Types</option>
                    <option value="dealer" @if($partner_type == 'dealer') selected @endif>Dealer</option>
                    <option value="service_partner" @if($partner_type == 'service_partner') selected @endif>Service partner</option>
                </select>
            </div>  
            <div class="col-auto">
                <select name="type" class="form-control select-md" id="type">
                    <option value="">All Types</option>
                    <option value="fg" @if($type == 'fg') selected @endif>Finshed Goods</option>
                    <option value="sp" @if($type == 'sp') selected @endif>Spare Parts</option>
                </select>
            </div>  
            <div class="col-auto">
                <input type="search" autocomplete="off" name="search" id="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
            </div>
        </div>
    </div>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>            
            <div class="col-7">
                <input type="search" autocomplete="off" placeholder="Enter Item Name" onkeyup="searchProduct(this.value);"  name="product_name" maxlength="200" class="form-control select-md" id="product_name" value="{{  $product_name }}">
                <input type="hidden" name="product_id" id="product_id" value="{{ $product_id }}">
                <div class="respDropProduct" id="respDropProduct" style="position: relative;"></div>
            </div> 
        </div>
    </div>
    </form>
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
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Date</th>
                <th>SLIP NO</th>
                <th>ORDER NO</th>
                <th>To</th>
                <th>Amount</th>
                <th>View Items</th>
                <th>Item Type</th>
                <th>Action</th>
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
            @php
                $item_details = json_decode($item->item_details);
            @endphp
            <tr>
                <td>{{$i}}</td>
                <td>{{$item->invoice_no}}</td>
                <td>{{date('j M Y, l', strtotime($item->created_at))}}</td>
                <td>
                    {{$item->packingslip->slipno}}                   
                </td>
                <td>
                    <a href="{{ route('sales-order.show', Crypt::encrypt($item->sales_order_id)) }}?backtomodule=invoice&backtodestination={{Request::fullUrl()}}" class="btn btn-outline-primary select-md">{{$item->sales_order->order_no}}</a>
                </td>
                <td>                   
                    <p class="small text-muted mb-1">
                        @if (!empty($item->service_partner))
                            <strong>SERVICE PARTNER</strong> <br/>
                            <span>Company Name: <strong>  {{$item->service_partner->company_name}} </strong></span> <br/>
                            <span>Person Name: <strong> {{$item->service_partner->person_name}} </strong></span> <br/>
                            <span>Phone: <strong>  {{$item->service_partner->phone}}   </strong></span> <br/>
                            <span>Email: <strong>  {{$item->service_partner->email}}  </strong></span>
                        @else
                            <strong>DEALER</strong> <br/>
                            <span>
                                <strong>
                                    {{$item->dealer->name}}
                                </strong>
                            </span>
                        @endif
                    </p>
                </td>
                
                <td>Rs. {{ number_format((float)$item->total_amount, 2, '.', '') }}</td>
                <td>
                    
                    <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}"> View Items ({{count($item->items)}}) </button>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="prodTitle">
                                        {{$item->sales_order->order_no}} / 
                                        @if (!empty($item->sales_order->service_partner))
                                            {{$item->sales_order->service_partner->company_name}}
                                        @else
                                            {{$item->sales_order->dealer->name}}
                                        @endif
                                    </h5>
                                    
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table" id="prodHistTable">
                                            <thead>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Total Pcs</th>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $j=1;
                                                    
                                                @endphp
                                                @foreach ($item->items as $items)
                                                    <tr>
                                                        <td>{{$j}}</td>
                                                        <td>{{$items->product_title}}</td>
                                                        <td>{{$items->quantity}}</td>
                                                    </tr>
                                                @php
                                                    $j++;
                                                @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
                                </div>
                            </div>
                        </div>
                    </div>                   
                </td>
                <td>
                    @if ($item->sales_order->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('invoice.download', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Download</a>
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="9" style="text-align: center;">
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
    $('#search').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#product_name').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#product_id').val(0);
        $('#searchForm').submit();
    });
    $('#type').on('change', function(){
        $('#searchForm').submit();
    });
    $('#partner_type').on('change', function(){
        $('#searchForm').submit();
    });
    function searchProduct(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function(result) {
                    console.log(result);
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
                    $('#respDropProduct').html(content);
                }
            });
        } else {
            $('.product-dropdown').hide()
        }
        
    }

    function fetchProduct(id) {
        $('.product-dropdown').hide();
        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id
            },
            success: function(result) {
                console.log(result);
                var title = result.title;                
                $('#product_id').val(id);
                $('#product_name').val(title);
                $('#searchForm').submit();
            }
        });   
          
    }
</script>  
@endsection 