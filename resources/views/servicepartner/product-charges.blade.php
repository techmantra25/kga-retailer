@extends('layouts.app')
@section('content')
@section('page', 'Add Charges')
<section>   
    <ul class="breadcrumb_menu">
        <li><a href="{{ route('service-partner.list') }}">Service Partner</a> </li>
        <li><a href="{{ route('service-partner.show',$service_partner_idStr) }}">{{$person_name}}</a></li>
        <li>Add Charges</li>
    </ul>
    <div class="row">
        <form id="searchForm" action="{{ route('service-partner.add-charges',$service_partner_idStr) }}" method="GET">
            
            <div class="row">
                <div class="col-sm-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <label for="">Goods Type</label>
                            <select name="goods_type" class="form-control" id="goods_type">
                                <option value="" hidden selected>Choose Type</option>
                                <option value="general" @if($goods_type == 'general') selected @endif>General</option>
                                <option value="chimney" @if($goods_type == 'chimney') selected @endif>Chimney</option>
                                <option value="gas_stove" @if($goods_type == 'gas_stove') selected @endif>Gas Stove</option>
                                <option value="ac" @if($goods_type == 'ac') selected @endif>Ac</option>
								<option value="gieger" @if($goods_type == 'gieger') selected @endif>Geyser</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @if (!empty($goods_type))
      
    <div class="row" id="">    
        
        <form id="myForm" action="{{ route('service-partner.save-charges',[$service_partner_idStr,$getQueryString]) }}" method="POST">
            @csrf
       
        
        <input type="hidden" name="service_partner_id" value="{{$id}}">
        <input type="hidden" name="goods_type" value="{{$goods_type}}">


        <input type="hidden" name="browser_name" id="browser_name">
        <input type="hidden" name="navigator_useragent" id="navigator_useragent">

        <div class="row">
            
        </div>
        <div class="row">
            <div class="col-sm-12">  
                <div class="card shadow-sm">
					<div class="card-body">
                    <h6>Item Details</h6> 
                    <div class="table-responsive order-addmore">
                        <table class="table" id="timePriceTable">
                            <thead>
                                <tr>
                                    <th>Product <span class="text-danger">*</span></th>
                                    <th>Installation <span class="text-danger">*</span></th>
                                    <th>Repair <span class="text-danger">*</span></th>
                                    @if ($goods_type !== 'general')
                                    <th>Normal Cleaning <span class="text-danger">*</span></th>
									 <th>Deep Cleaning <span class="text-danger">*</span></th>
                                    @endif
                                    
                                </tr>
                            </thead>
                            <tbody> 
                                @if(old('details'))
                                @php
                                    $old_details = old('details');
                                @endphp
                                @foreach ($old_details as $key=>$details)

                                @php
                                    $getServicePartnerProductCharges = getServicePartnerProductCharges($id,$details['product_id']);

                                    $isNew = 0;
                                    $readonlyOld = "";
                                    if(!empty($getServicePartnerProductCharges)){
                                        $isNew = 1;
                                        $readonlyOld = "readonly";
                                    }
                                @endphp
                                <tr id="tr_{{$key}}" class="tr_pro">
                                    <td class="f-12">
                                        <input type="text" class="form-control" id="product{{$key}}" onkeyup="getProducts(this.value,{{$key}});" placeholder="Search product ... " name="details[{{$key}}][product]" value="{{ old('details.'.$key.'.product') }}" {{$readonlyOld}}>
                                        <input type="hidden" name="details[{{$key}}][product_id]" id="product_id{{$key}}" class="productids" value="{{ old('details.'.$key.'.product_id') }}">

                                        <input type="hidden" name="details[{{$key}}][isNew]" value="{{$isNew}}">
                                        
                                        <div class="respDrop" id="respDrop{{$key}}"></div>
                                        @error('details.'.$key.'.product_id') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[{{$key}}][installation]" class="form-control" id="installation{{$key}}" value="{{ old('details.'.$key.'.installation') }}" onkeypress="validateNum(event)" >
                                            
                                        </div>
                                        @error('details.'.$key.'.installation') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>  
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[{{$key}}][repair]" class="form-control" id="repair{{$key}}" value="{{ old('details.'.$key.'.repair') }}" onkeypress="validateNum(event)" >
                                            
                                        </div>
                                        @error('details.'.$key.'.repair') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>  
                                    @if ($goods_type !== 'general')
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[{{$key}}][cleaning]" class="form-control" id="cleaning{{$key}}" value="{{ old('details.'.$key.'.cleaning') }}" onkeypress="validateNum(event)" >
                                            
                                        </div>
                                        @error('details.'.$key.'.cleaning') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>  
									<!-- Deep Cleaning -->
									 <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[{{$key}}][deep_cleaning]" class="form-control" id="deep_cleaning{{$key}}" value="{{ old('details.'.$key.'.deep_cleaning') }}" onkeypress="validateNum(event)" >
                                            
                                        </div>
                                        @error('details.'.$key.'.deep_cleaning') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>  
                                    @endif
                                    <td>
                                        <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew{{$key}}">+</a>
                                        <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew{{$key}}" onclick="removeRow({{$key}})">X</a>
                                    </td>
                                </tr> 
                                @endforeach
                                @else

                                @if(!empty($data))
                                    @php 
                                        $j = 1;
                                    @endphp
                                    @foreach($data as $item)

                                    
                                    <tr id="tr_{{$j}}" class="tr_pro">
                                        <td class="f-12">
                                            <input type="text" class="form-control" id="product{{$j}}" onkeyup="getProducts(this.value,{{$j}});" placeholder="Search product ... " name="details[{{$j}}][product]" value="{{ getSingleAttributeTable('products','id',$item['product_id'],'title') }}" readonly>
                                            <input type="hidden" name="details[{{$j}}][product_id]" id="product_id{{$j}}" class="productids" value="{{$item['product_id']}}">

                                            <input type="hidden" name="details[{{$j}}][isNew]" value="1">
                                            
                                        </td>
                                        <td>
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        Rs.
                                                    </div>
                                                </div>
                                                <input type="text" name="details[{{$j}}][installation]" class="form-control" value="{{$item['installation']}}" onkeypress="validateNum(event)"  id="installation{{$j}}" style="width: 90px;">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        Rs.
                                                    </div>
                                                </div>
                                                <input type="text" name="details[{{$j}}][repair]" class="form-control" onkeypress="validateNum(event)"  id="repair{{$j}}" value="{{$item['repair']}}" style="width: 90px;">
                                            </div>
                                        </td>
                                        @if ($goods_type !== 'general')
                                        <td>
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        Rs.
                                                    </div>
                                                </div>
                                                <input type="text" name="details[{{$j}}][cleaning]" class="form-control" onkeypress="validateNum(event)"  id="cleaning{{$j}}" value="{{$item['cleaning']}}" style="width: 90px;">
                                            </div>
                                        </td>
										<!--Deep Cleaning-->
										 <td>
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        Rs.
                                                    </div>
                                                </div>
                                                <input type="text" name="details[{{$j}}][deep_cleaning]" class="form-control" onkeypress="validateNum(event)"  id="deep_cleaning{{$j}}" value="{{$item['deep_cleaning']}}" style="width: 90px;">
                                            </div>
                                        </td>
                                        @endif
                                        
                                        <td>
                                            <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew{{$j}}">+</a>
                                            <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew{{$j}}" onclick="removeRow({{$j}})">X</a>
                                        </td>
                                    </tr>  
                                    @php
                                        $j++;
                                    @endphp
                                    @endforeach
                                @else
                                <tr id="tr_1" class="tr_pro">
                                    <td class="f-12">
                                        <input type="text" class="form-control" id="product1" onkeyup="getProducts(this.value,1);" placeholder="Search product ... " name="details[1][product]" >
                                        <input type="hidden" name="details[1][product_id]" id="product_id1" class="productids">

                                        <input type="hidden" name="details[1][isNew]" value="0">
                                        
                                        <div class="respDrop" id="respDrop1"></div>
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[1][installation]" class="form-control" onkeypress="validateNum(event)"  id="installation1" style="width: 90px;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[1][repair]" class="form-control" onkeypress="validateNum(event)"  id="repair1" style="width: 90px;">
                                        </div>
                                    </td>
                                    @if ($goods_type !== 'general')
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[1][cleaning]" class="form-control" onkeypress="validateNum(event)"  id="cleaning1" style="width: 90px;">
                                        </div>
                                    </td>
									<!--Deep Cleaning-->
									<td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" name="details[1][deep_cleaning]" class="form-control" onkeypress="validateNum(event)"  id="deep_cleaning1" style="width: 90px;">
                                        </div>
                                    </td>
                                    @endif
                                    
                                    <td>
                                        <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew1">+</a>
                                        <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew1" onclick="removeRow(1)">X</a>
                                    </td>
                                </tr>  
                                @endif

                                
                                @endif                       
                                                          
                            </tbody>
                        </table>
                    </div>
					</div>
                </div>                
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{ route('service-partner.list') }}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Save </button>
                    </div>
                </div>  
                                                                     
            </div>              
        </div>                         
        </form>             
    </div>     
    @endif 
