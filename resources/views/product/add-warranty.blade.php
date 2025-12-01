@extends('layouts.app')
@section('content')
@section('page', 'Add Warranty')
<section>   
    <ul class="breadcrumb_menu">
        <li>Product </li>
        <li>{{$product->title}}</li>
        <li>Add Warranty</li>
    </ul>
    <ul class="breadcrumb_menu">
        <li>
            <a href="{{ route('product.list') }}?{{$getQueryString}}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To Product
            </a>
        </li>               
    </ul>
    <form id="searchForm" action="{{ route('product.add-goods-warranty',[$idStr,$getQueryString]) }}" method="GET">
      <div class="row">
          <div class="col-sm-4">
              <div class="card shadow-sm">
                  <div class="card-body">
                      <label for="">Dealer Type <span class="text-danger">*</span></label>
                      <select name="dealer_type" class="form-control" id="dealer_type" @if(!empty($dealer_type)) readonly style="pointer-events: none;" @endif>
                          <option value="" hidden selected>Choose Type</option>
                          <option value="khosla" @if($dealer_type == 'khosla') selected @endif>Khosla</option>
                          <option value="nonkhosla" @if($dealer_type == 'nonkhosla') selected @endif>Non Khosla</option>
                      </select>
                      
                  </div>
              </div>
          </div>
      </div>
    </form>
        
    @if (!empty($dealer_type))
    <div class="row" id="">    
        <form id="myForm" action="{{ route('product.save-goods-warranty', [$idStr,$getQueryString]) }}" method="POST">
            @csrf
       
        <input type="hidden" name="goods_id" value="{{$id}}">
        <input type="hidden" name="dealer_type" value="{{$dealer_type}}">
        <input type="hidden" name="browser_name" id="browser_name">
        <input type="hidden" name="navigator_useragent" id="navigator_useragent">
        <div class="row">
        </div>
        <div class="row">
            <div class="col-sm-12">  
                <div class="card shadow-sm" id="">
                    <div class="card-body">
                        <div class="row">
                          @if (!empty($dealer_type))
                          <div class="col-md-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <label for="">Warranty Type <span class="text-danger">*</span></label>
                                    <select name="warranty_type" class="form-control" id="warranty_type" >
                                        <option value="" hidden selected>Choose Type</option>
                                        <option value="comprehensive" @if(old('warranty_type') == 'comprehensive') selected @endif >Comprehensive</option>
                                        <option value="parts" @if(old('warranty_type') == 'parts') selected @endif >Spear Parts</option>
                                        <option value="additional" @if(old('warranty_type') == 'additional') selected @endif >Additional</option>
                                        <option value="cleaning" @if(old('warranty_type') == 'cleaning') selected @endif>Normal Cleaning</option>
									<!--	<option value="deep_cleaning" @if(old('warranty_type') == 'deep_cleaning') selected @endif>Deep Cleaning</option>  -->
                                    </select>
                                    @error('warranty_type') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>
                            </div>
                          </div>
                          @endif
                            
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm" id="general_div">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4" id="additional_warranty_div">
                                <div class="form-group">
                                    <label for=""> Additional Warranty Type<span class="text-danger">*</span> </label>
                                    <select name="additional_warranty_type" class="form-control">
                                        <option value="" selected hidden>Select type..</option>
                                        <option value="1" {{old('additional_warranty_type')==1?"selected":""}}>Parts Chargeable</option>
                                        <option value="2" {{old('additional_warranty_type')==2?"selected":""}}>Service Chargeable</option>
                                    </select>
                                </div>
                                @error('additional_warranty_type') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div> 
                            <div class="col-md-4" id="number_of_cleaning_div">
                                <div class="form-group">
                                    <label for=""> Number of Normal Cleaning<span class="text-danger">*</span> </label>
                                    <input type="number" name="number_of_cleaning" placeholder="Enter number of normal cleaning" class="form-control" value="{{old('number_of_cleaning')}}">
                                </div>
                                @error('number_of_cleaning') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div> 
							<!--Deep cleaning -->
							<div class="col-md-4" id="number_of_deep_cleaning_div">
                                <div class="form-group">
                                    <label for=""> Number of Deep Cleaning<span class="text-danger">*</span> </label>
                                    <input type="number" name="number_of_deep_cleaning" placeholder="Enter number of deep cleaning" class="form-control" value="{{old('number_of_deep_cleaning')}}">
                                </div>
                                @error('number_of_deep_cleaning') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for=""><span id="additional_text"></span> Warranty Period (In month)<span class="text-danger">*</span> </label>
                                    <input type="text" name="general_warranty" id="general_warranty" placeholder="Please Enter Warranty Period" class="form-control" maxlength="4"  onkeypress="validateNum(event)"  @if(old('comprehensive_warranty')) value="{{ old('comprehensive_warranty') }}" @else value="{{ !empty($goods_warranty)?$goods_warranty->general_warranty:'' }}" @endif  >
                                </div>
                                @error('general_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div> 

                        </div>
                    </div>
                </div>
                <div class="card shadow-sm" id="parts_div">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label for="">Spear parts<span class="text-danger">*</span> </label>
                                  <select name="spear_parts" class="form-control">
                                    <option value="" selected hidden> Select spear parts..</option>
                                    @foreach ($spear_parts as $item)
                                        <option value="{{$item->id}}" {{ old('spear_parts')==$item->id?"selected":"" }}>{{$item->title}}</option>
                                    @endforeach
                                  </select>
                              </div>
                              @error('spear_parts') <p class="small text-danger">{{ $message }}</p> @enderror
                          </div>
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label for=""> <span id="additional_spear_text"></span> Warranty Period (In month)<span class="text-danger">*</span> </label>
                                  <input type="text" name="parts_warranty" id="parts_warranty" placeholder="Please Enter Warranty Period" class="form-control" maxlength="4"  onkeypress="validateNum(event)"  @if(old('parts_warranty')) value="{{ old('parts_warranty') }}" @else value="{{ !empty($goods_warranty)?$goods_warranty->parts_warranty:'' }}" @endif>
                              </div>
                              @error('parts_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                          </div>
                      </div>
                  </div>
              </div>           
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{ route('product.list') }}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <a href="{{ route('product.add-goods-warranty',$idStr) }}?{{$getQueryString}}" class="btn btn-sm btn-warning">Reset page</a>
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

    $(document).ready(function(){  
        // alert(rowCount)
        @if (!empty($goods_warranty))
          @if ($goods_warranty->warranty_type == 'parts')
          $('#parts_div').show();
          $('#general_div').hide();
          $('#additional_warranty_div').hide();
		  $('#number_of_deep_cleaning').hide();
          @elseif ($goods_warranty->warranty_type == 'additional')
          $('#general_div').show();
          $('#parts_div').hide();
          $('#additional_warranty_div').show();
		  $('#number_of_deep_cleaning').hide();
          @elseif ($goods_warranty->warranty_type == 'cleaning')
          $('#general_div').show();
          $('#parts_div').hide();
          $('#number_of_cleaning_div').show();
		  $('#number_of_deep_cleaning_div').hide();
		 @elseif ($goods_warranty->warranty_type == 'deep_cleaning')
          $('#general_div').show();
          $('#parts_div').hide();
          $('#number_of_deep_cleaning_div').show();
          @else
          $('#general_div').show();
          $('#parts_div').hide();
          $('#additional_warranty_div').hide();
		  $('#number_of_deep_cleaning').hide();
          @endif
          
        @else

        var old_warranty_type = "{{ old('warranty_type') }}";
        if(old_warranty_type == ''){
            $('#general_div').hide();
            $('#parts_div').hide();
            $('#additional_warranty_div').hide();
        } else {
            if(old_warranty_type == 'parts'){
                $('#general_div').hide();
                $('#parts_div').show();
                $('#additional_warranty_div').hide();
                $('#number_of_cleaning_div').hide();
            }else if(old_warranty_type == 'additional'){
                $('#additional_warranty_div').show();
                $('#number_of_cleaning_div').hide();
                $('#general_div').show();
                $('#parts_div').hide();
				 $('#number_of_deep_cleaning').hide();
            }else if(old_warranty_type == 'cleaning'){
                $('#number_of_cleaning_div').show();
				$('#number_of_deep_cleaning_div').hide();
                $('#additional_warranty_div').hide();
                $('#general_div').show();
                $('#parts_div').hide();
			}else if(old_warranty_type == 'deep_cleaning'){
                $('#number_of_deep_cleaning_div').show();
				$('#number_of_cleaning_div').hide();
                $('#additional_warranty_div').hide();
                $('#general_div').show();
                $('#parts_div').hide();
            }else{
                $('#additional_warranty_div').hide();
                $('#number_of_cleaning_div').hide();
				 $('#number_of_deep_cleaning').hide();
                $('#general_div').show();
                $('#parts_div').hide();
            }
        }
          
        @endif

        
    });

    $('#dealer_type').on('change', function(){
        var dealer_type = this.value;
        $('#searchForm').submit();
        
    })

    $('#warranty_type').on('change', function(){
        var warranty_type = this.value;
        if(warranty_type == 'parts'){
            $('#additional_spear_text').text(ucwords(warranty_type));
            $('#parts_div').show();
            $('#general_div').hide();
            $('#additional_warranty_div').hide();
            $('#number_of_cleaning_div').hide();
			$('#number_of_deep_cleaning_div').hide();	
        } else if(warranty_type == 'additional'){
            $('select[name="spear_parts"]').val('');
            $('#additional_text').text(ucwords(warranty_type));
            $('#general_div').show();
            $('#parts_div').hide();
            $('#additional_warranty_div').show();
            $('#number_of_cleaning_div').hide();
			$('#number_of_deep_cleaning_div').hide();	
        } else if(warranty_type == 'cleaning'){
            $('select[name="spear_parts"]').val('');
            $('#additional_text').text(ucwords(warranty_type));
            $('#general_div').show();
            $('#parts_div').hide();
            $('#additional_warranty_div').hide();
            $('#number_of_cleaning_div').show();
			 $('#number_of_deep_cleaning_div').hide();
		} else if(warranty_type == 'deep_cleaning'){
            $('select[name="spear_parts"]').val('');
            $('#additional_text').text(ucwords(warranty_type));
            $('#general_div').show();
            $('#parts_div').hide();
            $('#additional_warranty_div').hide();
            $('#number_of_cleaning_div').hide();
			 $('#number_of_deep_cleaning_div').show();	
        }else {
            $('select[name="spear_parts"]').val('');
            $('#additional_text').text(ucwords(warranty_type));
            $('#general_div').show();
            $('#parts_div').hide();
            $('#additional_warranty_div').hide();
            $('#number_of_cleaning_div').hide();
			$('#number_of_deep_cleaning_div').hide();	
        }
    })

    $("#myForm").submit(function() {
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
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

   function ucwords(str) {
    return str
        .replace(/_/g, ' ')                 // Replace underscores with spaces
        .toLowerCase()                      // Convert all to lowercase
        .replace(/\b\w/g, c => c.toUpperCase());  // Capitalize first letter of each word
}


</script>
@endsection