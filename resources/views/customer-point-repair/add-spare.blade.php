@extends('layouts.app')
@section('content')
@section('page', 'Add Spare parts')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Customer Point Repair</li>
        <li>Product Name: <strong>{{$data->item}}</strong></li>        
    </ul>
    <div class="row">
        @if (Session::has('success'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('success') }}
        </div>
        @endif
        @if (Session::has('error'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('error') }}
        </div>
        @endif
        <div class="row" id="">
        
        <form id="myForm" action="{{route('customer-point-repair.save-spare')}}" method="POST">
            @csrf
            {{-- $validSpareIds = StockInventory::whereIn('product_id', $spareIds)->where('quantity', '>', 0)
            ->pluck('product_id') // Only pluck the product_id
            ->toArray(); --}}
        <input type="hidden" name="crp_id" value="{{$data->id}}">
        <input type="hidden" name="generate_by" value="{{Auth::user()->id}}">

        <div class="row">
            
        </div>
        <div class="row">
            <div class="col-sm-12">  
                <div class="card shadow-sm">
                    <div class="card-body">
                       <div class="row">
                        <div class="col-md-4">
                        <h6 class="badge bg-success">For Service Partner</h6> 
                        </div>
                            <div class="col-md-8 text-end">
                                <a href="{{ route('customer-point-repair.list-booking') }}" class="btn btn-sm btn-danger select-md">Back</a>
                                @php
                                    $sales_order_id = App\Models\SalesOrder::where('crp_id', $data->id)->orderBy('id', 'DESC')->value('id');
                                    
                                    $sales_order_id = $sales_order_id?$sales_order_id:null;
                                        $Packingslip_id = App\Models\Packingslip::where('sales_order_id', $sales_order_id)->orderBy('id', 'DESC')->value('id');
                                @endphp
                                @if($data->packing_slip && $Packingslip_id)
                                <a href="{{ route('packingslip.download', Crypt::encrypt($Packingslip_id)) }}" class="btn btn-outline-primary select-md">Packing Slip Download</a>
                                @endif
                                @if($data->service_partner_invoice)
                                    @php
                                        $invoice_id = App\Models\Invoice::where('invoice_no', $data->service_partner_invoice)->orderBy('id', 'DESC')->value('id');
                                    @endphp
                                    <a href="{{ route('sales-order.show', Crypt::encrypt($sales_order_id)) }}" class="btn btn-outline-primary select-md">Order Details</a>
                                    <a href="{{ route('invoice.download', Crypt::encrypt($invoice_id)) }}" class="btn btn-outline-primary select-md">Invoice Download</a>
                                @endif
                                @if(count($spare_data) > 0 && $data->status >= 7 && $data->return_spare==0)
                                    <a href="{{ route('return-spares.store_crp_spare', ['crp_id' => $data->id]) }}" 
                                    class="btn btn-outline-warning select-md"
                                    onclick="return confirm('Are you sure you want to return this spare?');">
                                        Return Spare
                                    </a>
                                @endif
                                @if($data->return_spare_order)
                                    <a href="{{ route('return-spares.list', ['search' => $data->return_spare_order])}}" 
                                        class="btn btn-outline-success select-md">
                                            Return Spare Details
                                        </a>
                                @endif
                                @if($data->return_spare==1 && is_null($data->return_spare_order))
                                    <a href="#" 
                                        class="btn btn-warning select-md">
                                            Return Spare Completed
                                        </a>
                                @endif
                            
                            
                            </div>
                       </div>
                    </div>
                </div> 
                <div class="card shadow-sm">
					<div class="card-body">
                    <h6 class="badge bg-danger">Estimate Spare Parts Details</h6> 
                        <table id="productTable" class="table w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr id="tr_1" class="tr_pro">
                                {{-- Before Close the call --}}
                                @if($data->status<2)
                                <td class="f-9">
                                    <input type="text" id="productName1" class="productids form-control" onkeyup="getProducts(this.value, {{$data->product_id}}, 1);">
                                    <input type="hidden" id="productId1"  name="product_id[]" />
                                    <div id="respDrop1"></div>
                                </td>                                    
                                <td class="f-1">
                                    <input type="number" id="productQty1" class="form-control" value="1" name="product_qty[]" readonly />
                                    <span id="get_quantity"></span>
                                </td>                                     
                                <td>
                                    <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew1" onclick="addRow()">+</a>
                                    <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew1" onclick="removeRow(1)">X</a>
                                </td>
                                @else
                                    @if(!$data->status >= 7)
                                        <td colspan="3">
                                            <div class="alert alert-danger" role="alert">
                                                <p>Sorry! You don't have permission to add spare because invoice generated for existing service partner</p>
                                            </div>
                                        </td>
                                    @endif
                                @endif
                            </tr>
                            @if($spare_data)
                                @foreach($spare_data as $item)
                                    <tr id="tr_{{$item->id}}" class="tr_pro"  >
                                        <td class="f-12">
                                            <input type="text" class="productids form-control" value="{{$item->sp_name}}">
                                            <input type="hidden"  value="{{$item->sp_id}}" />
                                            <div id="respDrop1"></div>
                                        </td>         
                                        <td>
                                            <input type="number" id="productQty{{$item->id}}" class="form-control" value="{{$item->quantity}}" name="product_qty[]" readonly/>
                                        </td>                              
                                        <td>
                                            @if($data->status<2)
                                            <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew1" onclick="RemoveItemData({{$item->id}},{{$item->crp_id}})">X</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            @if($data->status<2)
                            <tr>
                                <td colspan="3" class="text-end">
                                    <button type="submit" id="submitBtn" class="btn btn-sm btn-success select-md">Save</button>
                                    @if(count($spare_data)>0)
                                        <a href="{{ route('sales-order.add', ['type' => 'sp', 'service_partner' => $data->assign_service_perter_id, 'crp_id'=>$data->id]) }}" class="btn btn-sm btn-warning">Next-></a>
                                    @endif
                                </td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                
                @if($data->is_spare_required !==0 && $data->status >= 7) 
                <div class="card shadow-sm">
					<div class="card-body">
                        <h6 class="badge bg-success">Final Spare Parts Details</h6> 
                        <table id="productTable" class="table w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Warranty Satus</th>
                                    <th>Actual Price</th>
                                    <th>Selling Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($final_spare_data)>0)
                                    @foreach($final_spare_data as $item)
                                        <tr>
                                            <td class="f-9">
                                            {{$item->partsName?$item->partsName->title:''}}
                                            </td>                                    
                                            <td class="f-1">1</td>                                     
                                            <td class="f-1">
                                            @if($item->warranty_status == 1)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                            </td>                                     
                                            <td class="f-1">{{number_format($item->actual_price,2)}}</td>                                     
                                            <td class="f-1">{{number_format($item->selling_price,2)}}</td>                                   
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3"></td>  
                                        <td><strong>Spare Amount</strong></td>  
                                        <td>{{number_format($data->spare_charge, 2)}}</td>  
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>  
                                        <td><strong>Repair Charge <span class="badge bg-success">{{$data->final_amount==0?"In warranty":""}}</span></strong></td>  
                                        <td>{{number_format($data->repair_charge, 2)}}</td>  
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>  
                                        <td><strong>Final Amount</strong></td>  
                                        <td><strong>{{number_format($data->final_amount, 2)}}</strong>@if($data->final_amount>0) @if($data->payment_method=='online')<span class="badge bg-success">PAID({{$data->payment_method}})</span>@endif @if($data->payment_method=='cash') <span class="badge bg-warning">PAID({{$data->payment_method}})</span>@endif @endif</td>  
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="card shadow-sm">
                <div class="card-body">
                       <div class="row">
                        <div class="col-md-4">
                        <h6 class="badge bg-success">For Customer</h6> 
                        </div>
                            <div class="col-md-8 text-end">
                                @if($data->final_amount>0)
                                    @if ($data->status == 7 || $data->status == 8)
                                    <a href="{{ route('customer-point-repair.download-customer-invoice', Crypt::encrypt($id)) }}" class="btn btn-outline-primary select-md">Customer Invoice Download</a>
                                        <a href="{{ route('customer-point-repair.send-user-invoice-link', Crypt::encrypt($id)) }}" class="btn btn-outline-primary select-md">Send Invoice Link</a>
                                        @if($data->return_spare==1 && is_null($data->return_spare_order))
                                        <a href="{{ route('customer-point-repair.return-spares.barcodes', [Crypt::encrypt($id),Request::getQueryString()]) }}" class="btn btn-outline-primary select-md">Dead Spare Barcode</a>
                                        @endif
                                    @endif
                                @endif
                            </div>
                       </div>
                    </div>
                </div> 
                @endif
                @if($data->is_spare_required==0 && $data->admin_approval==1)
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-success" role="alert">
                            <strong>No spare parts were used for this record.</strong><br>
                            This service call has been successfully closed and approved by an Admin.
                        </div>
                    </div>
                </div>
                
                @endif
                @if($data->is_spare_required==0 && $data->admin_approval==2)
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="alert alert-success" role="alert">
                        <strong>Service Call Rejected.</strong><br>
                        This service call has been rejected by an Admin. Please review the details or contact support for further information.
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
        </form>
        </div>
    </div>
       
