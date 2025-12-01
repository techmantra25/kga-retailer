@extends('layouts.app')
@section('content')
@section('page', strtoupper($po_type))
<section>
    <ul class="breadcrumb_menu">    
        <li>Purchase Order</li>    
        <li><a href="{{ route('purchase-order.list', ['po_type'=>$po_type]) }}">{{strtoupper($po_type)}}</a> </li>        
    </ul>
    <form action="" id="searchForm">
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col mb-2 mb-sm-0">
                <ul>
                    <li @if(empty($status)) class="active" @endif><a href="{{ route('purchase-order.list') }}">All </a></li>
                    <li @if($status == 1) class="active" @endif><a href="{{ route('purchase-order.list') }}?status=1">Pending </a></li>
                    <li @if($status == 2) class="active" @endif><a href="{{ route('purchase-order.list') }}?status=2">Received </a></li>
                    <li @if($status == 3) class="active" @endif><a href="{{ route('purchase-order.list') }}?status=3">Cancelled </a></li>
                </ul>
            </div>
            <div class="col-auto">
                @if ($po_type == 'po')
                <a href="{{route('purchase-order.add')}}" class="btn btn-outline-primary select-md">Add New</a>        
                @endif
                      
            </div>
            <div class="col-auto">
                
                    <input type="hidden" name="po_type" value="{{$po_type}}">
                    <input type="hidden" name="status" value="{{$status}}">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <select name="type" class="form-control select-md" id="type">
                            <option value="">All Types</option>
                            <option value="fg" @if($type == 'fg') selected @endif>Finished Goods</option>
                            <option value="sp" @if($type == 'sp') selected @endif>Spare Parts</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here..">
                    </div>
                </div>
                
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
                Number of rows:
            </div><div class="col-auto p-0">
                <select name="paginate" id="paginate" class="form-control select-md">
                    <option value="25" @if($paginate == 25) selected @endif>25</option>
                    <option value="50" @if($paginate == 50) selected @endif>50</option>
                    <option value="100" @if($paginate == 100) selected @endif>100</option>
                    <option value="200" @if($paginate == 200) selected @endif>200</option>
                </select>
            </div>
            
            <div class="col-auto">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
</form>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                @if ($po_type == 'po')
                <th>ORDER NO <br/> / Created At </th>
                @else
                <th>GRN NO <br/> / Created At </th>
                @endif                
                <th>Supplier</th>  
                <th>Items</th>
                <th>Type</th>  
                <th>Amount</th>    
                @if ($po_type == 'po')
                <th>Status</th>
                @endif                
                @if ($po_type == 'grn')
                <th>Stock In Type</th>
                @endif
                <th>Is Goods In</th>
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
        
            <tr>
                <td>{{$i}}</td>
                <td>                    
                    <strong>{{$item->order_no}}</strong> <br/>
                    {{date('j M Y, l', strtotime($item->created_at))}} 
                </td>                
                <td>
                    <span>{{$item->supplier->public_name}} </span>
                </td>
                <td>
                    <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}"> View Items ({{count($item->purchase_order_products)}}) </button>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="prodTitle">
                                        {{$item->order_no}} / 
                                        @if (!empty($item->supplier_id))
                                            {{$item->supplier->public_name}}
                                        
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
                                                <th>Quantity</th>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $j=1;
                                                    // $details = json_decode($item->details);
                                                @endphp
                                                @foreach ($item->purchase_order_products as $detail)
                                                    <tr>
                                                        <td>{{$j}}</td>
                                                        <td>{{$detail->product->title}}</td>
                                                        <td>{{$detail->quantity}}</td>
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
                    @if ($item->type == 'fg')
                        <span class="badge bg-dark">Finished Goods</span>
                    @else
                        <span class="badge bg-dark">Spare Parts</span>
                    @endif
                </td>
                <td>
                    Rs. {{ number_format((float)$item->amount, 2, '.', '') }}
                </td>
                @if ($po_type == 'po')
                <td>
                    @if($item->status == 1)
                        <span class="badge bg-warning">Pending</span>
                    @elseif ($item->status == 2)
                        <span class="badge bg-success">Received</span>
                    @elseif ($item->status == 3)
                        <span class="badge bg-danger">Cancelled</span>
                    @endif
                </td>
                @endif
                @if ($po_type == 'grn')
                <td>
                    <span class="badge bg-success">{{ ucwords($item->goods_in_type) }}</span>
                </td>
                @endif
                <td>
                    @if (!empty($item->is_goods_in))
                        <span class="badge bg-success">Yes</span>
                    @else
                        <span class="badge bg-danger">No</span>
                    @endif
                </td>
                <td>                    
                    @if ($item->status == 1)
                        <a href="{{ route('purchase-order.make-grn', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Generate GRN</a>
                        <a href="{{ route('purchase-order.edit', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Edit</a>
                        <a href="{{route('purchase-order.cancel', [Crypt::encrypt($item->id),Request::getQueryString()] )}}" onclick="return confirm('Are you sure want to cancel the order?');" class="btn btn-outline-danger select-md">Cancel</a>
                    @endif
                    <a href="{{ route('purchase-order.show', [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Details</a>
                    @if ($item->status != 3)
                        <a href="{{ route('purchase-order.barcodes', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Barcodes</a>
                    @endif

                    @if (count($item->archived) > 1)
                        <a href="{{ route('purchase-order.archived', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-danger select-md">Archived ({{ count($item->archived) }})</a>
                    @endif
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
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#type').on('change', function(){
        $('#searchForm').submit();
    })
    $('#paginate').on('change',function(){
        $('#searchForm').submit();
    })
</script>  
@endsection 