</section>
<script>
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        var navigator_useragent = navigator.userAgent;
        $('#navigator_useragent').val(navigator_useragent);
        if (test(/opr\//i) || !!window.opr) {
            return 'Opera';
        } else if (test(/edg/i)) {
            return 'Microsoft Edge';
        } else if (test(/chrome|chromium|crios/i)) {
            return 'Google Chrome';
        } else if (test(/firefox|fxios/i)) {
            return 'Mozilla Firefox';
        } else if (test(/safari/i)) {
            return 'Apple Safari';
        } else if (test(/trident/i)) {
            return 'Microsoft Internet Explorer';
        } else if (test(/ucbrowser/i)) {
            return 'UC Browser';
        } else if (test(/samsungbrowser/i)) {
            return 'Samsung Browser';
        } else {
            return 'Unknown browser';
        }
    }
    const browserType = getBrowserType();
    console.log(browserType);
    $('#browser_name').val(browserType);


    var rowCount = $('#timePriceTable tbody tr').length;
    var proIdArr = [];
    
    $(document).ready(function(){  
        // alert(rowCount)
        if(rowCount == 1){
            $('#removeNew1').hide();
        }

        $('.productids').each(function(){ 
            if($(this).val() != ''){
                proIdArr.push($(this).val())
            }  
        });
        
        
    });

    $('#goods_type').on('change', function(){
        var goods_type = this.value;
        $('#searchForm').submit();
        
    })

    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });

    @if (empty($data))
        var i = 2;
    @else
        var totalDetails = "{{count($data)}}";
        totalDetails = parseInt(totalDetails);
        var i = totalDetails +1;
    @endif

    
    @if (old('details'))
        // {{count(old('details'))}}          
        @foreach($old_details as $key=>$details)
            var totalDetails = "{{$key}}";
        @endforeach        
        // var totalDetails = "{{count(old('details'))}}"; 
        totalDetails = parseInt(totalDetails)    
        console.log('totalDetails:- '+totalDetails);
        i = totalDetails+1;   
        
    @endif

    console.log('index:- '+i);


    $(document).on('click','.addNewTime',function(){

        var goods_type = "{{ $goods_type }}";
        // alert(goods_type)

        var thisClickedBtn = $(this);    
        var toAppend = ``;  
        
        if(goods_type == 'general'){
            toAppend += `
            <tr id="tr_`+i+`" class="tr_pro">
                <td class="f-12">
                    <input type="text" class="form-control" id="product`+i+`" placeholder="Search product ... " onkeyup="getProducts(this.value,`+i+`);" name="details[`+i+`][product]">
                    <input type="hidden" name="details[`+i+`][product_id]" id="product_id`+i+`" class="productids">

                    <input type="hidden" name="details[`+i+`][isNew]" value="0">
                    
                    <div class="respDrop" id="respDrop`+i+`"></div>
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" name="details[`+i+`][installation]" class="form-control"  id="installation`+i+`" style="width: 90px;" onkeypress="validateNum(event)" >
                    </div>
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" name="details[`+i+`][repair]" class="form-control"  id="repair`+i+`" style="width: 90px;" onkeypress="validateNum(event)" >
                    </div>
                </td>

                                
                <td>
                    <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew`+i+`">+</a>
                    <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew`+i+`" onclick="removeRow(`+i+`)">X</a>
                </td>
            </tr>
            `;
        } else if (goods_type !== 'general') {
            toAppend += `
            <tr id="tr_`+i+`" class="tr_pro">
                <td class="f-12">
                    <input type="text" class="form-control" id="product`+i+`" placeholder="Search product ... " onkeyup="getProducts(this.value,`+i+`);" name="details[`+i+`][product]">
                    <input type="hidden" name="details[`+i+`][product_id]" id="product_id`+i+`" class="productids">

                    <input type="hidden" name="details[`+i+`][isNew]" value="0">
                    
                    <div class="respDrop" id="respDrop`+i+`"></div>
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" name="details[`+i+`][installation]" class="form-control"  id="installation`+i+`" style="width: 90px;" onkeypress="validateNum(event)" >
                    </div>
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" name="details[`+i+`][repair]" class="form-control"  id="repair`+i+`" style="width: 90px;" onkeypress="validateNum(event)" >
                    </div>
                </td>
                <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" name="details[`+i+`][cleaning]" class="form-control"  id="cleaning`+i+`" style="width: 90px;" onkeypress="validateNum(event)" >
                    </div>
                </td>
                 <td>                
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" name="details[`+i+`][deep_cleaning]" class="form-control"  id="deep_cleaning`+i+`" style="width: 90px;" onkeypress="validateNum(event)" >
                    </div>
                </td>
                <td>
                    <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew`+i+`">+</a>
                    <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew`+i+`" onclick="removeRow(`+i+`)">X</a>
                </td>
            </tr>
            `;
        }
        
        
        
        

        $('#timePriceTable tbody').append(toAppend);
        i++;
    });
    
    function removeRow(i){
        var count_tr_pro = $('.tr_pro').length; 
        console.log(count_tr_pro);  
        if(count_tr_pro > 1){    
            var proId = $('#product_id'+i).val();                        
            proIdArr =  proIdArr.filter(e => e!=proId)
            // alert(proIdArr)        
            $('#tr_'+i).remove();
        }        
    }

    function getProducts(search,index){
        var goods_type = $('#goods_type').val();
        console.log(goods_type);
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    type: 'fg',
                    goods_type: goods_type,
                    idnotin: proIdArr
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct('${index}',${value.id})">${value.title}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDrop'+index).html(content);
                }
            });
        } else {
            $('.product-dropdown').hide()
        }
        
    }

    function fetchProduct(count,id) {
        $('.product-dropdown').hide()

        $.ajax({
            url: "{{ route('ajax.get-single-product') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                id:id
            },
            success: function(result) {
                // console.log(result);
                var title = result.title;
                var unique_id = result.unique_id;
                $('#product'+count).val(title);
                $('#product_id'+count).val(id);
                                    
                $('#removeNew'+count).show();  
                proIdArr.push(id);
                // alert(proIdArr) 
                
            }
        });             
    }

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
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

</script>
@endsection