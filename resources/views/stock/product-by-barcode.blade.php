@extends('layouts.app')
@section('content')
@section('page', 'Product By Barcode')

<section>
    <form action="" id="searchForm">
        <input type="hidden" name="back_to" value="stock"> 
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (Request::get('back_to') == 'home')
                <a href="{{ route('home') }}" class="btn btn-outline-danger select-md">Back To Home</a>
                @else
                <a href="{{ route('stock.list') }}" class="btn btn-outline-danger select-md">Back To Stock Inventory</a>
                @endif
                
            </div>
            <div class="col-4">
                <input type="search" name="search" value="{{$search}}" maxlength="16" class="form-control select-md" placeholder="Please enter barcode number ...">
            </div>
            
        </div>
    </div>
    </form>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (!empty($search))
                    @if (!empty($product))
                        <span>Product Name:- <strong>{{ $product->product->title }}</strong></span>
                    @else
                        <span> No Product Found !!!</span>
                    @endif
                @endif
               
            </div>
            
            <div class="col-auto">

            </div>
        </div>
    </div>
    @if (!empty($product))
      
    <div class="row">
        <div class="col-sm-6" id="div_grn">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6><span class="badge rounded-pill bg-secondary">GRN Details</span></h6>
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Supplier : </span>
                                {{$product->stock->purchase_order->supplier->public_name}}
                            </p>
                        </div> 
                             
                        @if (!empty($stock_products)) 
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Date : </span> 
                                {{ date('d/m/Y', strtotime($stock_products->created_at)) }}
                            </p>
                        </div>               
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Rate : </span>
                                Rs. {{ number_format((float)$stock_products->cost_price, 2, '.', '') }}
                            </p>
                        </div>  
                        @endif                      
                    </div>
                </div>  
            </div>                                      
        </div>  
        <div class="col-sm-6" id="div_ps">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">  
                        <h6><span class="badge rounded-pill bg-secondary">Dispatch Details</span></h6>
                        @if (!empty($product->packingslip))
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">
                                    
                                    @if ($product->packingslip->sales_order->dealer)
                                        Dealer : 
                                        {{ $product->packingslip->sales_order->dealer->name }}
                                    @else
                                        Service Partner
                                        {{ $product->packingslip->sales_order->service_partner->person_name }}
                                    @endif
                                </span> 
                            </p>
                        </div>
                        @endif                      
                                                
                        @if (!empty($sales_order_products))
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Date : </span> 
                                {{ date('d/m/Y', strtotime($sales_order_products->created_at)) }}
                            </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Rate : </span> 
                                Rs. {{ number_format((float)$sales_order_products->product_price, 2, '.', '') }} 
                            </p>
                        </div>   
                        @endif
                                             
                    </div>
                </div>  
            </div>                                      
        </div>
        @if($kga_sales_data)
        <div class="col-sm-6" id="div_kga_sales_data">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">  
                        <h6><span class="badge rounded-pill bg-secondary">KGA Sales Details</span></h6>
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">
                                        Bill To : {{ $kga_sales_data->customer_name }},</br>
                                        Address : {{ $kga_sales_data->address }},</br>
                                        Bill Date : {{ $kga_sales_data->bill_date }},</br>
                                        Branch : <strong>{{ $kga_sales_data->branch }}</strong></br>
                                </span> 
                            </p>
                        </div>                               
                    </div>
                </div>  
            </div>                                      
        </div>
        @endif
        <!-- dap_data -->
        @if(is_countable($dap_data) && count($dap_data) > 0)
        @foreach($dap_data as $key=> $data)
        <div class="col-sm-6" id="div_dap_{{$data->id}}">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body"> 
                        <div class="d-flex justify-content-between"> 
                            <h6><span class="badge rounded-pill bg-secondary">DAP Details</span></h6>
                            <span class="badge rounded-pill bg-secondary">{{$key+1}}</span>         
                        </div>
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">
                                  
                                        Customer Name : {{ $data->customer_name }},</br>
                                        Phone : {{ $data->mobile }} | {{ $data->alternate_no }} (Alternate no),</br>
                                        Booking Date : <strong>{{ $data->entry_date }} &nbsp;&nbsp;&nbsp; ({{$data->branch->name?$data->branch->name:""}})</strong>,</br>
                                        Dispatch From Showroom : <strong>{{ \Carbon\Carbon::parse($data->is_dispatched_from_branch_date)->format('Y-m-d')}}</strong></br>
                                        Receive At Service Centre : <strong>{{ \Carbon\Carbon::parse($data->is_reached_service_centre_date)->format('Y-m-d')}}</strong></br>
                                        Assign Engineer : <strong>{{$data->servicePartner->person_name?$data->servicePartner->person_name:""}}</strong>,</br>
                                        Expencess : <strong>{{number_format($data->final_amount,2)}}</strong>,</br>
                                        Dispatch From Service Centre : <strong>{{ \Carbon\Carbon::parse($data->service_centre_dispatch_date)->format('Y-m-d')}}</strong></br>
                                        Receive At Showrrom : <strong>{{ \Carbon\Carbon::parse($data->is_received_at_branch_date)->format('Y-m-d')}}</strong></br>
                                        Delivery Date : <strong>{{ \Carbon\Carbon::parse($data->customer_delivery_time)->format('Y-m-d')}} &nbsp;&nbsp;&nbsp; ({{$data->return_branch?$data->return_branch->name:""}})</strong></br>

                                </span> 
                            </p>
                        </div>                                                                 
                    </div>
                </div>  
            </div>                                      
        </div>
        @endforeach
        @endif   
    <!-- crp_data -->
        @if(is_countable($crp_data) && count($crp_data) > 0)
        @foreach($crp_data as $key=> $data)
        <div class="col-sm-6" id="div_dap_{{$data->id}}">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body"> 
                        <div class="d-flex justify-content-between"> 
                            <h6><span class="badge rounded-pill bg-secondary text">CRP Details</span></h6>
                            <span class="badge rounded-pill bg-secondary">{{$key+1}}</span>         
                        </div>
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">
                                        Customer Name : {{ $data->customer_name }},</br>
                                        Phone : {{ $data->mobile }} | {{ $data->alternate_no }} (Alternate no),</br>
                                        Booking Date : <strong>{{ $data->entry_date }}</strong>,</br>
                                        Assign Engineer : <strong>{{$data->servicePartner->person_name?$data->servicePartner->person_name:""}}</strong>,</br>
                                        Expencess : <strong>{{number_format($data->final_amount,2)}}</strong>,</br>
                                        Repaire & closing Date : <strong>{{ \Carbon\Carbon::parse($data->closing_otp_time)->format('Y-m-d')}}</strong></br>

                                </span> 
                            </p>
                        </div>                                                                 
                    </div>
                </div>  
            </div>                                      
        </div>
        @endforeach
        @endif            
    </div>    
    @endif 
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