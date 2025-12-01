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
                            <th>Customer Detail</th>
                            <th>Item Detail</th>   
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
                            // Initialize warranty variables
                            $general_warranty = 0;
                            $comprehensive_warranty = 0;
                            $extra_warranty = 0;
                            $motor_warranty = 0;
                            $warranty_period = 0;
                            $in_warranty = 0;
                            $warranty_status = "Out of Warranty";
                            $warranty_status_class = "badge bg-danger";

                            // Fetch Goods Warranty if it exists
                            $GoodsWarranty = App\Models\GoodsWarranty::where('dealer_type', 'khosla')
                                                ->where('goods_id', $item->product_id)
                                                ->first();                            

                            if ($GoodsWarranty) {
                                $general_warranty = $GoodsWarranty->general_warranty ?: 0;
                                $comprehensive_warranty = $GoodsWarranty->comprehensive_warranty ?: 0;
                                $extra_warranty = $GoodsWarranty->extra_warranty ?: 0;
                                $motor_warranty = $GoodsWarranty->motor_warranty ?: 0;
                            }

                            $warranty_period = $general_warranty + $extra_warranty;
                            $warranty_end_date = date('Y-m-d', strtotime($item->bill_date . ' + ' . $warranty_period . ' months'));
                            $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));

                            if ($current_date < $warranty_date) {
                                $in_warranty = 1;
                                $warranty_status = "In Warranty";
                                $warranty_status_class = "badge bg-success";
                            } else {
                                if ($comprehensive_warranty > 0) {
                                    $warranty_end_date = date('Y-m-d', strtotime($item->bill_date . ' + ' . $comprehensive_warranty . ' months'));
                                    $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                                    if ($current_date < $warranty_date) {
                                        $in_warranty = 1;
                                        $warranty_status = "In Comprehensive Warranty";
                                        $warranty_status_class = "badge bg-success";
                                    }
                                } 
                                if ($motor_warranty > 0) {
                                    $warranty_end_date = date('Y-m-d', strtotime($item->bill_date . ' + ' . $motor_warranty . ' months'));
                                    $warranty_date = date('Y-m-d', strtotime($warranty_end_date . ' -1 days'));
                                    if ($current_date < $warranty_date) {
                                        $in_warranty = 1;
                                        $warranty_status = "In Motor Warranty";
                                        $warranty_status_class = "badge bg-success";
                                    }
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{$i}}</td>
                            <td>
                                <p class="small text-muted mb-1">
                                    <span>Bill No: <strong>{{$item->bill_no}}</strong></span> <br/>
                                    <span>Bill Date: <strong>{{date('d/m/Y', strtotime($item->bill_date))}}</strong></span> <br/>
                                    <span>Branch: <strong>{{$item->branch}}</strong></span>
                                </p>
                            </td>
                            <td>
                                <p class="small text-muted mb-1">
                                    <span>Name: <strong>{{$item->customer_name}}</strong></span> <br/>
                                    <span>Mobile: <strong>{{$item->mobile}}</strong></span> <br/>
                                    <span>Phone: <strong>{{$item->phone}}</strong></span> <br/>
                                    <span>Address: <strong>{{$item->address}}</strong></span> <br/>
                                </p>
                            </td>
                            <td>
                                <p class="small text-muted mb-1">
                                    <span>Serial: <strong>{{$item->serial}}</strong></span> <br/>
                                    <span>Item: <strong>{{$item->item}}</strong></span> <br/>
                                    <span>Class: <strong>{{$item->product->category->name}}</strong></span> <br/>
                                    <span>Warranty Period: <strong>{{$item->product->warranty_period}} months</strong></span> <br/>
                                    
                                </p>
                            </td>
                            <td>
                                <span class="{{$warranty_status_class}}">{{$warranty_status}}</span>
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
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <input type="hidden" name="bill_date" value="{{$item->bill_date}}">
                                        <input type="hidden" name="bill_no" value="{{$item->bill_no}}">
                                        <input type="hidden" name="barcode" value="{{$item->barcode}}">
                                        <input type="hidden" name="serial" value="{{$item->serial}}">
                                        <input type="hidden" name="barcode" value="{{$item->barcode}}">
                                        <input type="hidden" name="in_warranty" value="{{$in_warranty}}">
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