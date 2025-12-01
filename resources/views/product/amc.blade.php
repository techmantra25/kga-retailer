@extends('layouts.app')
@section('content')
@section('page', 'AMC Details')
<section>   
    <ul class="breadcrumb_menu">
        <li>Product Management </li>
        <li><a href="{{ route('product.show',$idStr) }}">{{$product->unique_id}}: {{ $product->title }}</a></li>
        <li>AMC Details</li>
    </ul>
    <ul class="breadcrumb_menu">   
        <li><a href="{{ route('product.list') }}?{{$getQueryString}}"><i class="fi-br-arrow-alt-circle-left"></i> Back To Product</a></li>   
    </ul>
    @error('finalErrMsg') <p class="small text-danger">{{ $message }}</p> @enderror
    
    <div class="row" id="">    
        
        <form id="myForm" action="{{ route('product.save-amc',[$idStr,$getQueryString]) }}" method="POST">
            @csrf  
            <input type="hidden" name="browser_name" id="browser_name">
            <input type="hidden" name="navigator_useragent" id="navigator_useragent">     
        <div class="row">
            
        </div>
        <div class="row">
            <div class="col-sm-12">  
                <div class="card shadow-sm">
					<div class="card-body">
                    <h6>Details</h6> 
                    <div class="table-responsive order-addmore">
                        <table class="table" id="timePriceTable">
                            <thead>
                                <tr>
                                    <th>Time Period <span class="text-danger">*</span></th>
                                    <th>Amount <span class="text-danger">*</span></th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @if(old('details'))
                                    @php
                                        $old_details = old('details');
                                    @endphp
                                @foreach ($old_details as $key=>$details)

                               @php
									$disabled = "";
									$getAmcDuration = getAmcDuration(Crypt::decrypt($idStr), $details['month_val'] ?? null);
									if(!empty($getAmcDuration)){
										$disabled = "disabled";
									}
								@endphp

								<tr id="tr_{{$key}}" class="tr_amc">
									<td>
										
											<input type="hidden" name="details[{{$key}}][month_val]" value="{{ $details['month_val'] ?? '' }}">
										
										<select name="details[{{$key}}][month_val]" class="form-control months" id="month_val{{$key}}" onchange="getMonths({{$key}});">
											<option value="" hidden selected>Choose One</option>
											@for ($m = 1; $m <= 10; $m++)
												@php $month_val = ($m * 12); @endphp
												<option value="{{ $month_val }}" @if($month_val == ($details['month_val'] ?? old('details.'.$key.'.month_val'))) selected @endif>
													{{ $m }} Year
												</option>
											@endfor
										</select>
										@error('details.'.$key.'.month_val') <p class="small text-danger">{{ $message }}</p> @enderror
									</td>

                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[{{$key}}][amount]" class="form-control" onkeypress="validateNum(event)"  id="amount{{$key}}" value="{{ old('details.'.$key.'.amount') }}" style="width: 90px;" maxlength="8">
                                        </div>
                                        @error('details.'.$key.'.amount') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                    <td>
                                        <textarea name="details[{{$key}}][description]" class="form-control" id="description{{$key}}" cols="30" rows="2">{{ old('details.'.$key.'.description') }}</textarea>
                                        @error('details.'.$key.'.description') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </td>
                                                                            
                                    <td>
                                        <a class="btn  btn-success actionTimebtn addNewTime" id="addNew{{$key}}">+</a>
                                        <a class="btn  btn-danger actionTimebtn removeTimePrice" id="removeNew{{$key}}" onclick="removeRow({{$key}})">X</a>
                                    </td>
                                </tr>  
                                @endforeach
                                @else

                                @if(!empty($amc))
                                    @php 
                                        $j = 1;
                                    @endphp
                                    @foreach($amc as $item)

                                    
                                    <tr id="tr_{{$j}}" class="tr_amc">
                                        <input type="hidden" name="details[{{$j}}][month_val]" value="{{$item['month_val'] ?? ''}}">
                                        <td>
                                            <select name=""  class="form-control months" id="month_val{{$j}}" >
                                                <option value="">Choose Year</option>
                                                @for ($m=1;$m<=10;$m++)
                                                @php
                                                    $month_val = ($m*12);
                                                @endphp
                                                <option value="{{ $month_val }}" @if($month_val == ($item['month_val'] ?? old('details.'.$j.'.month_val') ?? '')) selected @endif>
    {{ $m }} Year
</option>

                                                @endfor
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group ">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        Rs.
                                                    </div>
                                                </div>
                                                <input type="text" autocomplete="off" name="details[{{$j}}][amount]" class="form-control" value="{{$item['amount']}}" onkeypress="validateNum(event)" maxlength="8" id="amount{{$j}}" style="width: 90px;">
                                            </div>
                                        </td>
                                        <td>
                                            <textarea name="details[{{$j}}][description]" class="form-control" id="description{{$j}}" cols="30" rows="2">
    {{ $item['description'] ?? old('details.'.$j.'.description') ?? '' }}
