@extends('layouts.app')
@section('content')
@section('page', 'Pincodes')
<section>   
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li>Pincodes</li>
    </ul>
    <div class="row">
        <form action="{{ route('service-partner.asign-pincodes',$id) }}" method="POST">
            @csrf
        <div class="row">
            <div class="col-sm-12">            
                <div class="card shadow-sm">
                    <div class="row">
                        @forelse ($data as $item)
                        @php
                            $checked = "";
                            if(in_array($item->id,$mypincodeArr)){
                                $checked = "checked";
                            }
                            $disabled = "";
                            if(in_array($item->id,$pincodeArr)){
                                $disabled = "disabled";
                            }
                        @endphp
                        <div class="col-md-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="pincodes[]" value="{{$item->id}}" id="{{$item->number}}" {{$checked}} {{$disabled}}>
                                <label class="form-check-label" for="{{$item->number}}">
                                  {{$item->number}}
                                </label>
                            </div>
                        </div> 
                        @empty
                            
                        @endforelse                                        
                    </div>  
                </div>    
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('service-partner.list')}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" class="btn btn-sm btn-success">Save </button>
                    </div>
                </div>                                       
            </div> 
        </div>                 
        </form>             
    </div>    
</section>
<script>
    
</script>
@endsection