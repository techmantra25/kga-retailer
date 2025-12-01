@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Add Repair Spare Parts')
<ul class="breadcrumb_menu">
    <li>Service Notifications</li>
    <li>Repair - {{ $repair->unique_id }}</li>
    <li>{{ $repair->product_name }}</li>
</ul>
<section>    
    <div class="row">        
        <form id="myForm" action="{{ route('servicepartnerweb.repair-spare.save',$getQueryString) }}" method="POST">
        @csrf
        <input type="hidden" name="repair_id" value="{{$repair_id}}">
        <div class="row">
            <div class="col-sm-12">                  
                <div class="card shadow-sm">
                    <h6>Item Details</h6> 
                    <div class="table-responsive order-addmore">
                        <table class="table" id="timePriceTable">
                            <thead>
                                <tr>
                                    <th>Product <span class="text-danger">*</span></th>
                                    <th>Non-broken / Broken</th>
                                    <th>Quantity <span class="text-danger">*</span></th>
                                </tr>
                            </thead>
                            <tbody> 
                                @if(old('details'))
                                @php
                                    $old_details = old('details');
                                @endphp
                                @foreach ($old_details as $key=>$details)

                                @php
                                    $getRepairSpares = getRepairSpares($repair_id,$details['product_id']);

                                    $isNew = 0;
                                    $readonlyOld = "";
                                    if(!empty($getRepairSpares)){
                                        $isNew = 1;
                                        $readonlyOld = "readonly";
                                    }
                                @endphp
                                <tr id="tr_{{$key}}" class="tr_pro">
                                    <td class="f-12" style="width: 690px;">
                                        <input type="text" class="form-control" id="product{{$key}}" onkeyup="getProducts(this.value,{{$key}});" placeholder="Search product ... " name="details[{{$key}}][product]" value="{{ old('details.'.$key.'.product') }}" {{$readonlyOld}}>
                                        <input type="hidden" name="details[{{$key}}][product_id]" id="product_id{{$key}}" class="productids" value="{{ old('details.'.$key.'.product_id') }}">

                                        <input type="hidden" name="details[{{$key}}][isNew]" value="{{$isNew}}">
                                        
                                        <div class="respDrop" id="respDrop{{$key}}"></div>
                                        @error('details.'.$key.'.product_id') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td width="20">
                                        <select name="details[{{$key}}][is_broken]" class="form-control" id="is_broken{{$key}}">
                                            <option value="0" @if('details.'.$key.'.is_broken' == 0) selected @endif>Non-broken</option>
                                            <option value="1" @if('details.'.$key.'.is_broken' == 1) selected @endif>Broken</option>
                                        </select>
                                    </td>
                                    <td width="20">
                                        <input type="text" name="details[{{$key}}][quantity]" class="form-control" id="quantity{{$key}}" onkeypress="validateNum(event)" maxlength="2" value="{{ old('details.'.$key.'.quantity') }}">
                                        @error('details.'.$key.'.quantity') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>                     
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
                                        <td class="f-12" style="width: 690px;">
                                            <input type="text" class="form-control" id="product{{$j}}" onkeyup="getProducts(this.value,{{$j}});" placeholder="Search product ... " name="details[{{$j}}][product]" value="{{ getSingleAttributeTable('products','id',$item['product_id'],'title') }}" readonly>
                                            <input type="hidden" name="details[{{$j}}][product_id]" id="product_id{{$j}}" class="productids" value="{{$item['product_id']}}">

                                            <input type="hidden" name="details[{{$j}}][isNew]" value="1">
                                            
                                        </td>
                                        <td width="20">
                                            <select name="details[{{$j}}][is_broken]" class="form-control" id="is_broken{{$j}}">
                                                <option value="0" @if($item['is_broken'] == 0) selected @endif>Non-broken</option>
                                                <option value="1" @if($item['is_broken'] == 1) selected @endif>Broken</option>
                                            </select>
                                        </td>
                                        <td width="20">
                                            <input type="text" name="details[{{$j}}][quantity]" class="form-control" id="quantity{{$j}}" value="{{ $item['quantity'] }}" onkeypress="validateNum(event)" maxlength="2">
                                        </td>                                        
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
                                    <td class="f-12" style="width: 690px;">
                                        <input type="text" class="form-control" id="product1" onkeyup="getProducts(this.value,1);" placeholder="Search product ... " name="details[1][product]" >
                                        <input type="hidden" name="details[1][product_id]" id="product_id1" class="productids">

                                        <input type="hidden" name="details[1][isNew]" value="0">
                                        
                                        <div class="respDrop" id="respDrop1"></div>
                                    </td>
                                    <td width="20">
                                        <select name="details[1][is_broken]" class="form-control" id="is_broken1">
                                            <option value="0" selected>Non-broken</option>
                                            <option value="1">Broken</option>
                                        </select>
                                    </td>
                                    <td width="20">
                                        <input type="text" name="details[1][quantity]" class="form-control" id="quantity1" onkeypress="validateNum(event)" maxlength="2" value="1">
                                    </td>
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
        </div> 
        <div class="row">
            <div class="col-sm-12">
                <h6>Requisition Spare Notes</h6>
                <div class="form-group">
                    <label for="">Notes <span class="text-danger">*</span></label>
                    <textarea name="note" class="form-control" id="req_note" cols="30" rows="10">{{ !empty($repair->req_note)?$repair->req_note->note:old('note') }}</textarea>
                    @error('note') <p class="small text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </div> 
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow-sm">
                    @if (!empty($data))
                        <div class="card-body text-end">
                            <a href="{{ route('servicepartnerweb.repair-spare.clear',[Crypt::encrypt($repair_id),$getQueryString]) }}" onclick="return confirm('Are you sure want to clear all spare?');" class="btn btn-sm btn-warning">Clear Spare Requisition</a>
                        </div>

                        <div class="card-body text-end">
                            
                            <a href="{{ route('servicepartnerweb.notification.list-repair') }}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                            <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Save </button>
                        </div>

                    @else 

                    <div class="card-body text-end">
                        
                        <a href="{{ route('servicepartnerweb.notification.list-repair') }}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Save & Get OTP </button>
                    </div>
                    @endif
                    
                    
                </div>  
                @error('spare_err_msg') <p class="small text-danger">{{ $message }}</p> @enderror    
            </div>    
        </div>                       
        </form>             
    </div>   