</textarea>

                                        </td>
                                                                                
                                        <td>
                                            <a class="btn  btn-success actionTimebtn addNewTime" id="addNew{{$j}}">+</a>
                                            <a class="btn  btn-danger actionTimebtn removeTimePrice" id="removeNew{{$j}}" onclick="removeRow({{$j}})">X</a>
                                        </td>
                                    </tr>  
                                    @php
                                        $j++;
                                    @endphp
                                    @endforeach
                                @else
                                <tr id="tr_1" class="tr_amc">
                                    <td>
                                        <select name="details[1][month_val]" class="form-control months" id="month_val1" onchange="getMonths(1);">
                                            <option value="" hidden selected>Choose Year</option>
                                            @for ($m=1;$m<=10;$m++)
                                            @php
                                                $month_val = ($m*12);
                                            @endphp
                                            <option value="{{$month_val}}">{{$m}} Year</option> 
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    Rs.
                                                </div>
                                            </div>
                                            <input type="text" autocomplete="off" name="details[1][amount]" class="form-control" value="" onkeypress="validateNum(event)" maxlength="8" id="amount1" style="width: 90px;">
                                        </div>
                                    </td>
                                    <td>
                                        <textarea name="details[1][description]" class="form-control" id="description1" cols="30" rows="2"></textarea>
                                    </td>
                                                                            
                                    <td>
                                        <a class="btn  btn-success actionTimebtn addNewTime" id="addNew1">+</a>
                                        <a class="btn  btn-danger actionTimebtn removeTimePrice" id="removeNew1" onclick="removeRow(1)">X</a>
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
                        @error('finalErrMsg') <p class="small text-danger">{{ $message }}</p> @enderror
                        <a href="{{ route('product.list') }}?{{$getQueryString}}" class="btn  btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn  btn-success">Save </button>

                        @if (!empty($amc))
                        <a onclick="return confirm('Are you sure want to remove all?');" href="{{ route('product.remove-amc-offers', [$idStr,$getQueryString]) }}" class="btn btn-warning">Remove All</a>
                        @endif
                    </div>
                </div>  
                                                                     
            </div>              
        </div>                         
        </form>             
    </div>  
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
    var monthArr = [];
    var indexArr = [];
    
    $(document).ready(function(){          
        if(rowCount == 1){
            $('#removeNew1').hide();
        }      
    });

    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });

    @if (empty($amc))
        var i = 2;
        
    @else
        var totalDetails = "{{count($amc)}}";
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

        var count_tr_amc = $('.tr_amc').length; 
        if(count_tr_amc == 10){
            alert('Already 10 added');
        } else {
            var thisClickedBtn = $(this);    
            var toAppend = mOptions = ``; 
            
            for(var m = 1; m<=10; m++){
                var mVal = (m*12);
                mOptions += `<option value="`+mVal+`">`+m+` Year</option>`;
            }
            
            
            toAppend += `
            <tr id="tr_`+i+`" class="tr_amc">
                <td>
                    <select name="details[`+i+`][month_val]" class="form-control months" id="month_val`+i+`" onchange="getMonths(`+i+`);">
                        <option value="" hidden selected>Choose Year</option>
                        `+mOptions+`
                    </select>
                </td>
                <td>
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                Rs.
                            </div>
                        </div>
                        <input type="text" autocomplete="off" name="details[`+i+`][amount]" class="form-control" value="" onkeypress="validateNum(event)" maxlength="8" id="amount`+i+`" style="width: 90px;">
                    </div>
                </td>
                <td>
                    <textarea name="details[`+i+`][description]" class="form-control" id="description`+i+`" cols="30" rows="2"></textarea>
                </td>
                                                        
                <td>
                    <a class="btn  btn-success actionTimebtn addNewTime" id="addNew`+i+`">+</a>
                    <a class="btn  btn-danger actionTimebtn removeTimePrice" id="removeNew`+i+`" onclick="removeRow(`+i+`)">X</a>
                </td>
            </tr>  
            `;
            

            $('#timePriceTable tbody').append(toAppend);
            i++;
        }

       
        
    });
    
    function removeRow(i){
        var count_tr_amc = $('.tr_amc').length; 
        console.log(count_tr_amc);  
        if(count_tr_amc > 1){     
            var m = $('#month_val'+i).val();                      
            monthArr =  monthArr.filter(e => e!=m)
            // alert(monthArr)        
            $('#tr_'+i).remove();
        }        
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

    

    function getMonths(i){       
        var e = $('#month_val'+i).val();
        monthArr.push(e);
        
    }

</script>
@endsection