@extends('layouts.app')
@section('content')
@section('page', 'Upload Product CSV')
<section>
    <ul class="breadcrumb_menu">        
        <li><a href="{{ route('product.list') }}">Product</a> </li>
        <li>Upload CSV</li>
    </ul>
    <div class="row">
        <div class="col">
            @if (Session::has('message'))
            <div class="alert alert-success" role="alert">
                {{ Session::get('message') }}
            </div>
            @endif
        </div>
        <form id="myForm" action="{{ route('product.submit-csv') }}" enctype="multipart/form-data" method="POST">
            @csrf

            <input type="hidden" name="browser_name" id="browser_name">
            <input type="hidden" name="navigator_useragent" id="navigator_useragent">

            <div class="row">
                <div class="col-sm-12">            
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Upload CSV <span class="text-danger">*</span></label>
                                        <input type="file" name="csv" 
                                        accept=".csv" 
                                        class="form-control" id="">
                                        @error('csv') <p class="small text-danger">{{ $message }}</p> @enderror
                                    </div>
                                </div>                  
                            </div> 
                        </div> 
                    </div>    
                    <div class="card shadow-sm">
                        <div class="card-body text-end">
                            <a href="{{ url('/samplecsv/product/sample-product.csv') }}" class="btn btn-outline-primary ">Download Sample CSV</a>
                            <a href="{{route('product.list')}}" class="btn btn-danger ">Back</a>
                            <button id="submitBtn" type="submit" class="btn  btn-success">Submit </button>
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


    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    });
    $("#myForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#submitBtn').attr('disabled', 'disabled');    
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        return true;
    });
    
</script>
@endsection