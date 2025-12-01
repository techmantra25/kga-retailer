@extends('layouts.app')
@section('content')
@section('page', 'Chimney Maintenance - Check Item Status')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Chimney Maintenance & Repair Request</li>
        <li>Check Item Status</li>
        
        
    </ul>
    <div class="row">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        
        <div class="row">
            <div class="col-sm-12">
                <div id="form2">   
                    <form id="myForm" action=""  method="GET">
                    
                    <input type="hidden" name="service_type" value="{{ Request::get('service_type') }}">

                    <ul class="pincodeclass">
                        <li>Bill</li>
                    </ul>  
                    <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Bill No<span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" placeholder="Enter Bill No" name="bill_no" class="form-control" maxlength="100" id="" value="{{ $bill_no }}">        
                                    @error('bill_no') <p class="small text-danger">{{ $message }}</p> @enderror                           
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Bill Date<span class="text-danger">*</span></label>
                                    <input type="date" max="{{date('Y-m-d')}}" name="order_date" class="form-control" id="order_date" value="{{ $order_date }}">  
                                    @error('order_date') <p class="small text-danger">{{ $message }}</p> @enderror                                   
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Bill Value </label>
                                    <input type="text" autocomplete="off" placeholder="Enter Bill Value" name="product_value" class="form-control" id="" value="{{ $product_value }}"> 
                                </div>
                            </div>                                                                       
                        </div>  
                    </div>                    
                    </div>  
                    <ul class="pincodeclass">
                        <li>Product</li>
                    </ul>  
                    <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="">Name <span class="text-danger">*</span></label>
                                    <input type="hidden" name="product_id" id="product_id" value="{{ $product_id }}">
                                    <input type="hidden" name="comprehensive_warranty" id="comprehensive_warranty" value="{{ $comprehensive_warranty }}">
                                    <input type="hidden" name="comprehensive_warranty_free_services" id="comprehensive_warranty_free_services" value="{{ $comprehensive_warranty_free_services }}">
                                    <input type="hidden" name="extra_warranty" id="extra_warranty" value="{{ $extra_warranty }}">
                                    <input type="hidden" name="motor_warranty" id="motor_warranty" value="{{ $motor_warranty }}">
                                    <input type="text" autocomplete="off" placeholder="Enter Chimney Name" onkeyup="searchProduct(this.value);"  name="product_name" maxlength="200" class="form-control" id="product_name" value="{{  $product_name }}">
                                    <div class="respDropProduct" id="respDropProduct" style="position: relative;"></div>
                                    @error('product_id') <p class="small text-danger">{{ $message }}</p> @enderror   
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Serial No </label>
                                    <input type="text" autocomplete="off" placeholder="Enter Product Serial No" name="product_sl_no" maxlength="100" class="form-control" id="" value="{{ $product_sl_no }}"> 
                                    @error('product_sl_no') <p class="small text-danger">{{ $message }}</p> @enderror                                 
                                </div>
                            </div>
                        </div>                                  
                        </div>                      
                    </div>
                               
                                    
                    <div class="card shadow-sm">
                        <div class="card-body text-end">
                            <a href="{{route('maintenance.list')}}" class="btn btn-sm btn-danger">Back</a>
                            <a href="{{ route('maintenance.checkitemstatus') }}" class="btn btn-sm btn-warning">Reset</a>
                            <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Check Status </button>
                        </div>
                    </div>       
                    </form>   
                
                </div>       
                                                 
            </div>              
        </div> 
        @if ( !empty($bill_no) && !empty($order_date) && !empty($product_id) )
        <div class="row">
            <div class="col-sm-12">
                <h4>Check Status</h4>                
                <ul class="pincodeclass">
                    <li>
                        {{$chimney_service_status_text}}
                    </li>
                    @if ($maintenance_type == 'free')
                    <li>
                        {{$no_comprehensive_service_left}}
                    </li>
                    @endif
                    @if ($repairCharge)
                        <li>
                            Repair Is Chargeable
                        </li> 
                    @else 
                        <li>
                            Free Repair Available
                        </li>                                       
                    @endif
                    @if ($spareCharge)
                        <li>
                            Spare Is Chargeable
                        </li>  
                    @else
                        <li>
                            Free Spare Available
                        </li>                                       
                    @endif
                    
                    
                </ul>
                <ul class="pincodeclass">
                    <li>
                        {{$motor_service_status_text}}
                    </li>
                    @if ($motorRepairCharge)
                    <li>
                        Repair Is Chargeable
                    </li>                                        
                    @endif
                    @if ($motorSpareCharge)
                    <li>
                        Spare Is Chargeable
                    </li>                                        
                    @endif
                </ul>
                <br>
                <form action="{{ route('maintenance.add') }}" method="GET">
                    <input type="hidden" name="bill_no" value="{{$bill_no}}">
                    <input type="hidden" name="order_date" value="{{$order_date}}">
                    <input type="hidden" name="product_value" value="{{$product_value}}">
                    <input type="hidden" name="product_id" value="{{$product_id}}">
                    <input type="hidden" name="comprehensive_warranty" value="{{$comprehensive_warranty}}">
                    <input type="hidden" name="comprehensive_warranty_free_services" value="{{$comprehensive_warranty_free_services}}">
                    <input type="hidden" name="extra_warranty" value="{{$extra_warranty}}">
                    <input type="hidden" name="motor_warranty" value="{{$motor_warranty}}">
                    <input type="hidden" name="product_name" value="{{$product_name}}">
                    <input type="hidden" name="product_sl_no" value="{{$product_sl_no}}">
                    {{-- <input type="hidden" name="service_type" value="chimney"> --}}
                    <input type="hidden" name="is_spare_chargeable" value="{{ $spareCharge ? 1 : 0 }}">
                    <input type="hidden" name="is_repair_chargeable" value="{{ $repairCharge ? 1 : 0 }}">
                    <input type="hidden" name="out_of_warranty" value="{{ $outOfWarrantyChimney ? 1 : 0 }}">
                    <input type="hidden" name="maintenance_type" value="{{$maintenance_type}}">
                <button type="submit" class="btn btn-success select-md">Book A Call</button>
                </form>
                            
            </div>
        </div>   
        @endif
                          
    </div>    
</section>
<script>
    function searchProduct(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    type: 'fg',
                    cw: '1'
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

        var order_date = $('#order_date').val();

        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id,
                order_date:order_date
            },
            success: function(result) {
                console.log(result);
                var title = result.title;
                var unique_id = result.unique_id;
                

                var comprehensive_warranty = result.comprehensive_warranty;
                var comprehensive_warranty_free_services = result.comprehensive_warranty_free_services;
                var extra_warranty = result.extra_warranty;
                var motor_warranty = result.motor_warranty;
                
                
                $('#product_id').val(id);
                $('#product_name').val(title);

                $('#comprehensive_warranty').val(comprehensive_warranty);
                $('#comprehensive_warranty_free_services').val(comprehensive_warranty_free_services);
                $('#extra_warranty').val(extra_warranty);
                $('#motor_warranty').val(motor_warranty);
                
                
            }
        });   
          
    }
</script>
@endsection