@extends('layouts.app')
@section('content')
@section('page', 'Book a Call')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Service Partner Management</li>      
        {{-- <li><a href="{{ route('product.list') }}">Product</a> </li> --}}
        <li>Book a Call</li>
    </ul>
    <div class="row">
        <form id="myForm" action="{{ route('service-partner.save-order') }}" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="entry_date" value="{{ date('Y-m-d') }}">
        <div class="row">
            <div class="col-sm-12">  
                <ul class="pincodeclass">
                    <li>Dealer</li>
                </ul>
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Dealer User <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="dealer_user_name" onkeyup="searchDealerUser(this.value);" placeholder="Search dealer user ... " name="dealer_user_name" >
                                <input type="hidden" name="dealer_user_id" id="dealer_user_id" class="">
                                
                                <div class="respDropDealer" id="respDropDealer" style="position: relative;"></div>
                                @error('dealer_user_id') <p class="small text-danger">{{ $message }}</p> @enderror                                
                            </div>
                        </div> 
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">PIN Code<span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter PIN Code" maxlength="6" name="pincode" class="form-control" id="" onkeypress="validateNum(event)">   
                                @error('pincode') <p class="small text-danger">{{ $message }}</p> @enderror                              
                            </div>
                        </div>                                              
                    </div>
                </div>
                <ul class="pincodeclass">
                    <li>Bill</li>
                </ul>  
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill No<span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Bill No" name="bill_no" class="form-control" maxlength="100" id="">        
                                @error('bill_no') <p class="small text-danger">{{ $message }}</p> @enderror                           
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill Date<span class="text-danger">*</span></label>
                                <input type="date" max="{{date('Y-m-d')}}" name="delivery_date" class="form-control" id="">  
                                @error('delivery_date') <p class="small text-danger">{{ $message }}</p> @enderror                                   
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Bill Value </label>
                                <input type="text" placeholder="Enter Bill Value" name="product_value" class="form-control" id=""> 
                            </div>
                        </div>                                                                       
                    </div>                      
                </div>
                <ul class="pincodeclass">
                    <li>End Customer</li>
                </ul>  
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Customer Full Name" name="customer_name" class="form-control" maxlength="250" id="">  
                                @error('customer_name') <p class="small text-danger">{{ $message }}</p> @enderror                                  
                            </div>
                        </div> 
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Mobile No <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Customer Mobile No" name="mobile_no" class="form-control" maxlength="10" id="">    
                                @error('mobile_no') <p class="small text-danger">{{ $message }}</p> @enderror                                
                            </div>
                        </div>                                             
                    </div>                      
                </div>
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Address<span class="text-danger">*</span></label>
                                <textarea name="address" placeholder="Enter Address" class="form-control" id="" cols="1" rows="1"></textarea>    
                                @error('address') <p class="small text-danger">{{ $message }}</p> @enderror                            
                            </div>
                        </div> 
                                                                    
                    </div>                      
                </div>
                <ul class="pincodeclass">
                    <li>Salesman</li>
                </ul>  
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="">Name<span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Salesman Name" name="salesman" class="form-control" id="">     
                                @error('salesman') <p class="small text-danger">{{ $message }}</p> @enderror                          
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Phone No <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Salesman Phone No" name="salesman_mobile_no" class="form-control" id="" maxlength="10">   
                                @error('salesman_mobile_no') <p class="small text-danger">{{ $message }}</p> @enderror                             
                            </div>
                        </div>                                             
                    </div>                      
                </div>
                <ul class="pincodeclass">
                    <li>Product</li>
                </ul>  
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="">Name <span class="text-danger">*</span></label>
                                <input type="hidden" name="product_id" id="product_id" value="">
                                <input type="text" placeholder="Enter Product Name" onkeyup="searchProduct(this.value);"  name="product_name" maxlength="200" class="form-control" id="product_name">
                                <div class="respDropProduct" id="respDropProduct" style="position: relative;"></div>
                                @error('product_name') <p class="small text-danger">{{ $message }}</p> @enderror   
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Serial No<span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Product Serial No" name="product_sl_no" maxlength="100" class="form-control" id=""> 
                                @error('product_sl_no') <p class="small text-danger">{{ $message }}</p> @enderror                                 
                            </div>
                        </div>                                  
                    </div>                      
                </div>
                <div class="card shadow-sm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Upload Bill Image </label>
                                <input type="file" name="filename" class="form-control" id="" accept="image/*">
                                @error('filename') <p class="small text-danger">{{ $message }}</p> @enderror                                 
                            </div>
                        </div>                                                         
                    </div>                      
                </div>
               
                
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{route('service-partner.upload-csv-order')}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Submit </button>
                    </div>
                </div>                                            
            </div>              
        </div>
                 
        </form>             
    </div>    
</section>
<script>
    $(document).ready(function(){
            
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');   
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>');     
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

    function searchDealerUser(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-dealer-user') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  dealer-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchDealer(${value.id},'${value.name}')">${value.name} | ${value.company_name}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  dealer-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDropDealer').html(content);
                }
            });
        } else {
            $('.dealer-dropdown').hide()
        }
        
    }

    function fetchDealer(id,name) {
        $('.dealer-dropdown').hide()
        $('#dealer_user_id').val(id);
        $('#dealer_user_name').val(name);
          
    }

    function searchProduct(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct(${value.id})">${value.title}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDropProduct').html(content);
                }
            });
        } else {
            $('.product-dropdown').hide()
        }
        
    }

    function fetchProduct(id) {
        $('.product-dropdown').hide()

        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id
            },
            success: function(result) {
                console.log(result);
                var title = result.title;
                var unique_id = result.unique_id;
                $('#product_id').val(id);
                $('#product_name').val(title)
                
            }
        });   
          
    }
</script>
@endsection