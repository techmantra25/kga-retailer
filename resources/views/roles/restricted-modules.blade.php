@extends('layouts.app')
@section('content')
@section('page', 'Restricted Modules')
<section>
    <ul class="breadcrumb_menu">
        <li>Roles</li>
        <li>{{$role->name}}</li>
        <li>Restricted Modules</li>
    </ul>
    @if (Session::has('message'))
    <div class="alert alert-success" role="alert">
        {{ Session::get('message') }}
        {{ Session::forget('message') }}
    </div>
    @endif
    
    <form id="myForm" method="POST" action="{{ route('role-management.save-restricted-modules') }}">
        @csrf
        <input type="hidden" name="role_id" value="{{ $role_id }}">
    <div class="row" id="">
        
        @foreach ($modules as $module)
        @php
            $checked = "";
            if(in_array($module->id,$restricted_modules)){
                $checked = "checked";
            }
        @endphp
        <div class="col-md-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="{{$module->id}}" id="module{{$module->id}}" name="module_ids[]" {{$checked}}>
                <label class="form-check-label" for="module{{$module->id}}">
                    {{$module->name}}
                </label>
            </div>
        </div>
        @endforeach
        
        
    </div>
    <div class="row">
        <div class="card shadow-sm">
            <div class="card-body text-end">
                <a href="{{ route('role-management.list') }}" class="btn btn-sm btn-danger">Back</a>
                <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Save </button>
            </div>
        </div>
    </div>
    </form>
    
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
   
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>  
@endsection 