</section>
<script>
    var rowCount = $('#timePriceTable tbody tr').length;
    var proIdArr = [];
    $(document).ready(function(){
        
        if(rowCount == 1){
            $('#removeNew1').hide();
        }

        $('.productids').each(function(){ 
            if($(this).val() != ''){
                proIdArr.push($(this).val())
            }  
        });

        ClassicEditor.create( document.querySelector( '#req_note' ) )
        .catch( error => {
            console.error( error );
        });

    });

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

    $(document).on('click','.addNewTime',function(){
        var thisClickedBtn = $(this);    
        var toAppend = ``;     
        
        toAppend += `
        <tr id="tr_`+i+`" class="tr_pro">
            <td class="f-12" style="width: 690px;">
                <input type="text" class="form-control" id="product`+i+`" placeholder="Search product ... " onkeyup="getProducts(this.value,`+i+`);" name="details[`+i+`][product]">
                <input type="hidden" name="details[`+i+`][product_id]" id="product_id`+i+`" class="productids">

                <input type="hidden" name="details[`+i+`][isNew]" value="0">
                
                <div class="respDrop" id="respDrop`+i+`"></div>
            </td>
            <td width="20">
                <select name="details[`+i+`][is_broken]" class="form-control" id="is_broken`+i+`">
                    <option value="0" selected>Non-broken</option>
                    <option value="1">Broken</option>
                </select>
            </td>
            <td width="20">                
                <input type="text" name="details[`+i+`][quantity]" class="form-control" id="quantity`+i+`" onkeypress="validateNum(event)" maxlength="2" value="1">
            </td>            
            <td>
                <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew`+i+`">+</a>
                <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew`+i+`" onclick="removeRow(`+i+`)">X</a>
            </td>
        </tr>
        `;
        
        

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
        goods_id = "{{$repair->product_id}}";
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.get-goods-spare') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    goods_id: goods_id,
                    idnotin: proIdArr
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct('${index}',${value.spare.id})">${value.spare.title}</a>`;
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