</section>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
    let rowCount = 1; // Initialize the row count

// Function to add a new row
function addRow() {
    rowCount++; // Increment row count

    // Clone the existing row and adjust IDs
    const newRow = $('#tr_1').clone().attr('id', `tr_${rowCount}`);
    $('#get_quantity').text('');
    $('#productId1').val('');
    // Update input field IDs and events
    newRow.find('.productids').attr('id', `productName${rowCount}`).attr('onkeyup', `getProducts(this.value, {{$data->product_id}}, ${rowCount})`);
    newRow.find('#productId1').attr('id', `productId${rowCount}`);
    newRow.find('#respDrop1').attr('id', `respDrop${rowCount}`);
    newRow.find('#respDrop1').attr('id', `respDrop${rowCount}`);

    newRow.find('#productId1').val(''); // Clear the hidden product ID field

    
    // Update buttons' IDs and events
    newRow.find('a.addNewTime').remove();
    newRow.find('.removeTimePrice').attr('id', `removeNew${rowCount}`).attr('onclick', `removeRow(${rowCount})`);

    // Append the new row to the table
    $('#productTable tbody').append(newRow);
    $('#productName1').val('');
}

// Function to remove a row
function removeRow(rowId) {
    // Ensure that at least one row remains
    if ($('#productTable tbody tr').length > 1) {
        $(`#tr_${rowId}`).remove();
    } else {
        alert('Cannot remove the last row.');
    }
}
function RemoveItemData(itemId, crpId) {
    swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this imaginary file!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('customer-point-repair.delete-spare') }}",
                        method: 'post',
                        data: {
                            '_token': '{{ csrf_token() }}',
                            id: itemId,
                            crpId: crpId,
                        },
                        success: function(result) {
                            if(result.status=="success"){
                                $('#tr_'+itemId).remove();
                                swal("Poof! Your imaginary file has been deleted!", {
                                icon: "success",
                                });
                            }
                        }
                    });
                } else {
                    swal("Your imaginary file is safe!");
            }
        });
}

