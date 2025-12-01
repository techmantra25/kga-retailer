@extends('layouts.app')
@section('content')
@section('page', 'Add Spares')
<section>    
    <ul class="breadcrumb_menu">  
        <li>Repair</li>     
        <li>{{ $repair->unique_id }}</li>
        <li>{{ $repair->product_name }}</li>
        <li>Add Spares</li>
    </ul>
    <ul class="breadcrumb_menu">  
        <li>Service Partner</li>     
        <li>{{ $repair->service_partner->person_name }} - {{ $repair->service_partner->company_name }}</li>
        
    </ul>
    <div class="row">        
        <form id="myForm" action="{{ route('repair.save-spares', [$idStr,$getQueryString]) }}" method="POST">
        @csrf
        <input type="hidden" name="repair_id" value="{{$id}}">
        @error('goods_id') <p class="small text-danger">{{ $message }}</p> @enderror
        <div class="row">
            <div class="col-sm-12">                  
                <div class="card shadow-sm">
                    <h6>Item Details</h6> 
                    <div class="table-responsive order-addmore">
                        <table class="table" id="timePriceTable">
                            <thead>
                                <tr>
                                    <th>Spare <span class="text-danger">*</span></th>
                                    <th>Barcode  <span class="text-danger">*</span></th>
                                </tr>
                            </thead>
                            <tbody> 

                              @if (old('details'))

                              @php
                                $old_details = old('details');
                              @endphp

                              @foreach ($old_details as $key=>$details)
                                <tr id="tr_{{$key}}" class="tr_pro">
                                  <td class="f-12" style="width: 690px;">
                                      <input type="text" class="form-control" id="product{{$key}}" onkeyup="getProducts(this.value,{{$key}});" placeholder="Search spare ... " name="details[{{$key}}][product]" autocomplete="off" value="{{$details['product']}}" >
                                      <input type="hidden" name="details[{{$key}}][product_id]" id="product_id{{$key}}" class="productids" value="{{$details['product_id']}}">
                                      <div class="respDrop" id="respDrop{{$key}}"></div>
                                      @error('details.'.$key.'.product_id') <p class="small text-danger">{{ $message }}</p> @enderror
                                  </td>
                                  <td class="f-12" style="width: 200px;">
                                    <input type="text" class="form-control barcodesearch" id="searchBarcode{{$key}}" onkeyup="searchBarcodes(this.value,{{$key}});"  placeholder="Search barcode ... " name="" autocomplete="off">
                                    
                                    <div class="respDropBarcode" id="respDropBarcode{{$key}}"></div>
                                    @error('details.'.$key.'.barcodes') <p class="small text-danger">{{ $message }}</p> @enderror
                                  </td>
                                  @php
                                    $barcodes = isset($details['barcodes'])?$details['barcodes']:array();
                                  @endphp
                                  <td class="f-12" style="width: 600px;">
                                    <ul class="goods_class m-0" id="barcodeul{{$key}}">
                                      @forelse ($barcodes as $barcode)
                                      <li id="barcode"> 
                                        {{$barcode}}
                                        <a href="javascript:void(0);" onclick="removeBarcode({{$key}},'{{$barcode}}')"><i class="fi fi-br-cross-small"></i>
                                        </a> 
                                        <input type="hidden" class="barcodes" name="details[{{$key}}][barcodes][]" value="{{$barcode}}">
                                      </li>
                                      @empty
                                        
                                      @endforelse
                                      
                                      
                                    </ul>
                                    
                                  </td>
                                
                                  <td>
                                      <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew{{$key}}">+</a>
                                      <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew{{$key}}" onclick="removeRow({{$key}})">X</a>
                                  </td>
                                </tr>
                              @endforeach
                              @else                              
                                <tr id="tr_1" class="tr_pro">
                                    <td class="f-12" style="width: 690px;">
                                        <input type="text" class="form-control" id="product1" onkeyup="getProducts(this.value,1);" placeholder="Search spare ... " name="details[1][product]" autocomplete="off">
                                        <input type="hidden" name="details[1][product_id]" id="product_id1" class="productids">
                                        <div class="respDrop" id="respDrop1"></div>
                                    </td>
                                    <td class="f-12" style="width: 200px;">
                                      <input type="text" class="form-control barcodesearch" id="searchBarcode1" onkeyup="searchBarcodes(this.value,1);" placeholder="Search barcode ... " name="" autocomplete="off">
                                      
                                      <div class="respDropBarcode" id="respDropBarcode1"></div>
                                  </td>
                                  <td class="f-12" style="width: 600px;">
                                    <ul class="goods_class m-0" id="barcodeul1">
                                      
                                    </ul>
                                  </td>                                   
                                    <td>
                                        <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew1">+</a>
                                        <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew1" onclick="removeRow(1)">X</a>
                                    </td>
                                </tr>
                              @endif                
                                                          
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{ route('repair.list') }}?{{$getQueryString}}" class="btn btn-sm btn-danger">Back</a>
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Save </button>
                    </div>
                </div>  
                                                                     
            </div>              
        </div>                         
        </form>             
    </div>   
