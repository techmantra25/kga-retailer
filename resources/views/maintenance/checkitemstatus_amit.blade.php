@extends('layouts.app')
@section('content')
@section('page', 'Chimney Maintenance - Check Item Status')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Chimney Maintenance & Repair</li>
        <li>Check Item Status</li>        
    </ul>
    <div class="row">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        @if (Session::has('serial'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('serial') }}
        </div>
        @endif
        
        <div class="row">
            <div class="col-sm-12">
                <div id="form2">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form id="myForm" action="" method="GET">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="form-group">
                                            <label for="buyFrom">Product Buy From <strong>(Branch)</strong></label>
                                            <select class="form-control form-select" id="buyFrom" name="type" onchange="toggleDivs()">
                                                <option value="khosla" @if($type=='khosla') selected @endif>Khosla</option>
                                                <option value="non-khosla" @if($type=='non-khosla') selected @endif>Others</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-9 text-end">
                                        <a href="{{route('customer-point-repair.check-product-details')}}" class="btn btn-warning">Reset</a>
                                    </div>
                                </div>

                                <div id="non-khosla" style="display: {{$type=="non-khosla"?"block":"none"}};">
                                    <div class="row">
                                        <div class="col-md-6 col-lg-3 mb-3">
                                            <div class="form-group">
                                                <label for="">Product Serial No</label>
                                                <input type="text" autocomplete="off" placeholder="Enter Product Serial No"
                                                    name="non_khosla_serial" class="form-control" maxlength="100" value="{{ $non_khosla_serial }}">
                                                @error('non_khosla_serial') <p class="small text-danger">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="card shadow-sm">
                                                <div class="card-body text-end">
                                                    <button type="submit" class="btn btn-success">Check Product</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    
                                <div id="khosla" style="display: {{$type=="khosla"?"block":"none"}};">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-3 mb-3">
                                            <div class="form-group">
                                                <label for="contact_type">Contact Type</label>
                                                <select name="contact_type" class="form-control form-select" id="contact_type" aria-label="Default select example">
                                                    <option value="mobile" @if($contact_type=='mobile') selected @endif>Mobile</option>
                                                    <option value="phone" @if($contact_type=='phone') selected @endif>Phone</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3 mb-3">
                                            <div class="form-group">
                                                <label for="" id="contact_lable">Mobile</label>
                                                <input type="text" autocomplete="off" placeholder="Enter Customer Mobile No" id="contact" class="form-control" maxlength="10"
                                                    @if($contact_type=='mobile') value="{{$mobile}}" name="mobile" @elseif($contact_type=='phone') value="{{$phone}}" name="phone" @else name="mobile" @endif>
                                                @error('contact') <p class="small text-danger">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3 mb-3">
                                            <div class="form-group">
                                                <label for="">Bill No</label>
                                                <input type="text" autocomplete="off" placeholder="Enter Bill No" name="bill_no"
                                                    class="form-control" maxlength="100" value="{{ $bill_no }}">
                                                @error('bill_no') <p class="small text-danger">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-3 mb-3">
                                            <div class="form-group">
                                                <label for="">Product Serial No</label>
                                                <input type="text" autocomplete="off" placeholder="Enter Product Serial No" name="serial"
                                                    class="form-control" maxlength="100" value="{{ $serial }}">
                                                @error('serial') <p class="small text-danger">{{ $message }}</p> @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="card shadow-sm">
                                                <div class="card-body text-end">
                                                    <button type="submit" id="submitBtn" class="btn btn-success">Check Product</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>              
        </div>
       
        @if(isset($_GET['type']))
            @if (!empty($data) )
                <div class="row">
                    <div class="col-sm-12">
                        <h5>Product Details</h5>  
                        <div class="filter">
                            <div class="row align-items-center justify-content-between">
                                <div class="col">
                                </div>
                            </div>
                        </div>  
                        <!-- User-friendly Design for the Table -->
                        <table class="table table-bordered table-hover mt-4">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">Product Name</th>  
                                    <th class="text-center">Warranty Status</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="25%">
                                        <p class="small text-muted mb-1">
                                            <strong>{{ $data->product->title ?? 'N/A' }}</strong>
                                        </p>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <!-- Dealer Type: Khosla -->
                                            @php
                                                $khosla_data = App\Models\ProductWarranty::with('goods', 'spear_goods')->where('goods_id', $data->product_id)->where('dealer_type', 'khosla')->get();                  
                                                $nonkhosla_data = App\Models\ProductWarranty::with('goods', 'spear_goods')->where('goods_id', $data->product_id)->where('dealer_type', 'nonkhosla')->get();                  
                                            @endphp
                                            @if(count($khosla_data)>0)
                                                <div class="col-sm-{{count($nonkhosla_data)>0?'6':'12'}} mb-3">
                                                    <div class="card shadow-sm">
                                                        <div class="card-header bg-light">
                                                            <span class="badge bg-secondary">Dealer Type: Khosla</span>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-sm table-borderless">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th>Warranty Type</th>
                                                                        <th>W Period (Months)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($khosla_data as $k=>$item)
                                                                        <tr>
                                                                            <td>{{ucwords($item->warranty_type)}}
                                                                                @if($item->warranty_type=="additional")
                                                                                <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;">{{$item->additional_warranty_type==1?"Parts Chargeable":"Service Chargeable"}}</span>
                                                                                @endif
                                                                                @if($item->warranty_type=="cleaning")
                                                                                    @php
                                                                                        $GetCleaningWarranty = GetCleaningWarranty($serial);
                                                                                    @endphp
                                                                                    <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">{{$item->number_of_cleaning}}</span>
                                                                                    Left
                                                                                    <span class="badge bg-success" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">{{$item->number_of_cleaning-$GetCleaningWarranty}}</span>
                                                                                @endif
                                                                                <span class="badge bg-success">{{$item->spear_goods?$item->spear_goods->title:""}}</span>
                                                                            </td>
                                                                            <td><span class="badge bg-success">{{$item->warranty_period}}</span></td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <!-- Dealer Type: Non-Khosla -->
                                            @if(count($nonkhosla_data)>0)
                                            <div class="col-sm-{{count($khosla_data)>0?'6':'12'}} mb-3">
                                                <div class="card shadow-sm">
                                                    <div class="card-header bg-light">
                                                        <span class="badge bg-secondary">Dealer Type: Non-Khosla</span>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table table-sm table-borderless">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Warranty Type</th>
                                                                    <th>W Period (Months)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($nonkhosla_data as $k=>$item)
                                                                <tr>
                                                                    <td>{{ucwords($item->warranty_type)}}
                                                                        @if($item->warranty_type=="additional")
                                                                        <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;">{{$item->additional_warranty_type==1?"Parts Chargeable":"Service Chargeable"}}</span>
                                                                        @endif
                                                                        @if($item->warranty_type=="cleaning")
                                                                            @php
                                                                                $GetCleaningWarranty = GetCleaningWarranty($non_khosla_serial);
                                                                            @endphp
                                                                            <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">{{$item->number_of_cleaning}}</span>
                                                                            Left
                                                                            <span class="badge bg-success" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">{{$item->number_of_cleaning-$GetCleaningWarranty}}</span>
                                                                        @endif
                                                                        <span class="badge bg-success">{{$item->spear_goods?$item->spear_goods->title:""}}</span>
                                                                    </td>
                                                                    <td><span class="badge bg-success">{{$item->warranty_period}}</span></td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-end">
                            <form action="{{ route('maintenance.add-call-request') }}" method="GET">
                                <input type="hidden" name="product_name" value="{{ $data->product->title }}">
                                <input type="hidden" name="product_id" value="{{ $data->product->id }}">
                                <input type="hidden" name="product_type" value="{{ $data->product->goods_type }}">
                                <input type="hidden" name="serial" value="{{ $serial?$serial:$non_khosla_serial }}">
                                <input type="hidden" name="redirect_url" value="{{ Request::getQueryString() }}">
                                <button type="submit" class="btn btn-outline-success">Book Call</button>
                            </form> 
                        </div>
                    </div>
                </div>
            @elseif(count($khosla_data)>0) 
                @foreach($khosla_data as $value)
                <div class="row">
                    <div class="col-sm-12">
                        <h5>Product Details</h5>  
                        <div class="filter">
                            <div class="row align-items-center justify-content-between">
                                <div class="col">
                                </div>
                            </div>
                        </div>  
                        <!-- User-friendly Design for the Table -->
                        <table class="table table-bordered table-hover mt-4">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">Product Name</th>  
                                    <th class="text-center">Warranty Status</th> 
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="25%">
                                        <p class="small text-muted mb-1">
                                            <strong>{{ $value->product->title ?? 'N/A' }}</strong>
                                        </p>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <!-- Dealer Type: Khosla -->
                                            @php
                                                $khosla_data = App\Models\ProductWarranty::with('goods', 'spear_goods')->where('goods_id', $value->product_id)->where('dealer_type', 'khosla')->get();                  
                                                $nonkhosla_data = App\Models\ProductWarranty::with('goods', 'spear_goods')->where('goods_id', $value->product_id)->where('dealer_type', 'nonkhosla')->get();                  
                                            @endphp
                                            @if(count($khosla_data)>0)
                                                <div class="col-sm-{{count($nonkhosla_data)>0?'6':'12'}} mb-3">
                                                    <div class="card shadow-sm">
                                                        <div class="card-header bg-light">
                                                            <span class="badge bg-secondary">Dealer Type: Khosla</span>
                                                        </div>
                                                        <div class="card-body">
                                                            <table class="table table-sm table-borderless">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th>Warranty Type</th>
                                                                        <th>W Period (Months)</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($khosla_data as $k=>$item)
                                                                        <tr>
                                                                            <td>{{ucwords($item->warranty_type)}}
                                                                                @if($item->warranty_type=="additional")
                                                                                <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;">{{$item->additional_warranty_type==1?"Parts Chargeable":"Service Chargeable"}}</span>
                                                                                @endif
                                                                                @if($item->warranty_type=="cleaning")
                                                                                @php
                                                                                    $GetCleaningWarranty = GetCleaningWarranty($serial);
                                                                                @endphp
                                                                                <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">{{$item->number_of_cleaning}}</span>
                                                                                Left
                                                                                <span class="badge bg-success" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">{{$item->number_of_cleaning-$GetCleaningWarranty}}</span>
                                                                                @endif
                                                                                <span class="badge bg-success">{{$item->spear_goods?$item->spear_goods->title:""}}</span>
                                                                            </td>
                                                                            <td><span class="badge bg-success">{{$item->warranty_period}}</span></td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <!-- Dealer Type: Non-Khosla -->
                                            @if(count($nonkhosla_data)>0)
                                            <div class="col-sm-{{count($khosla_data)>0?'6':'12'}} mb-3">
                                                <div class="card shadow-sm">
                                                    <div class="card-header bg-light">
                                                        <span class="badge bg-secondary">Dealer Type: Non-Khosla</span>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class="table table-sm table-borderless">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th>Warranty Type</th>
                                                                    <th>W Period (Months)</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($nonkhosla_data as $k=>$item)
                                                                <tr>
                                                                    <td>{{ucwords($item->warranty_type)}}
                                                                        @if($item->warranty_type=="additional")
                                                                        <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;">{{$item->additional_warranty_type==1?"Parts Chargeable":"Service Chargeable"}}</span>
                                                                        @endif
                                                                        @if($item->warranty_type=="cleaning")
                                                                            @php
                                                                                $GetCleaningWarranty = GetCleaningWarranty($non_khosla_serial);
                                                                            @endphp
                                                                            <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">{{$item->number_of_cleaning}}</span>
                                                                            Left
                                                                            <span class="badge bg-success" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">{{$item->number_of_cleaning-$GetCleaningWarranty}}</span>
                                                                        @endif
                                                                        <span class="badge bg-success">{{$item->spear_goods?$item->spear_goods->title:""}}</span>
                                                                    </td>
                                                                    <td><span class="badge bg-success">{{$item->warranty_period}}</span></td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="text-end">
                            <form action="{{ route('customer-point-repair.add-call-request') }}" method="GET">
                                <input type="hidden" name="product_name" value="{{ $value->product->title }}">
                                <input type="hidden" name="customer_name" value="{{ $value->customer_name }}">
                                <input type="hidden" name="product_id" value="{{ $value->product_id }}">
                                <input type="hidden" name="product_type" value="{{ $value->product->goods_type }}">
                                <input type="hidden" name="serial" value="{{ $value->serial?$value->serial:$value->non_khosla_serial}}">
                                <input type="hidden" name="mobile" value="{{$value->mobile}}">
                                <input type="hidden" name="phone" value="{{$value->phone}}">
                                <input type="hidden" name="address" value="{{$value->address}}">
                                <input type="hidden" name="bill_date" value="{{$value->bill_date}}">
                                <input type="hidden" name="bill_no" value="{{$value->bill_no}}">
                                <input type="hidden" name="pincode" value="{{$value->pincode}}">
                                <input type="hidden" name="dealer_id" value=2>
                                <input type="hidden" name="dealer_type" value="khosla">
                                
                                <input type="hidden" name="redirect_url" value="{{ Request::getQueryString() }}">
                                <button type="submit" class="btn btn-outline-success">Book Call</button>
                            </form> 
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table mt-4 bg-danger">
                        <tbody>
                        <tr>
                            <td class="bg-danger text-dark">
                            <span> <strong>No record found</strong></span>
                            </td>
                        </tr>
                        </tbody>
                        </table>
                    </div>
                </div>
            @endif                          
        @endif                          
    </div>    
</section>
<script>
    $('#contact_type').on('change', function(){
        if(this.value == 'mobile'){
            $('#contact').attr('placeholder', 'Enter Customer Mobile No');
            $('#contact').attr('name', 'mobile');
            $('#contact_lable').text('Mobile');
        } else {
            $('#contact').attr('placeholder', 'Enter Customer Phone No');
            $('#contact').attr('name', 'phone');
            $('#contact_lable').text('Phone');
        }
    })

    function toggleDivs() {
        var buyFrom = document.getElementById('buyFrom').value;
        var khoslaDiv = document.getElementById('khosla');
        var nonKhoslaDiv = document.getElementById('non-khosla');

        if (buyFrom === 'khosla') {
            khoslaDiv.style.display = 'block';
            nonKhoslaDiv.style.display = 'none';
        } else if (buyFrom === 'non-khosla') {
            nonKhoslaDiv.style.display = 'block';
            khoslaDiv.style.display = 'none';
        } else {
            khoslaDiv.style.display = 'none';
            nonKhoslaDiv.style.display = 'none';
        }
    }

</script>
@endsection