// Function to get products from the server
function getProducts(search, pId, index) {
    if (search.length > 0) {
        $.ajax({
            url: "{{ route('ajax.get-spare-part') }}",
            method: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                search: search,
                goods_id: pId,
            },
            success: function(result) {
                console.log(result);

                let content = '';
                if (result.length > 0) {
                    content += `<div class="dropdown-menu show product-dropdown select-md" aria-labelledby="dropdownMenuButton${index}">`;

                    $.each(result, (key, value) => {
                        content += `
                            <a class="dropdown-item" href="javascript:void(0);" data-quantity='${value.quantity}' data-title='${value.title}' 
                               onclick="fetchProduct(${index}, ${value.id})">
                             ${value.title}
                            </a>`;
                    });
                    content += `</div>`;
                } else {
                    content += `<div class="dropdown-menu show product-dropdown select-md" aria-labelledby="dropdownMenuButton${index}">
                                    <li class="dropdown-item">No product found</li>
                                </div>`;
                }

                // Set the dropdown content
                $('#respDrop' + index).html(content);
            }
        });
    }
}

// Fetch product details and update the input field
function fetchProduct(index, id) {
    var title = $(event.target).attr('data-title');
    // var quantity = $(event.target).attr('data-quantity');
    // if (quantity > 0) {
    //     $('#get_quantity').text('Available Quantity ' + quantity).css('color', 'green');
    // } else {
    //     $('#get_quantity').text('Out of Stock').css('color', 'red');
    // }

    

    // Update the input field with the selected product title
    $('#productName' + index).val(title);

    // Optionally, handle the product ID if needed
    $('#productId' + index).val(id);

    // Hide the dropdown after selection
    $('#respDrop' + index).html('');
   
}
</script>

@endsection