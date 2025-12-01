@extends('layouts.app')
@section('content')
@section('page', 'Add Employee -> ' . $dealer->name)
<section>    
    <div class="row">
        <div class="col-sm-12">
            <form id="myForm" action="{{ route('dealers.dealer-employee-store',[Crypt::encrypt($id),Request::getQueryString()]) }}" method="POST">
                @csrf
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Name <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="name" placeholder="Name" class="form-control" maxlength="100" value="{{old('name')}}">
                                    @error('name') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                          
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Phone No <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="phone" placeholder="Phone" class="form-control" maxlength="10" value="{{old('phone')}}" onkeypress="validateNum(event)">
                                    @error('phone') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>   
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="">Branch <span class="text-danger">*</span> </label>
                                    <input type="text" name="branch_name" class="form-control" placeholder="Search showroom ..." onkeyup="searchBranch(this.value);" id="branch_name" value="{{ old('branch_name') }}" autocomplete="off">
                                    <input type="hidden" name="branch_id" id="branch_id" >
                                    @error('branch_name') <p class="small text-danger">{{ $message }}</p> @enderror
                                    <div class="respBranch" id="respBranch" style="position: relative;"></div>
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
                                    <label for="">Password<span class="text-danger">*</span> </label>
                                    <input type="password" autocomplete="off" name="password" placeholder="password" class="form-control" value="{{old('password')}}">
                                    @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Confirm Password<span class="text-danger">*</span> </label>
                                    <input type="password" autocomplete="off" name="cpassword" placeholder="re-enter password" class="form-control" value="{{old('cpassword')}}">
                                    @error('cpassword') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <div class="row align-items-center">
                            <div class="col-auto">
                            <a href="{{route('dealers.dealer-employee-list',[Crypt::encrypt($id),Request::getQueryString()])}}" class="btn btn-sm btn-danger">Back</a>
                            <input type="hidden" name="dealer_id" value="{{$id}}">
                            <input type="hidden" name="dealer_type" value="{{$dealer->dealer_type}}">
                        <button id="submitBtn" type="submit" class="btn btn-sm btn-success">Create </button>
                            </div>
                        </div>
                        
                      
                        
                    </div>
                </div>  
            </form>                              
        </div>            
    </div>    
</section>
<script>
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
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
        var regex = /[0-9]/; // only number
        // var regex = /[0-9]|\./; // number with point
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }
</script>
<script>
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
        $('#searchForm').submit();
    }
</script>


@endsection