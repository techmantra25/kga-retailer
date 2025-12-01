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
                                    @if (!empty($goods_warranty))
                                    <select name="warranty_type" class="form-control" id="warranty_type">
                                        <option value="" hidden selected>Choose Type</option>
                                        <option value="general" @if((!empty($goods_warranty)) && $goods_warranty->warranty_type == 'general') selected  @endif  >General</option>
                                        <option value="categorized" @if((!empty($goods_warranty)) && $goods_warranty->warranty_type == 'categorized') selected    @endif >Categorized</option>
                                    </select>
                                    @else
                                    <select name="warranty_type" class="form-control" id="warranty_type" >
                                        <option value="" hidden selected>Choose Type</option>
                                        <option value="general" @if(old('warranty_type') == 'general') selected @endif >General</option>
                                        <option value="categorized" @if(old('warranty_type') == 'categorized') selected @endif>Categorized</option>
                                    </select>
                                    @endif
                                    
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Warranty Period (In month)<span class="text-danger">*</span> </label>
                                    <input type="text" name="general_warranty" id="general_warranty" placeholder="Please Enter Warranty Period" class="form-control" maxlength="4"  onkeypress="validateNum(event)"  @if(old('comprehensive_warranty')) value="{{ old('comprehensive_warranty') }}" @else value="{{ !empty($goods_warranty)?$goods_warranty->general_warranty:'' }}" @endif  >
                                </div>
                                @error('general_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                            </div> 
                            
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm" id="categorized_div">
                  <div class="card-body">
                      <div class="row">
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label for="">Comprehensive Warranty (In month)<span class="text-danger">*</span> </label>
                                  <input type="text" name="comprehensive_warranty" id="comprehensive_warranty" placeholder="Please Enter Warranty Period" class="form-control" maxlength="4"  onkeypress="validateNum(event)"  @if(old('comprehensive_warranty')) value="{{ old('comprehensive_warranty') }}" @else value="{{ !empty($goods_warranty)?$goods_warranty->comprehensive_warranty:'' }}" @endif>
                              </div>
                              @error('comprehensive_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                          </div>
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label for="">Additional Warranty (In month) </label>
                                  <input type="text" name="extra_warranty" id="extra_warranty" placeholder="Please Enter Warranty Period" class="form-control" maxlength="4"  onkeypress="validateNum(event)" @if(old('comprehensive_warranty')) value="{{ old('comprehensive_warranty') }}" @else value="{{ !empty($goods_warranty)?$goods_warranty->extra_warranty:'' }}" @endif >
                              </div>
                              @error('extra_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                          </div>
                          <div class="col-md-4">
                              <div class="form-group">
                                  <label for="">Motor Warranty (In month) </label>
                                  <input type="text" name="motor_warranty" id="motor_warranty" placeholder="Please Enter Warranty Period" class="form-control" maxlength="4"  onkeypress="validateNum(event)"  @if(old('comprehensive_warranty')) value="{{ old('comprehensive_warranty') }}" @else value="{{ !empty($goods_warranty)?$goods_warranty->motor_warranty:'' }}"> @endif  
                              </div>
                              @error('motor_warranty') <p class="small text-danger">{{ $message }}</p> @enderror
                          </div>                          
                      </div>
                  </div>
              </div>           
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{ route('product.list') }}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <a href="{{ route('product.add-goods-warranty',$idStr) }}?{{$getQueryString}}" class="btn btn-sm btn-warning">Reset Dealer</a>
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
          @if ($goods_warranty->warranty_type == 'general')
            $('#general_div').show();
            $('#categorized_div').hide();
          @else
            $('#categorized_div').show();
            $('#general_div').hide();
          @endif
          
        @else

        var old_warranty_type = "{{ old('warranty_type') }}";
        if(old_warranty_type == ''){
            $('#general_div').hide();
            $('#categorized_div').hide();
        } else {
            if(old_warranty_type == 'general'){
                $('#general_div').show();
                $('#categorized_div').hide();
            } else if (old_warranty_type == 'categorized'){
                $('#general_div').hide();
                $('#categorized_div').show();
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
        if(warranty_type == 'general'){
          $('#general_div').show();
          $('#categorized_div').hide();
        } else {
          $('#categorized_div').show();
          $('#general_div').hide();
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

</script>
@endsection