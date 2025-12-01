@extends('layouts.app')
@section('content')
@section('page', 'Duplicate PINCODE Assignee')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>Duplicate PINCODE Assignee</li>
    </ul>
    <div class="row">
        <form id="myForm" action="{{ route('service-partner.remove-duplicate-pincode-assignee') }}" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                    <h5>Please check email id which should be removed</h5>
                    @error('service_partner_pincode_id') <p class="small text-danger">{{ $message }}</p> @enderror
                    <div class="row">
                        @forelse ($data as $item)                        
                        <div class="col-md-4" style="padding: 20px;">                            
                            <strong>
                                {{$item->number}}
                            </strong>  
                            @php
                                $service_partner_pincode_id_arr = explode(",",$item->service_partner_pincode_ids);
                            @endphp
                            @forelse ($service_partner_pincode_id_arr as $service_partner_pincode_id)
                            @php
                                $pincode = getSingleAttributeTable('service_partner_pincodes','id',$service_partner_pincode_id,'number');
                                $product_type = getSingleAttributeTable('service_partner_pincodes','id',$service_partner_pincode_id,'product_type');
                                $service_partner_id = getSingleAttributeTable('service_partner_pincodes','id',$service_partner_pincode_id,'service_partner_id');
                                $email = getSingleAttributeTable('service_partners','id',$service_partner_id,'email');
                               
                            @endphp
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input type="hidden" name="">
                                            <input class="form-check-input" type="checkbox" name="service_partner_pincode_id[]" value="{{$service_partner_pincode_id}}" id="{{$service_partner_pincode_id}}">
                                            <label class="form-check-label" for="{{$service_partner_pincode_id}}">
                                              {{$email}} <strong>({{$product_type}})</strong>
                                            </label>
                                        </div>
                                    </div> 
                                </div>
                            @empty
                                
                            @endforelse
                        </div> 
                        @empty
                        <span>No duplicate record found !!!</span>
                        @endforelse                                        
                    </div>  
                </div>    
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('service-partner.list')}}" class="btn btn-sm btn-danger">Back</a>
                        @if (count($data) > 0)
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Remove </button>
                        @else
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success disabled">Remove </button>
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
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');     
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');   
        return true;
    });
</script>
@endsection