@extends('layouts.app')
@section('content')
@section('page', 'Sell Amc From Service Center')

<section>
        <div class="row">
            <div class="col-sm-12">
                <div id="form2">   
                    <form id="myForm" action=""  method="GET">
                    
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Mobile / Phone</label>
                                        <input type="text" autocomplete="off" placeholder="Enter Customer Mobile/Phone No"  id="contact_no" class="form-control " maxlength="10" name="contact_no" value="{{old('contact_no', $contact_no) }}">        
                                        @error('contact_no') <p class="small text-danger">{{ $message }}</p> @enderror                            
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Bill No</label>
                                        <input type="text" autocomplete="off" placeholder="Enter Bill No" name="bill_no" class="form-control " maxlength="100" id="" value="{{ old('bill_no',$bill_no) }}">        
                                        @error('bill_no') <p class="small text-danger">{{ $message }}</p> @enderror                           
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Product Serial No</label>
                                        <input type="text" autocomplete="off" placeholder="Enter Product Serial No" name="serial" class="form-control " maxlength="100" id="" value="{{ old('serial',$serial) }}">        
                                        @error('serial') <p class="small text-danger">{{ $message }}</p> @enderror                           
                                    </div>
                                </div> 
                                                                                                    
                            </div>  
                            <div class="card-body text-end">
                                <a href="{{ route('amc.add') }}" class="btn btn-outline-warning ">Reset Search</a>
                                <button type="submit" id="submitBtn" class="btn btn-success "> Check </button>
                            </div>      
                        </form> 
                        </div>                    
                    </div>  
                                                              
                </div>                                      
            </div>              
        </div> 
        @if(count($kga_sales_data)>0)
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm">

                    <table class="table table-sm table-borderless">
                        <thead class="thead-light">
                            <tr>
                                <th class="sr_no">#</th>
                                <th class="sr_no">KGA-Sales-Id</th>
                                <th>Serial no</th>
                                <th>Customer Details</th>
                                <th>Amc details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kga_sales_data as $item)

                            @php
                                $AmcSubscriptionSerials = App\Models\AmcSubscription::groupBy('serial')->pluck('serial')->toArray();
                            @endphp

                            <tr>
                                <td class="sr_no">{{$loop->iteration}}</td>
                                <td class="sr_no">{{$item->id}}</td>
                                <td class="sr_no">{{$item->serial}}</td>
                                <td>
                                    <p class="small text-muted mb-1">
                                        <span>Name: <strong>{{ $item->customer_name}}</strong></span> <br/>
                                        <span>Mobile: <strong>{{ $item->mobile}}</strong></span> <br/>
                                        <span>Phone: <strong>{{ $item->phone}}</strong></span> <br/>
                                        <span>Address: <strong>{{ $item->address}}</strong></span> <br/>
                                        <span>Pin: <strong>{{ $item->pincode}}</strong></span> <br/>
                                        <span>Product Name: <strong>{{ $item->item}}</strong></span> <br/>
                                        <span>Bill No: <strong>{{ $item->bill_no}}</strong></span> <br/>
                                        <span>Bill date: <strong>{{ $item->bill_date}}</strong></span> <br/>

                                    </p>
                                </td>
                                <td>
                                @if(!in_array($item->serial, $AmcSubscriptionSerials))
                                    <a href="{{ route('amc.amc-by-product',[$item->id, Crypt::encrypt($item->product_id)])  }}" class="btn btn-outline-primary select-md">View Amc Plan</a>
                                @else
                                    @php
                                        $subscription = $item->AmcSubscription->first();
                                        $amc_data = App\Models\ProductAmc::find($subscription->amc_id);
                                        $plan_data = App\Models\AmcPlanType::find($amc_data->plan_id);
                                    @endphp
                                    <p class="small text-muted mb-1">
                                        <span>Plan Name: <strong>{{ $plan_data->name }}</strong></span> <br/>
                                        <span>Duration: <strong>{{ $amc_data->duration }}</strong></span> <br/>
                                        <span>Purchase Date: <strong>{{ $subscription->purchase_date }}</strong></span> <br/>
                                        <span>Actual amount: <strong>{{ $subscription->actual_amount }}</strong></span> <br/>
                                        <span>Discount: <strong>{{ $subscription->discount }} %</strong></span> <br/>
                                        <span>Purchase amount: <strong>{{ $subscription->purchase_amount }}</strong></span> <br/>
                                        <span>Amc Start Date: <strong>{{ $subscription->amc_start_date }}</strong></span> <br/>
                                        <span>Amc End Date: <strong>{{ $subscription->amc_end_date }}</strong></span> <br/>
                                    </p>
                                @endif
                                </td>

                            </tr>

                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center;">
                                        No data found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
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
