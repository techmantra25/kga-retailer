@extends('layouts.app')
@section('content')
@section('page', 'Service Partner(Customer Point Repair)')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('customer-point-repair.list') }}?{{$getQueryString}}">Service Partner</a> </li>
        <li>Details</li>
    </ul> 
    <div class="row">
        <div class="col-sm-9">
            <div class="card shadow-sm">                
                <div class="card-body">
                    <div class="form-group mb-3">
                        @if ($data->type == 1)                                
                        <span > 24 * 7 </span>
                        @elseif ($data->type == 2)
                        <span > Inhouse Technician </span>
                        @elseif ($data->type == 3)                                
                        <span > Local Vendors </span>
                        @endif
                    </div> 
                    <div class="form-group mb-3">
                        @if (!empty($data->status))
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                        
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Person Name : </span>{{$data->person_name}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Company Name : </span>{{$data->company_name}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Email : </span>{{$data->email}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Phone : </span>{{$data->phone}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Aadhaar : </span>{{$data->aadhaar_no}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">PAN : </span>{{$data->pan_no}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">GST No : </span>{{$data->gst_no}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">License No : </span>{{$data->license_no}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Address : </span>{{$data->address}} </p>
                    </div> 
                    <div class="form-group mb-3"> 
                        <p><span class="text-muted">State </span> <span class="text-dark"> : {{$data->state}} </span> </p> 
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">City </span> <span class="text-dark"> : {{$data->city}} </span></p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Salary : </span>{{$data->salary}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Repair Charge : </span>{{$data->repair_charge}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Travelling Allowance : </span>{{$data->travelling_allowance}} </p>
                    </div> 
                    <div class="form-group mb-3">
                        <p><span class="text-muted">About : </span>{{$data->about}} </p>
                    </div> 
                    
                    <div class="form-group mb-3">
                        <p><span class="text-muted">PIN Codes : </span></p>
                        <ul class="pincodeclass">
                            @forelse ($data->pincodes as $pincode)
                            <li>{{$pincode->number}}</li>
                            @empty
                                
                            @endforelse
                            
                        </ul>
                    </div> 
                </div>
            </div>                                      
        </div> 
        <div class="col-sm-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="form-group mb-3">
                        <p><span class="text-muted">Photo  </p>
                        <p>
                            @if (!empty($data->photo))                        
                            <img src="{{ asset($data->photo) }}" style="height: 200px;width:100%;">
                            @else                        
                            <img src="{{url('assets')}}/images/placeholder-image.jpg" style="height: 200px;width:100%;">
                            @endif
                        </p>
                    </div> 
                </div>
            </div>
        </div>           
    </div>    
</section>
<script>
    
</script>  
@endsection 