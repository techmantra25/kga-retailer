@extends('layouts.app')
@section('content')
@section('page', 'Upload Pincode CSV(Customer point repair)')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('customer-point-repair.list') }}">Service Partner</a> </li>
        <li>{{$service_partner->person_name}} - {{$service_partner->company_name}}</li>
        <li>Upload Pincode CSV</li>
    </ul>
    <div class="row">
        <div class="col">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
        </div>
        <form id="myForm" action="{{ route('customer-point-repair.assign-pincode-csv') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="service_partner_id" value="{{$id}}">
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
					<div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            
                            <div class="form-group">
                                <label for="">Product Type <span class="text-danger">*</span></label>
                                <select name="product_type" class="form-control" id="">
                                    <option value="general">General</option>
                                    <option value="chimney">Chimney</option>
                                </select>
                                
                            </div>
                                                    
                        </div> 
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="">Upload CSV <span class="text-danger">*</span></label>
                                <input type="file" name="csv" 
                                accept=".csv" 
                                class="form-control" id="">
                                @error('csv') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>                        
					</div>
                    </div>  
                </div>    
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{ url('/samplecsv/pincode/sample-pincode.csv') }}" class="btn btn-outline-primary btn-sm">Download Sample CSV</a>
                        <a href="{{route('customer-point-repair.list')}}" class="btn btn-sm btn-danger">Back</a>
                        @php
                            $disabled = "";
                            if(empty($service_partner->status)){
                                $disabled = "disabled";
                            }
                        @endphp
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success" {{$disabled}}>Submit </button>
                    </div>
                </div>                                       
            </div> 
            
        </div>                 
        </form>   
        <div>
            <h6>
                Assigned Pincodes For General Goods
                @if(count($service_partner_pincodes_general) > 0) <strong>({{count($service_partner_pincodes_general)}})</strong> @endif
            </h6>
            <ul class="pincodeclass">
                @forelse ($service_partner_pincodes_general as $pincode)
                    <li>{{$pincode->number}}</li>
                @empty
                    <li> - No Pincodes Found -</li>
                @endforelse
                
            </ul>
            <h6>
                Assigned Pincodes For Chimney Goods 
                @if(count($service_partner_pincodes_chimney) > 0) <strong>({{count($service_partner_pincodes_chimney)}})</strong> @endif
            </h6>
            <ul class="pincodeclass">
                @forelse ($service_partner_pincodes_chimney as $pincode)
                    <li>{{$pincode->number}}</li>
                @empty
                    <li> - No Pincodes Found -</li>
                @endforelse
                
            </ul>
            <a href="{{ route('customer-point-repair.pincodelist', Crypt::encrypt($id)) }}" class="btn btn-outline-danger select-md">Remove Pincodes</a>
        </div>          
    </div>  
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');    
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        return true;
    });
</script>
@endsection