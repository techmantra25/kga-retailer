@extends('layouts.app')
@section('content')
@section('page', 'Generate Road Challan - DAP Service Request')
<section>
    <ul class="breadcrumb_menu">        
        <li>DAP Service Request </li>
        <li>List Bookings</li>
        <li>Generate Road Challan</li>
    </ul>    
    
    <div class="row">
        <div class="col-sm-12">
            <div id="form2">   
                @if (empty($branch_id))
                <form id="myForm" action=""  method="GET" >
                @else
                <form id="myForm" action="{{ route('dap-services.save-road-challan') }}"  method="POST">
                    @csrf
                @endif
                
                
                <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="contact_type">Branch <span class="text-danger">*</span></label>
                                <input type="text" autocomplete="off" name="branch_name" class="form-control" placeholder="Search branch where item dropped at..." onkeyup="searchBranch(this.value);" id="branch_name" value="{{ $branch_name }}" @if(!empty($branch_id)) disabled @endif>
                                <input type="hidden" name="branch_id" id="branch_id" value="{{ $branch_id }}" >
                                <div class="respBranch" id="respBranch" style="position: relative;"></div>                         
                            </div>
                        </div> 
                        @if (!empty($branch_id) && count($dap_services)>0 )
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Serice Centre <span class="text-danger">*</span></label>
                                <select name="service_centre_id" class="form-control" id="service_centre_id">Choose Service Centre
                                <option value="" hidden selected>Select One</option>
                                @forelse ($sc as $c)
                                    <option value="{{ $c['id'] }}">{{$c['name']}}</option>
                                @empty
                                    
                                @endforelse
                                </select>
                                @error('service_centre_id') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        
                        @endif
                         
                         
                                                                                              
                    </div>  
                </div>                    
                </div>  

                @if (!empty($branch_id) && count($dap_services)>0 )
                   
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for=""><span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="yes" id="challan_image" name="challan_image" @if(old('challan_image') == 'yes') checked @endif>
                                        <label class="form-check-label" for="challan_image">
                                            Road Challan Approved
                                        </label>
                                        @error('challan_image') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Challan Amount <span class="text-danger">*</span></label>
                                    <input type="text" autocomplete="off" name="amount" placeholder="Please entre challan amount" class="form-control"  id="amount" value="{{ old('amount') }}">
                                    @error('amount') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 
                @endif
                                               
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        @if (!empty($branch_id))
                            <span>Total <a href="{{ route('dap-services.list') }}?receive_type=notreceived&branch_id={{ $branch_id }}&branch_name={{ $branch_name }}" class="showdetails">{{ count($dap_services) }}</a> call requests found</span>
                        @endif
                        
                    </div>
                </div>                   
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('dap-services.list')}}" class="btn btn-outline-danger ">Back</a>
                        <a href="{{ route('dap-services.generate-road-challan') }}" class="btn btn-outline-warning ">Reset Branch</a>
                        @if (empty($branch_id))
                            <button type="submit" id="submitBtn" class="btn btn-outline-success ">Check Status </button>
                        @else
                            @php
                                $submitbtnDisabled = "";
                                if(count($dap_services)==0){
                                    $submitbtnDisabled = "disabled";
                                }
                            @endphp
                            <button type="submit" id="submitBtn" class="btn btn-success" {{$submitbtnDisabled}}>Submit </button>
                        @endif
                        
                    </div>
                </div>       
                </form>   
            
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

    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
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
        $('#myForm').submit();
    }
    
</script>
@endsection