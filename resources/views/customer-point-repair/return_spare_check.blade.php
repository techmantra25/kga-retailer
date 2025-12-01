@extends('layouts.app')
@section('content')
@section('page', 'Verity Spare Details')
<section>   
    <ul class="breadcrumb_menu">  
        <li>Customer Point Repair</li>   
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
        <div class="row">
            <div class="col-sm-12">  
                <div class="card shadow-sm">
					<div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                            <h6 class="badge bg-success">FInal Spare parts</h6> 
                            </div>
                            <div class="col-md-8 text-end">
                                <a href="{{ route('customer-point-repair.add-spare', Crypt::encrypt($id)) }}" class="btn btn-sm btn-danger select-md">Back</a>
                            </div>
                        </div>
                        <table id="productTable" class="table w-100">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Barcode For Old Spare</th>
                                    <th>Quantity</th>
                                    <th>Warranty Satus</th>
                                    <th>Actual Price</th>
                                    <th>Selling Price</th>
                                    <th class="text-center">Spare Not Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_amount = 0;
                                @endphp
                                @if(count($final_spare_data)>0)
                                    @foreach($final_spare_data as $key=>$item)
                                    @php
                                        if($item->warranty_status==1){
                                            $total_amount+=$item->actual_price;
                                        }else{
                                            $total_amount+=$item->return_required==0?$item->actual_price:0;
                                        }
                                       
                                    @endphp
                                        <tr>
                                            <td class="f-9">
                                            {{$item->partsName?$item->partsName->title:''}}
                                            </td>                                    
                                            <td class="f-1">{{$item->new_barcode}}</td>                                     
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
                                            <td class="f-1 text-center">
                                                @if($item->warranty_status==0)
                                                    <input class="form-check-input" type="checkbox" id="check{{$key+1}}" 
                                                    onchange="handleCheckboxChange({{ $item->id }}, this.checked)" {{$item->return_required==0?"checked":""}} style="width:30px;">
                                                @endif
                                            </td>                                   
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="5" class="text-end">Total Credit Will:</td>
                                        <td><strong>{{number_format($total_amount,2)}}</strong>
                                        </td>
                                        <td class="text-center">  
                                            @if($data->return_spare==0)
                                                <a href="{{ route('return-spares.return_old_spare', ['crp_id' => $id, 'total_amount'=>$total_amount]) }}" 
                                                class="btn btn-outline-success select-md"
                                                onclick="return confirm('Are you sure you want to return this spare?');">
                                                    Confirm Return Spare
                                                </a>
                                            @endif
                                            @if($data->return_spare==1)
                                                <button class="badge bg-warning select-md">
                                                    Return Spare completed</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

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
    function handleCheckboxChange(id, isChecked) {
        // Define the base route URL
        var route = '{{ route("return-spares.spare_not_required", [":id", ":status"]) }}';
        
        // Replace the placeholder with actual values
        route = route.replace(':id', id).replace(':status', isChecked ? 0 : 1);

        // Redirect to the route (which triggers a page load)
        window.location.href = route;
    }

</script>

@endsection