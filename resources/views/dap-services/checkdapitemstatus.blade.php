@extends('layouts.app')
@section('content')
@section('page', 'Check Item Status')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Inhouse DAP Servicing</li>
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
                    
                    <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="contact_type">Select Contact Type</label>
                                    <select name="contact_type" class="form-control " id="contact_type">
                                        <option value="mobile" @if($contact_type == 'mobile') selected @endif>Mobile</option>    
                                        <option value="phone" @if($contact_type == 'phone') selected @endif>Phone</option>
                                    </select>                           
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Mobile / Phone</label>
                                    <input type="text" autocomplete="off" placeholder="Enter Customer Mobile No"  id="contact" class="form-control " maxlength="10" @if($contact_type == 'mobile') value="{{$mobile}}" name="mobile" @elseif ($contact_type == 'phone') value="{{$phone}}" name="phone" @else name="mobile" @endif>        
                                    @error('contact') <p class="small text-danger">{{ $message }}</p> @enderror                           
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Bill No</label>
                                    <input type="text" autocomplete="off" placeholder="Enter Bill No" name="bill_no" class="form-control " maxlength="100" id="" value="{{ $bill_no }}">        
                                    @error('bill_no') <p class="small text-danger">{{ $message }}</p> @enderror                           
                                </div>
                            </div> 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Product Serial No</label>
                                    <input type="text" autocomplete="off" placeholder="Enter Product Serial No" name="serial" class="form-control " maxlength="100" id="" value="{{ $serial }}">        
                                    @error('serial') <p class="small text-danger">{{ $message }}</p> @enderror                           
                                </div>
                            </div> 
                                                                                                  
                        </div>  
                    </div>                    
                    </div>  
                                                   
                                    
                    <div class="card shadow-sm">
                        <div class="card-body text-end">
                            <a href="{{route('dap-services.list')}}" class="btn btn-outline-danger ">Back To List Bookings</a>
                            <a href="{{ route('dap-services.checkdapitemstatus') }}" class="btn btn-outline-warning ">Reset Search</a>
                            <button type="submit" id="submitBtn" class="btn btn-success ">Check Status </button>
                        </div>
                    </div>       
                    </form>   
                
                </div>       
                                                 
            </div>              
        </div> 
        @if (!empty($mobile) || !empty($phone) || !empty($bill_no) || !empty($serial) )
        <div class="row">
            <div class="col-sm-12">
                <h5>Sales Records</h5>  
                <div class="filter">
                    <div class="row align-items-center justify-content-between">
                        <div class="col">

                        </div>
                        
                        {{-- <div class="col-auto">
                            <p>
                                {{count($kga_sales_data)}} Records Found Based On
                                @php
                                    $basedOnMsg = "";
                                    if(!empty($mobile)){
                                        $basedOnMsg .= "Mobile No ";
                                        if(!empty($bill_no) || !empty($serial)){
                                            $basedOnMsg .= ", ";
                                        }
                                    }
                                    if(!empty($phone)){
                                        $basedOnMsg .= "Phone No ";
                                        if(!empty($bill_no) || !empty($serial)){
                                            $basedOnMsg .= ", ";
                                        }
                                    }
                                    if(!empty($bill_no)){
                                        $basedOnMsg .= "Bill No ";
                                        if(!empty($serial)){
                                            $basedOnMsg .= ", ";
                                        }
                                    }
                                    if(!empty($serial)){
                                        $basedOnMsg .= "Serial No ";
                                    }
                                @endphp
                                {{$basedOnMsg}}
                                
                            </p>
                        </div> --}}
                    </div>
                </div>  
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Detail</th>
                            <th>Warranty Status</th> 
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $i = 1;
                        $current_date = date('Y-m-d');
                    @endphp
                    @forelse ($kga_sales_data as $item)
                    
                        @php
                        $GoodsWarranty = App\Models\ProductWarranty::where('dealer_type', 'khosla')->where('goods_id', $item->product_id)->get();  
                           
                            $khosla_warranty=[];                               
                            if ($GoodsWarranty) {
                                foreach($GoodsWarranty as $key => $value){
                                    $array = [];
                                    $array['warranty_type']=$value->warranty_type;
                                    $array['additional_warranty_type']=$value->additional_warranty_type;
                                    $array['number_of_cleaning']=$value->number_of_cleaning;
                                    $array['parts']=$value->spear_goods?$value->spear_goods->title:null;
                                    $array['dealer_type']=$value->dealer_type;
                                    if ($value->warranty_type === 'additional') {
                                        $comprehensive_warranty_period = App\Models\ProductWarranty::where('goods_id', $value->goods_id)
                                            ->where('dealer_type', 'khosla')
                                            ->where('warranty_type', 'comprehensive')
                                            ->pluck('warranty_period')
                                            ->first();
                                        $comprehensive_warranty_period = $comprehensive_warranty_period ? $comprehensive_warranty_period : 0;
                                        $array['warranty_period'] = $value->warranty_period + $comprehensive_warranty_period;
                                    } else {
                                        $array['warranty_period'] = $value->warranty_period;
                                    }
                                    $warranty_period=$array['warranty_period'];
                                    $warranty_end_date = date('Y-m-d', strtotime($item->bill_date. ' + '.$warranty_period.' months'));
                                    $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                                    $array['warranty_end_date']=date('d-m-Y',strtotime($warranty_date));
                                    if(date('Y-m-d') < $warranty_date){
                                        $array['warranty_status']="YES";
                                    }else{
                                        $array['warranty_status']="NO";
                                    }
                                    $khosla_warranty[]= $array;
                            }
                        }

                        @endphp
                        <tr>
                            <td>{{$i}}</td>
                            <td>
                                <p class="small text-muted">
                                    <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                                    <span>Bill Date: <strong>{{date('d/m/Y', strtotime($item->bill_date))}}</strong></span> <br/>
                                    <span>Branch: <strong>{{$item->branch}}</strong></span>
                                </p>
                                <p class="badge bg-secondary">Customer Detail</p>
                                <p class="small text-muted">
                                    <span>Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                                    <span>Mobile: <strong>{{$item->mobile}}</strong></span> <br/>
                                    <span>Phone: <strong>{{$item->phone}}</strong></span> <br/>
                                    <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                                </p>
                                <p class="badge bg-secondary">Item Detail</p>
                                <p class="small text-muted">
                                    <span>Serial: <strong>{{$item->serial}}</strong></span> <br/>
                                    <span>Item: <strong class="badge bg-secondary">{{$item->item}}</strong></span> <br/>
                                    
                                </p>
                            </td>
                            <td>
                                <table class="table table-sm table-borderless">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Warranty Type</th>
                                            <th>Warranty Period (Months)</th>
                                            <th>Warranty End Date</th>
                                            <th>Warranty Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($khosla_warranty as $key => $val)
                                            <tr>
                                                <td>
                                                    {{ ucfirst($val['warranty_type']) }}

                                                    @if($val['warranty_type'] === "additional")
                                                        <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;">
                                                            {{ $val['additional_warranty_type'] == 1 ? "Parts Chargeable" : "Service Chargeable" }}
                                                        </span>
                                                    @endif

                                                    @if($val['warranty_type'] === "cleaning")
                                                        <span class="badge bg-danger" style="cursor: pointer; font-size: 9px;" title="Number of cleaning">
                                                            {{ $val['number_of_cleaning'] }}
                                                        </span>
                                                    @endif

                                                    @if($val['parts'])
                                                        <span class="badge bg-success">{{ $val['parts'] }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $val['warranty_period'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $val['warranty_status'] === "YES" ? "success" : "danger" }}">
                                                        {{ $val['warranty_end_date'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $val['warranty_status'] === "YES" ? "success" : "danger" }}">
                                                        {{ $val['warranty_status'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                            <td>
                            @if ( str_contains($item->product->category->name, 'DAP') )

                                    <form action="{{ route('dap-services.add') }}">
                                        <input type="hidden" name="product_id" value="{{$item->product_id}}">
                                        <input type="hidden" name="branch_id" value="{{$item->branch_id}}">
                                        <input type="hidden" name="customer_name" value="{{$item->customer_name}}">
                                        <input type="hidden" name="mobile" value="{{$item->mobile}}">
                                        <input type="hidden" name="phone" value="{{$item->phone}}">
                                        <input type="hidden" name="address" value="{{$item->address}}">
                                        <input type="hidden" name="item" value="{{$item->item}}">
                                        <input type="hidden" name="class_name" value="{{$item->class_name}}">
                                        <input type="hidden" name="bill_date" value="{{$item->bill_date}}">
                                        <input type="hidden" name="bill_no" value="{{$item->bill_no}}">
                                        <input type="hidden" name="barcode" value="{{$item->barcode}}">
                                        <input type="hidden" name="serial" value="{{$item->serial}}">
                                        <input type="hidden" name="barcode" value="{{$item->barcode}}">
                                        <input type="hidden" name="redirect_url" value="{{ Request::getQueryString() }}">
                                        <button type="submit" class="btn btn-outline-success select-md">Book Call</button>
                                    </form>                                    
                                @else
                                    <span class="badge bg-warning">Non-Domestic Appliance</span> <br>    
                                @endif                                
                            </td>                            
                        </tr>
                        @php
                            $i++;
                        @endphp
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center;">No record found</td>
                        </tr>  
                        @endforelse
                    </tbody>
                </table>                            
            </div>
        </div>   
        @endif                          
    </div>    
</section>
<script>
    $('#contact_type').on('change', function(){
        if(this.value == 'mobile'){
            $('#contact').attr('placeholder', 'Enter Customer Mobile No');
            $('#contact').attr('name', 'mobile');
        } else {
            $('#contact').attr('placeholder', 'Enter Customer Phone No');
            $('#contact').attr('name', 'phone');
        }
    })
</script>
@endsection