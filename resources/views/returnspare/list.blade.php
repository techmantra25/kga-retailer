@extends('layouts.app')
@section('content')
@section('page', 'Sales Returned Goods & Spares')
<section>
    <ul class="breadcrumb_menu">    
        <li>Returned Spare Management</li>    
        <li>Dealer & Service Partner - Returned Goods & Spares</li>
    </ul>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                <a href="{{ route('return-spares.add') }}" class="btn btn-outline-primary select-md">Add New</a>  
            </div>
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">                    
                    
                   
                    <div class="col-auto" >
                        <select class="form-control select-md" name="type" id="type">
                            <option value="service_partner" selected> Service Partner </option>
                            <option value="dealer" @if($type == 'dealer') selected @endif> Dealer </option>
                        </select>
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
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ORDER NO <br/> / Created At</th>    
                    <th>Details</th>  
                    <th>Items</th>
                    <th>Amount</th>       
                    <th>Status</th>
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
            @php
                $details = json_decode($item->details);            
            @endphp
                <tr>
                    <td>{{$i}}</td>
                    <td>
                        <strong>{{$item->transaction_id}}</strong>  <br/>
                        {{date('j M Y, l', strtotime($item->created_at))}}                    
                    </td>
                    <td>
                       
                      
                    @if($type ==='service_partner')
                        <p class="small text-muted mb-1">
                            <strong>Service Partner</strong> <br/>
                            <span> Person Name: <strong>{{$item->service_partner->person_name}}</strong> 
                            </span> <br>
                            <span> Company Name: <strong>{{$item->service_partner->company_name}}</strong> 
                            </span> <br>
                            <span> Phone: 
                                <strong>@if(!empty($item->service_partner->phone)) {{$item->service_partner->phone}} @endif</strong>
                            </span> <br> 
                            <span> Email: 
                                <strong>@if(!empty($item->service_partner->email)) {{$item->service_partner->email}} @endif</strong>
                            </span> <br>                            
                        </p>
                    @else
                        <p class="small text-muted mb-1">
                            <strong>Dealer </strong> <br/>
                            <span> Dealer Name: <strong>{{$item->dealer->name}}</strong> 
                            </span> <br>
                            <span> Phone: 
                                <strong>@if(!empty($item->dealer->phone)) {{$item->dealer->phone}} @endif</strong>
                            </span> <br> 
                            <span> Email: 
                                <strong>@if(!empty($item->dealer->email)) {{$item->dealer->email}} @endif</strong>
                            </span> <br>                            
                        </p> 
                    @endif
                                                    
                    </td>
                   
                    <td>
                       
                        <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}"> View Items ({{ count($item->items) }})</button>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="prodTitle">
                                            {{$item->transaction_id}} / 
                                            @if($type ==='service_partner')
                                            {{$item->service_partner->company_name}}
                                            @else
                                            {{$item->dealer->name}}
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
                                                    <th>Product Price</th>
                                                    <th>Total Price</th>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $j=1;
                                                        
                                                    @endphp
                                                    @foreach ($item->items as $detail)
                                                        <tr>
                                                            <td>{{$j}}</td>
                                                            <td>{{$detail->product->title}}</td>
                                                            <td>{{$detail->quantity}}</td>
                                                            <td>Rs. {{ number_format((float)$detail->product_price, 2, '.', '') }}</td>
                                                            <td>Rs. {{ number_format((float)$detail->product_total_price, 2, '.', '') }}</td>
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
                        Rs. {{ number_format((float)$item->amount, 2, '.', '') }}
                    </td>
                    <td>
                        @if($item->status == 1)
                            <span class="badge bg-warning">Pending</span>
                        @elseif ($item->status == 2)
                            <span class="badge bg-success">Received</span>
                        @elseif ($item->status == 3)
                            <span class="badge bg-danger">Cancelled</span>
                        @endif                               
                    </td>
                    <td>
                        @if (!empty($item->is_goods_in))
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-danger">No</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('return-spares.show', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Details</a>
                        <a href="{{ route('return-spares.barcodes', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Barcodes</a>
                        @if ($item->status != 2)                        
                            
                        @endif

                        @if ($item->status != 2 && $item->status != 3)
                            <a href="{{ route('return-spares.make-grn', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" class="btn btn-outline-success select-md">Generate GRN</a>
                            <a href="{{ route('return-spares.cancel', [Crypt::encrypt($item->id),Request::getQueryString()]) }}" onclick="return confirm('Are you sure?');" class="btn btn-outline-danger select-md">Cancel</a>
                        @endif
                        
                    </td>
                </tr>
                @php
                    $i++;
                @endphp
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">
                        No data found
                    </td>
                </tr>
            @endforelse
                
            </tbody>
        </table>
    </div>
    
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
    $('#status').on('change', function(){
        $('#searchForm').submit();
    })
</script>  
@endsection 