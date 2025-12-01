@extends('servicepartnerweb.layouts.app')
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
        
        <form id="myForm" action="{{route('servicepartnerweb.customer-repair-point.save-spare')}}" method="POST">
            @csrf
        
        <input type="hidden" name="crp_id" value="{{$data->id}}">
        <input type="hidden" name="generate_by" value="{{Auth::user()->id}}">

        <div class="row">
            
        </div>
        <div class="row">
            <div class="col-sm-12">  
                <div class="card shadow-sm">
					<div class="card-body">
                    <h6>Spare Parts Details</h6> 
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
                            @if($data->status<3)
                                <td class="f-9">
                                    <input type="text" id="productName1" class="productids form-control" onkeyup="getProducts(this.value, {{$data->product_id}}, 1);">
                                    <input type="hidden" id="productId1"  name="product_id[]" />
                                    <div id="respDrop1"></div>
                                </td>                                    
                                <td class="f-1">
                                    <input type="number" id="productQty1" class="form-control" value="1" name="product_qty[]" />
                                    <!-- <span id="get_quantity"></span> -->
                                </td>                                     
                                <td>
                                    <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew1" onclick="addRow()">+</a>
                                    <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew1" onclick="removeRow(1)">X</a>
                                </td>
                            @else
                                <td colspan="3">
                                    <div class="alert alert-danger" role="alert">
                                        <p>Sorry! You don't have permission to add any spares because this call has been closed or cancelled.</p>
                                    </div>
                                </td>
                            @endif
                            </tr>
                                @if($spare_data)
                                    @foreach($spare_data as $item)
                                        <tr id="tr_{{$item->id}}" class="tr_pro"  >
                                            <td class="f-12">
                                                <input type="text" id="productName1" class="productids form-control" value="{{$item->sp_name}}">
                                                <input type="hidden" id="productId1" value="{{$item->sp_id}}" />
                                                <div id="respDrop1"></div>
                                            </td>         
                                            <td>
                                                <input type="number" id="productQty{{$item->id}}" class="form-control" value="{{$item->quantity}}" name="product_qty[]" />
                                            </td>
                                            @if($data->status<3)                              
                                            <td>
                                                <a class="btn btn-sm btn-success actionTimebtn addNewTime" id="addNew1" onclick="addRow()">+</a>
                                                <a class="btn btn-sm btn-danger actionTimebtn removeTimePrice" id="removeNew1" onclick="RemoveItemData({{$item->id}},{{$item->crp_id}})">X</a>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-body text-end">
                        <a href="{{ route('servicepartnerweb.notification.list-customer-repair-point') }}" class="btn btn-sm btn-danger">Back</a>
                        @if($data->status<3)
                        <button type="submit" id="submitBtn" class="btn btn-sm btn-success">Save </button>
                        @endif
                    </div>
                </div> 
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
    newRow.find('.addNewTime').attr('id', `addNew${rowCount}`).attr('onclick', `addRow()`);
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
                        url: "{{ route('servicepartnerweb.customer-repair-point.delete-spare') }}",
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
                            <a class="dropdown-item" href="javascript:void(0);" data-title="${value.title}" 
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
    // Update the input field with the selected product title
    // var quantity = $(event.target).attr('data-quantity');
    // if (quantity > 0) {
    //     $('#get_quantity').text('Available Quantity ' + quantity).css('color', 'green');
    // } else {
    //     $('#get_quantity').text('Out of Stock').css('color', 'red');
    // }

    $('#productName' + index).val(title);

    // Optionally, handle the product ID if needed
    $('#productId' + index).val(id);

    // Hide the dropdown after selection
    $('#respDrop' + index).html('');
   
}
</script>

@endsection