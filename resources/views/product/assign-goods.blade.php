@extends('layouts.app')
@section('content')
@section('page', $data->title)
<section>
    <ul class="breadcrumb_menu">     
        <li>Product Management</li>      
        <li><a href="{{ route('product.list') }}?{{$getQueryString}}">Spare</a> </li>
        <li>Assign Goods</li> 
    </ul> 
    @if (!empty(Request::get('backtomodule')))
    <ul class="breadcrumb_menu">
        <li>
            <a href="{{Request::get('backtodestination')}}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To {{ str_replace("_"," ",ucwords(Request::get('backtomodule'))) }}
            </a>
        </li>               
    </ul>
    @else
    <ul class="breadcrumb_menu">
        <li>
            <a href="{{ route('product.list')}}?{{$getQueryString}}">
                <i class="fi-br-arrow-alt-circle-left"></i>
                Back To Product
            </a>
        </li>               
    </ul>
    @endif 
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <input type="text" autocomplete="off" class="form-control" id="product" onkeyup="getProducts(this.value);" placeholder="Search goods by name ... " name="product_name" value="" >
                <input type="hidden" name="product_id" id="product_id" class="" value="">
                <div class="respDropProd" id="respDropProd"></div>
            </div> 
                                                    
        </div>            
    </div>   
    <div id="">
        <form action="{{ route('product.save-spare-goods', [$idStr,$getQueryString]) }}" method="POST">
            @csrf
            <input type="hidden" name="browser_name" id="browser_name">
            <input type="hidden" name="navigator_useragent" id="navigator_useragent">
            <div class="row" id="">
                <ul class="goods_class">             
                    @if (!empty($data->spare_goods))
                    @forelse ($data->spare_goods as $goods)
                    <li id="goodsli{{$goods->goods_id}}"> 
                        {{ $goods->goods->title }}
                        <a href="javascript:void(0);" onclick="removeStore({{$goods->goods_id}});"><i class="fi fi-br-cross-small"></i>
                        </a> 
                        <input type="hidden" class="goods_ids" name="goods_ids[]" value="{{ $goods->goods_id }}" >
                    </li>
                    @empty
                        
                    @endforelse
                    
                    @endif
                </ul>                
            </div> 
            <div class="row" id="goodsDiv">
                <div class="col-md-6">
                    <a href="{{ route('product.list') }}?{{$getQueryString}}" class="btn btn-danger">Back</a>
                    <button type="submit" class="btn btn-success">Save</button>
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

    var proIdArr = [];
    $(document).ready(function(){
        @if (!empty($data->spare_goods->toArray()))
            $('#goodsDiv').show();
        @else
            $('#goodsDiv').hide();
        @endif

        $('.goods_ids').each(function(){ 
            proIdArr.push($(this).val())
        });
    })
    function getProducts(search,index){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-product-by-type') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    type: 'fg',
                    idnotin: proIdArr
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchProduct(${value.id})">${value.title}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  product-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No product found</li></div>`;
                    }
                    $('#respDropProd').html(content);
                }
            });
        } else {
            $('.product-dropdown').hide()
        }
        
    }

    function fetchProduct(id) {
        $('.product-dropdown').hide();
        

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
                var mop = result.mop;
                var hsn_code = result.hsn_code;

                // $('#product').val(title);
                // $('#product_id').val(id);
                 
                proIdArr.push(id);
    
                $('.goods_class').append(`<li id="goodsli`+id+`">`+title+` <a href="javascript:void(0);"  onclick="removeStore('`+id+`');"><i class="fi fi-br-cross-small"></i></a><input type="hidden" class="goods_ids" name="goods_ids[]" value="`+id+`" ></li>`);
                $('.product-dropdown').hide();
                $('#product').val('');

                if(proIdArr.length == 0){
                    $('#goodsDiv').hide();
                } else {
                    $('#goodsDiv').show();
                }

                // alert(proIdArr) 
                
            }
        });             
    }

    function removeStore(id){
        $('.goods_class > #goodsli'+id).remove();
        proIdArr =  proIdArr.filter(e => e!=id);

        var old_product_count = "{{ count($data->spare_goods->toArray()) }}";
        
        if(proIdArr.length == 0 && old_product_count == 0){
            $('#goodsDiv').hide();
        } else {
            $('#goodsDiv').show();
        }
    }
</script>  
@endsection 