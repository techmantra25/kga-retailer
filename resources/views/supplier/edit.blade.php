@extends('layouts.app')
@section('content')
@section('page', 'Supplier')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('supplier.list') }}?{{$getQueryString}}">Supplier</a> </li>
        <li>Update</li>
    </ul>
    <div class="row">
        <div class="col-sm-12">
            <form id="myForm" action="{{ route('supplier.update',[$idStr,$getQueryString]) }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Name <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="name" placeholder="Please Enter Name" class="form-control" maxlength="100" value="{{$data->name}}">
                                    @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Public Name <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="public_name" placeholder="Please Enter Public Name" class="form-control" maxlength="100" value="{{$data->public_name}}">
                                    @error('public_name') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                        
                        </div>  
                    </div>
                </div>    
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Email <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="email" placeholder="Please Enter Email" class="form-control" maxlength="100" value="{{$data->email}}">
                                    @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Phone <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="phone" placeholder="Please Enter Phone" class="form-control" maxlength="10" value="{{$data->phone}}">
                                    @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" name="is_inside" type="checkbox" @if($data->is_inside == 1) value="true" @else value="false" @endif id="is_inside" @if($data->is_inside == 1) checked @endif>
                                        <label class="form-check-label" for="is_inside">Inside India</label>
                                    </div>
                                    @error('is_inside') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="">Address <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control" id="" placeholder="Please Enter Address" cols="1" rows="1">{{$data->address}}</textarea>
                                    @error('address') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>                                                 
                        </div>
                    </div>
                </div> 
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">State <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="state" placeholder="Please Enter State" class="form-control" maxlength="100" value="{{$data->state}}">
                                    @error('state') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">City <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="city" placeholder="Please Enter City" class="form-control" maxlength="100" value="{{$data->city}}">
                                    @error('city') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            <div class="col-md-4">  
                                <div class="form-group">
                                    <label for="">PIN <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="pin" placeholder="Please Enter PIN Code" class="form-control" maxlength="10" value="{{$data->pin}}">
                                    @error('pin') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('supplier.list')}}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Update </button>
                    </div>
                </div>  
            </form>                              
        </div>            
    </div>    
</section>
<script>    
    $('#is_inside').change(function(){
        cb = $(this);
        cb.val(cb.prop('checked'));
        if($('checkbox#is_inside').is(':checked')){
            $("checkbox#is_inside").val(1);  // checked
        }else{
            $("checkbox#is_inside").val(0);  // unchecked
        }            
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');        
        return true;
    });  
</script>
@endsection