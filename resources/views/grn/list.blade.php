@extends('layouts.app')
@section('content')
@section('page', 'GRN')
<section>
    <ul class="breadcrumb_menu">    
        <li>Purchase Order</li>    
        <li><a href="">GRN</a> </li>        
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
                        
                    </div>
                    <div class="col-auto">
                        <input type="search" name="search" value="" class="form-control select-md" placeholder="Search here..">
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
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Date</th>
                <th>From</th>
                <th>Purpose</th>
                <th>Items</th>
                <th>Type</th>  
                <th>Amount</th>  
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
            // dd($item->products);
        @endphp
            <tr>
                <td>{{$i}}</td>
                <td>
                    {{$item->grn_no}}
                </td> 
                <td>
                    {{date('j M Y, l', strtotime($item->created_at))}} 
                </td>            
                <td>
                    @if (!empty($item->purchase_order_id) && $item->purchase_order && $item->purchase_order->supplier)
                        <span>SUPPLIER:- <strong>{{$item->purchase_order->supplier->public_name}} </strong>  </span>
                    @elseif (!empty($item->return_spare_id) && $item->return_spare && $item->return_spare->service_partner)
                        <span>SERVICE PARTNER:- <strong>{{$item->return_spare->service_partner->person_name}}  </strong> </span>
                    @endif                    
                </td>
                <td>
                    @if (!empty($item->purchase_order_id) && $item->purchase_order)
                        <span>Purchase Order  </span> <br/>
                        <span><strong>{{$item->purchase_order->order_no}}</strong></span>
                    @elseif (!empty($item->return_spare_id))
                        <span><strong>Return Spare</strong> </span>
                    @endif
                </td>
                <td>
                    <button type="button" class="btn btn-outline-success select-md" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}"> View Items ({{count($item->products)}}) </button>
                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="prodTitle">
                                        {{$item->grn_no}} / 
                                        @if (!empty($item->purchase_order) && $item->purchase_order->supplier)
                                            {{$item->purchase_order->supplier->public_name}}
                                        @elseif(!empty($item->return_spare) && $item->return_spare->service_partner)
                                            {{$item->return_spare->service_partner->person_name}}
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
                                                @endphp
                                                @foreach ($item->products as $detail)
                                                    <tr>
                                                        <td>{{$j}}</td>
                                                        <td>{{$detail->product->title}}</td>
                                                        <td>{{$detail->count}}</td>
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
                    @if (!empty($item->purchase_order))
                        @if ($item->purchase_order->type == 'fg')
                            <span class="badge bg-dark">Finished Goods</span>
                        @else
                            <span class="badge bg-dark">Spare Parts</span>
                        @endif
                    @else
                        
                        <span class="badge bg-dark">Spare Parts</span>
                        
                    @endif
                    
                </td>
                <td>
                    Rs. {{ number_format((float)$item->amount, 2, '.', '') }}
                </td>
                
                <td>                    
                    
                    <a href="{{ route('grn.show', [Crypt::encrypt($item->id),Request::getQueryString()] ) }}" class="btn btn-outline-primary select-md">Details</a>
                    
                    <a href="{{ route('grn.barcodes', Crypt::encrypt($item->id)) }}" class="btn btn-outline-primary select-md">Barcodes</a>
                    
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td>
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
</script>  
@endsection 