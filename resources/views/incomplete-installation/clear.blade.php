@extends('layouts.app')
@section('content')
@section('page', 'Add Charge & Book Installation')
<section>   
    @if (!empty($service_partner_id))
    <ul class="breadcrumb_menu">       
        <li>Service Partner</li> 
        <li>
            <a href="{{ route('service-partner.show',$service_partner_id) }}">{{ getSingleAttributeTable('service_partners','id',$service_partner_id,'person_name') }}</a> 
        </li>
    </ul>
    @endif
    
    <div class="row">
        @if (!empty($service_partner_id))
        <form id="myForm" action="{{ route('incomplete-installation.save-incomplete-installation') }}" method="POST">
            @csrf
            <input type="hidden" name="service_partner_id" value="{{$service_partner_id}}">
        @else
        <form id="myForm" action="" method="GET">
        @endif
        
        <div class="row">
            <div class="col-sm-12">   
                @if (empty($service_partner_id))
                    <div class="card shadow-sm">
						<div class="card-body">
                        <div class="row">                        
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Service Partner <span class="text-danger">*</span></label>
                                    <select name="service_partner_id" class="form-control" id="">
                                        <option value="">Select Service Partner</option>
                                        @foreach ($service_partner as $sp)
                                            <option value="{{$sp->id}}">{{$sp->person_name}} - {{ $sp->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                                                          
                        </div>  
						</div>
                    </div>
                @else
                    <div class="card shadow-sm">
						<div class="card-body">
                        <h6>Item Details</h6> 
                        <div class="table-responsive order-addmore">
                            <table class="table" id="timePriceTable">
                                <thead>
                                    <tr>
                                        <th class="primary_column">Product <span class="text-danger">*</span></th>
                                        <th>Installation <span class="text-danger">*</span></th>
                                        <th>Repair <span class="text-danger">*</span></th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @if (old('details'))
                                    @php
                                        $old_details = old('details');
                                    @endphp
                                        @foreach ($old_details as $key=>$details)
                                            <tr id="tr_{{$key}}" class="tr_pro">
                                                <td class="f-12 primary_column">
                                                    <input type="text" class="form-control" id="product{{$key}}"  name="details[{{$key}}][product]" value="{{ getSingleAttributeTable('products','id',$details['product_id'],'title') }}" readonly>
                                                    <input type="hidden" name="details[{{$key}}][product_id]" id="product_id{{$key}}" value="{{$details['product_id']}}">

                                                    @error('details.'.$key.'.product_id') <p class="small text-danger">{{ $message }}</p> @enderror
													<button type="button" class="toggle_table"></button>
                                                </td>
                                                <td data-colname="Installation">
                                                    <div class="input-group ">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                Rs.
                                                            </div>
                                                        </div>
                                                        <input type="text" autocomplete="off" name="details[{{$key}}][installation]" value="{{ old('details.'.$key.'.installation') }}" class="form-control" onkeypress="validateNum(event)"  id="installation{{$key}}" style="width: 90px;">

                                                        
                                                    </div>
                                                    @error('details.'.$key.'.installation') <p class="small text-danger">{{ $message }}</p> @enderror
                                                </td>
                                                <td data-colname="Repair">
                                                    <div class="input-group ">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">
                                                                Rs.
                                                            </div>
                                                        </div>
                                                        <input type="text" autocomplete="off" name="details[{{$key}}][repair]" value="{{ old('details.'.$key.'.repair') }}" class="form-control" onkeypress="validateNum(event)"  id="repair{{$key}}" style="width: 90px;">

                                                        
                                                    </div>
                                                    @error('details.'.$key.'.repair') <p class="small text-danger">{{ $message }}</p> @enderror
                                                </td>
                                                
                                            </tr>  
                                        @endforeach

                                        
                                    @else
                                        @php
                                            $i=1;
                                        @endphp                                        
                                        @forelse ($products as $item)
                                        <tr id="tr_{{$i}}" class="tr_pro">
                                            <td class="f-12 primary_column">
                                                <input type="text" autocomplete="off" class="form-control" id="product{{$i}}"  name="details[{{$i}}][product]" value="{{ getSingleAttributeTable('products','id',$item->product_id,'title') }}" readonly>
                                                <input type="hidden" name="details[{{$i}}][product_id]" id="product_id{{$i}}" value="{{$item->product_id}}">
                                            </td>
                                            <td data-colname="Installation">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            Rs.
                                                        </div>
                                                    </div>
                                                    <input type="text" autocomplete="off" name="details[{{$i}}][installation]" class="form-control" onkeypress="validateNum(event)"  id="installation{{$i}}" style="width: 90px;">
                                                </div>
                                            </td>
                                            <td data-colname="Repair">
                                                <div class="input-group ">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            Rs.
                                                        </div>
                                                    </div>
                                                    <input type="text" autocomplete="off" name="details[{{$i}}][repair]" class="form-control" onkeypress="validateNum(event)"  id="repair{{$i}}" style="width: 90px;">
                                                </div>
                                            </td>                                            
                                        </tr> 
                                        @php
                                            $i++;
                                        @endphp 
                                        @empty
                                        <td>
                                            <td colspan="3" style="text-align: center;"> No record found</td>
                                        </td>
                                        @endforelse
                                    @endif
                                </tbody>
                            </table>
                        </div>
			</div>
                    </div>                        
                @endif                  
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        @if (!empty($service_partner_id))
                            <a href="{{ route('incomplete-installation.clear-form') }}" class="btn btn-sm btn-danger">Back</a>
                            @if (!empty($products->toArray()))
                                <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Save Charges And Book Installations </button>
                            @endif
                        @else
                            <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Next </button>                        
                        @endif                        
                    </div>
                </div>                                               
            </div>              
        </div>                         
        </form>             
    </div>    
</section>
<script>   
    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });

    function validateNum(evt) {
        var theEvent = evt || window.event;

        // Handle paste
        if (theEvent.type === 'paste') {
            key = event.clipboardData.getData('text/plain');
        } else {
        // Handle key press
            var key = theEvent.keyCode || theEvent.which;
            key = String.fromCharCode(key);
        }
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }
	
	$('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});

</script>
@endsection