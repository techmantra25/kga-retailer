@extends('layouts.app')
@section('content')
@if ($type == 'parent')
    @section('page', 'Category')
    @section('small', '(Class)')
@else
    @section('page', 'Sub Category')
    @section('small', '(Group)')
@endif
<section>   
    <ul class="breadcrumb_menu">      
        <li>Product Management</li>  
        @if ($type == 'parent')
        <li><a href="{{ route('category.list',['type'=>$type]) }}">Category</a> </li>
        @else
        <li><a href="{{ route('category.list',['type'=>$type]) }}">Sub Category</a> </li>
        @endif   
        
        <li>Create</li>
    </ul>
    {{-- @if($errors->any())
        {{ implode('', $errors->all('<div>:message</div>')) }}
    @endif --}}
    <div class="row">
        <form id="myForm" action="{{ route('category.store') }}" enctype="multipart/form-data" method="POST">
        @csrf
        <input type="hidden" name="type" value="{{$type}}">
        <div class="row">
            <div class="col-sm-9">            
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">Product Type <span class="text-danger">*</span></label>
                                    <select name="product_type" class="form-control" id="product_type">
                                        <option value="" hidden selected>Select an option</option>
                                        <option value="fg" @if(old('product_type') == 'fg') selected @endif>Finished Goods</option>
                                        <option value="sp" @if(old('product_type') == 'sp') selected @endif>Spare Parts</option>
                                    </select>
                                    @error('product_type') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            @if ($type == 'child')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Category <span class="text-danger">*</span></label>
                                    <select name="parent_id" class="form-control" id="">
                                        <option value="" hidden selected>Select an option</option>
                                        {{-- <option value="">Parent</option> --}}
                                        @forelse ($parents as $p)
                                        <option value="{{$p->id}}">{{$p->name}}</option>
                                        @empty
                                        <option value="">No category found</option>
                                        @endforelse
                                    </select>
                                    @error('parent_id') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
                            @else
                            {{-- <input type="hidden" name="parent_id" value=""> --}}
                            @endif
                            
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="">Name <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="name" placeholder="Please Enter Name" class="form-control" maxlength="100" value="{{old('name')}}">
                                    @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div> 
							<!-- Amc Applicable-->   
						 	<div class="col-md-4">
								<div class="form-group">
									<label class="d-block">AMC Applicable</label>
									<div class="form-check form-check-inline">
										<input class="form-check-input"
											   type="radio"
											   name="amc_applicable"
											   id="amc_yes"
											   value="1"
											   {{ old('amc_applicable', 0) == 1 ? 'checked' : '' }}>
										<label class="form-check-label" for="amc_yes">Yes</label>
									</div>

									<div class="form-check form-check-inline">
										<input class="form-check-input"
											   type="radio"
											   name="amc_applicable"
											   id="amc_no"
											   value="0"
											   {{ old('amc_applicable', 0) == 0 ? 'checked' : '' }}>
										<label class="form-check-label" for="amc_no">No</label>
									</div>
								</div>
							</div>  
                        </div>  
                    </div>
                </div>      
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Description </label>
                                    <textarea name="description" class="form-control" id="" placeholder="Please Enter Description" cols="1" rows="2">{{old('description')}}</textarea>
                                    {{-- @error('about') <p class="small text-danger">{{ $message }}</p> @enderror --}}
                                </div>
                            </div>                                                 
                        </div>
                    </div>
                </div>                                          
            </div> 
            <div class="col-sm-3">
                <div class="card shadow-sm">
                    <div class="card-header">
                        Image
                    </div>
                    <div class="card-body">
                        <div class="w-100 product__thumb">
                            <label for="thumbnail"><img id="output" src="{{url('assets')}}/images/placeholder-image.jpg"></label>
                        </div>
                        <input type="file" name="photo" id="thumbnail" accept="image/*" onchange="loadFile(event)">
                        <script>
                            var loadFile = function(event) {
                            var output = document.getElementById('output');
                            output.src = URL.createObjectURL(event.target.files[0]);
                            output.onload = function() {
                                URL.revokeObjectURL(output.src) // free memory
                            }
                            };
                        </script>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('category.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Create </button>
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