</section>
<script>
    var rowCount = $('#timePriceTable tbody tr').length;
    var proIdArr = [];
    var barcodeArr = [];

    var i = 2;
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


    $(document).ready(function(){
        
        if(rowCount == 1){
            $('#removeNew1').hide();
        }

        $('.productids').each(function(){ 
            if($(this).val() != ''){
                proIdArr.push($(this).val());
            }  
        });


        @if(old('details'))
          $('.barcodesearch').attr('readonly', false);
        @else
          $('.barcodesearch').attr('readonly', true);
        @endif

    });

    $("#myForm").submit(function() {
        $('#submitBtn').attr('disabled', 'disabled');
        $('#submitBtn').html('<i class="fi fi-br-refresh"></i>').append('   Please wait ...');
        
        return true;
    });

    
    

    $(document).on('click','.addNewTime',function(){
        var thisClickedBtn = $(this);    
        var toAppend = ``;     
        
        toAppend += `
        <tr id="tr_`+i+`" class="tr_pro">
              <td class="f-12" style="width: 690px;">
                  <input type="text" class="form-control " id="product`+i+`" onkeyup="getProducts(this.value,`+i+`);" placeholder="Search spare ... " name="details[`+i+`][product]" autocomplete="off">
                  <input type="hidden" name="details[`+i+`][product_id]" id="product_id`+i+`" class="productids">
                  <div class="respDrop" id="respDrop`+i+`"></div>
              </td>
              <td class="f-12" style="width: 200px;">
                <input type="text" class="form-control barcodesearch" id="searchBarcode`+i+`" onkeyup="searchBarcodes(this.value,`+i+`);" placeholder="Search barcode ... " name="" autocomplete="off" readonly>
                
                <div class="respDropBarcode" id="respDropBarcode`+i+`"></div>
            </td>
            <td class="f-12" style="width: 600px;">
              <ul class="goods_class m-0" id="barcodeul`+i+`">
                
              </ul>
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
                $('#searchBarcode'+count).attr('readonly', false);
                // alert(proIdArr) 
                
            }
        });             
    }


    function searchBarcodes(search, index){
        var product_id = $('#product_id'+index).val();
        var service_partner_id = "{{$repair->service_partner_id}}";
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.service-partner-barcodes') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search,
                    product_id: product_id,
                    service_partner_id: service_partner_id,
                    barcodenotin: barcodeArr
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  barcode-dropdown select-md" aria-labelledby="dropdownMenuButton">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchBarcode(${index},'${value.barcode_no}')">${value.barcode_no}</a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  barcode-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No barcode found</li></div>`;
                    }
                    $('#respDropBarcode'+index).html(content);
                }
            });
        } else {
            $('.barcode-dropdown').hide()
        }
    }

    function fetchBarcode(index,val){
      $('.barcode-dropdown').hide()
      // alert(index+':- '+val);
      $('#searchBarcode'+index).val('');

      var liHtml = `<li id="barcode"> 
                      `+val+`
                      <a href="javascript:void(0);" onclick="removeBarcode(`+index+`,'`+val+`');"><i class="fi fi-br-cross-small"></i>
                      </a> 
                      <input type="hidden" class="barcodes" name="details[`+index+`][barcodes][]" value="`+val+`">
                    </li>`;

      $('#barcodeul'+index).append(liHtml);
      barcodeArr.push(val);

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