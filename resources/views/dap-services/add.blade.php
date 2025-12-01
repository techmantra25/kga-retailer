@extends('layouts.app')
@section('content')
@section('page', 'Book A Call')
@section('small', '(Call Summary)')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Inhouse DAP Servicing</li>
        <li>Check Item Status</li>
        <li>Book A Call</li>        
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
                    <form id="myForm" action="{{ route('dap-services.store') }}"  method="POST"> 
                        @csrf
                        <input type="hidden" name="barcode" value="{{ Request::get('barcode') }}">
                        <input type="hidden" name="product_id" value="{{ Request::get('product_id') }}">
                    <ul class="pincodeclass">
                        <li>Customer</li>
                    </ul>                   
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Customer Name</label>
                                        <input type="text" name="customer_name" id="customer_name" class="form-control" maxlength="10" value="{{ Request::get('customer_name') }}" readonly>                     
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Mobile</label>
                                        <input type="text" name="mobile" id="mobile" class="form-control" maxlength="10" value="{{ Request::get('mobile') }}" readonly>                         
                                    </div>
                                </div> 
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Phone</label>
                                        <input type="text" name="phone" id="phone" class="form-control" maxlength="10" value="{{ Request::get('phone') }}" readonly>                           
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="">Address</label>
                                        <input type="text" name="" id="address" class="form-control" maxlength="10" value="{{ Request::get('address') }}" readonly>                           
                                    </div>
                                </div> 
                                
                            </div>
                            
                        </div>                    
                    </div>
                    <ul class="pincodeclass">
                        <li>Order & Item</li>
                    </ul>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Product Serial No</label>
                                        <input type="text" name="serial" id="serial" class="form-control" maxlength="10" value="{{ Request::get('serial') }}" readonly>  
                                        @error('serial') <p class="small text-danger">{{ $message }}</p> @enderror
                                        @if($repeat_call === 1)  <p class="small text-danger">Repeat Call</p> @endif             
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Class</label>
                                        <input type="text" name="class_name" id="contact" class="form-control" maxlength="10" value="{{ Request::get('class_name') }}" readonly>                     
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Item</label>
                                        <input type="text" name="item" id="item" class="form-control" maxlength="10" value="{{ Request::get('item') }}" readonly>  
                                        @error('item') <p class="small text-danger">{{ $message }}</p> @enderror                          
                                    </div>
                                </div> 
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Bill No</label>
                                        <input type="text" name="bill_no" id="bill_no" class="form-control" maxlength="10" value="{{ Request::get('bill_no') }}" readonly>                           
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">Bill Date</label>
                                        <input type="text" name="bill_date" id="bill_date" class="form-control" maxlength="10" value="{{ Request::get('bill_date') }}" readonly>                           
                                    </div>
                                </div> 
                                
                            </div>
                            
                        </div>                    
                    </div>


                    <ul class="pincodeclass">
                        <li>Warranty</li>
                    </ul>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
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
                                        @php

                                            $GoodsWarranty = App\Models\ProductWarranty::where('dealer_type', 'khosla')
                                                                ->where('goods_id', Request::get('product_id'))
                                                                ->get();                   
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
                                                    $warranty_end_date = date('Y-m-d', strtotime(Request::get('bill_date'). ' + '.$warranty_period.' months'));
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
                                        @forelse($khosla_warranty as $key => $val)
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
                                                    <span class="badge bg-success">{{ $val['warranty_period'] }}</span>
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
                                        @empty
                                            <tr>
                                                <td colspan="4">No warranty data found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>                    
                    </div>


                    <ul class="pincodeclass">
                        <li>Showroom</li>
                    </ul>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Branch / Showroom <span class="text-danger">*</span></label>                 
                                        <input type="text" name="branch_name" class="form-control" placeholder="Search branch where item dropped at..." onkeyup="searchBranch(this.value);" id="branch_name" value="{{ old('branch_name') }}">
                                        <input type="hidden" name="branch_id" id="branch_id" value="{{ old('branch_id') }}">
                                        <div class="respBranch" id="respBranch" style="position: relative;"></div>
                                        @error('branch_id') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                
                                
                            </div>
                            
                        </div>                    
                    </div>
                    <ul class="pincodeclass">
                        <li>Information</li>
                    </ul>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">Alternate Phone No <span class="text-danger">*</span></label>
                                        <input type="text" name="alternate_no" id="alternate_no" class="form-control" maxlength="10" value="{{old('alternate_no', Request::get('mobile'))}}">  
                                        @error('alternate_no') <p class="small text-danger">{{ $message }}</p> @enderror                       
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="">Issue <span class="text-danger">*</span></label>
                                        <textarea name="issue" id="issue" class="form-control" >{{ old('issue') }}</textarea>  
                                        @error('issue') <p class="small text-danger">{{ $message }}</p> @enderror                       
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>    
                    <div class="card shadow-sm">
                        <div class="card-body text-end">
                            <a href="{{route('dap-services.checkdapitemstatus')}}?{{ Request::get('redirect_url') }}" class="btn btn-danger">Back</a>
                            <button type="submit" id="submitBtn" class="btn btn-success">Submit </button>
                        </div>
                    </div>       
                    </form>   
                
                </div>       
                                                 
            </div>              
        </div> 
             
    </div>    
</section>
<script>
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');   
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>');     
        return true;
    });
    function searchBranch(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-branches') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  branch-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchBranch(${value.id},'${value.name}')">${value.name} </a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  branch-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No branch found</li></div>`;
                    }
                    $('#respBranch').html(content);
                }
            });
        } else {
            $('.branch-dropdown').hide()
        }
        
    }

    function fetchBranch(id,name) {
        $('.branch-dropdown').hide()
        $('#branch_id').val(id);
        $('#branch_name').val(name);
          
    }
</script>
@endsection