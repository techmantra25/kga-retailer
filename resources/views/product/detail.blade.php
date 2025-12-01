@extends('layouts.app')
@section('content')
@section('page', $data->title)
<section>
    <ul class="breadcrumb_menu">     
        <li>Product Management</li>      
        <li><a href="{{ route('product.list') }}?{{$getQueryString}}">Product</a> </li>
        @if (!empty($data->is_test_product))
        <li>Test Product</li>
        @endif    
    </ul> 
    @if (!empty(Request::get('backtomodule')))
    <ul class="breadcrumb_menu">
        <li>
            <a href="{{Request::get('backtodestination')}}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To {{ str_replace("_"," ",ucwords(Request::get('backtomodule'))) }}
            </a>
        </li>               
    </ul>
    @else
    <ul class="breadcrumb_menu">
        <li>
            <a href="{{ route('product.list')}}?{{$getQueryString}}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To Product
            </a>
        </li>               
    </ul>
    @endif 
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <p>
                                <span class="badge bg-secondary">{{$data->unique_id}}</span>
                                @if ($data->type == 'fg')
                                <span class="badge bg-secondary">Finished Goods</span>
                                @else
                                <span class="badge bg-secondary">Spare Parts</span>
                                @endif
                                
                            </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Public Name : </span>{{$data->public_name}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">HSN Code : </span>{{$data->hsn_code}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Description : </span>{{$data->description}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Category : </span>{{$data->category->name}} </p>
                        </div> 
                        @if (!empty($data->subcat_id))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Subcategory : </span> {{$data->subcategory->name}} </p>
                        </div> 
                        @endif
                        
                        @if ($data->type == 'fg')
                        <div class="form-group mb-3">
                            <p><span class="text-muted">MOP : </span> Rs. {{ number_format((float)$data->mop, 2, '.', '') }} </p>
                        </div> 
                        @endif
                        
                        @if (!empty($data->repair_charge))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Repair Charge : </span> Rs. {{ number_format((float)$data->repair_charge, 2, '.', '') }} </p>
                        </div>
                        @endif

                        @if (!empty($data->last_po_cost_price))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Last PO Cost Price : </span> Rs. {{ number_format((float)$data->last_po_cost_price, 2, '.', '') }} </p>
                        </div>
                        @endif
                        @if (!empty($data->supplier_warranty_period))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Supplier Warranty Period : </span>{{ ucwords($data->supplier_warranty_period)}} months </p>
                        </div>
                        @endif
                         
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Warranty Status : </span>
                                @if ($data->warranty_status == 'yes')
                                <span class="badge bg-success">{{ucwords($data->warranty_status)}}</span>                                
                                @else
                                <span class="badge bg-danger">{{ucwords($data->warranty_status)}}</span>
                                @endif                                
                            </p>
                        </div> 
                        @if ($data->warranty_status == 'yes')
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Warranty Period : </span>{{ucwords($data->warranty_period)}} months </p>
                        </div> 
                        @endif
                        
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Set of Pieces : </span>{{$data->set_of_pcs}} </p>
                        </div> 
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Service Level : </span>
                                <span class="badge bg-info">{{ucwords(str_replace("_"," ",$data->service_level))}} </span> 
                                
                            </p>
                        </div> 
                        @if ($data->type == 'sp')
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Spare Type : </span>
                                <span class="badge bg-info">{{ucwords(str_replace("_"," ",$data->spare_type))}} </span> 
                                
                            </p>
                        </div> 
                        @elseif ($data->type == 'fg')
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Goods Type : </span>
                                <span class="badge bg-info">{{ucwords(str_replace("_"," ",$data->goods_type))}} </span> 
                                
                            </p>
                        </div> 
                        @endif
                        <div class="form-group mb-3">
                            <p>
                                <span class="text-muted">Installation Applicable : </span>
                                @if (!empty($data->is_installable))
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-danger">No</span>
                                @endif
                            </p>
                        </div> 
                       
                        @if (!empty($data->comprehensive_warranty))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Free Service Tenure : </span>{{ $data->comprehensive_warranty }} months </p>
                        </div> 
                        @endif
                        @if (!empty($data->comprehensive_warranty_free_services))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">No of Free Maintenances : </span>{{ $data->comprehensive_warranty_free_services }}  </p>
                        </div> 
                        @endif
                        @if (!empty($data->extra_warranty))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Additional Warranty Tenure : </span>{{ $data->extra_warranty }} months </p>
                        </div> 
                        @endif
                        @if (!empty($data->motor_warranty))
                        <div class="form-group mb-3">
                            <p><span class="text-muted">Motor Warranty Tenure : </span>{{ $data->motor_warranty }} months </p>
                        </div> 
                        @endif
                       

                        @if ($data->type == 'fg')
                        <div class="form-group mb-3">
                            <p>
                                <span class="">Available Spares : </span>
                                
                            </p>
                            @if (!empty($data->goods_spares->toArray()))

                                <ul class="pincodeclass">
                                    
                                @foreach ($data->goods_spares as $spares)
                                    <li>{{$spares->spare->title}}</li>
                                @endforeach
                                </ul>
                            @else
                                <span>No spare found !!!  </span>
                            @endif

                        </div> 
                            
                        
                            
                        @endif
                        
                    </div>
                </div>  
            </div>                                      
        </div>            
    </div>    
</section>
<script>
    
</script>  
@endsection 