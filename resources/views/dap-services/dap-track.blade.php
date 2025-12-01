@extends('layouts.app')
@section('content')
@section('page', 'DAP-Product track')
<section>
<ul class="breadcrumb_menu">        
        <li>DAP PRODUCT TRACK</li>
    </ul>    

            <div class="col-auto">
                <div class="row g-3 align-items-center">  
                    <div class="col-auto">
                        <a href="{{ route('dap-services.list') }}" class="btn btn-outline-danger select-md">Back</a>
                    </div>                  
                </div>
            </div> 
            <div class="row">  
            <div>
            <span class="btn btn-success select-md">Booking Details</span>
            <p>Booked By: {{ $data->callBookedEmployee->name ?? 'N/A' }}</p>
            <p>Booked Date: {{ date('Y-m-d', strtotime($data->entry_date)) }}</p>
            <p>Booked From: {{ $data->branch->name ?? 'N/A' }}</p>
            </div>
            <div>
            @if($data->is_dispatched_from_branch == 0)
            <span class="btn btn-danger select-md">Dispatched Details</span>
            <p>Yet to dispatch</p>
            @else
            <span class="btn btn-success select-md">Dispatched Details</span>
            <p>Dispatched by: {{ $data->dapProductDispatchedEmployee->name ?? 'N/A' }}</p>
            <p>Dispatched Time: {{date('Y-m-d H:i ',strtotime($data->is_dispatched_from_branch_date))}}</p>
            @endif
            </div>
            <div>
            @if($data->is_reached_service_centre == 0)
            <span class="btn btn-danger select-md">Service Centre Details</span>
            <p>Yet to Reach</p>
            @else
            <span class="btn btn-success select-md">Service Centre Details</span>
            <p>Service Centre Name: {{$data->serviceCentre->name ?? 'N/A'}}</p>
            <p>Received At: {{date('Y-m-d H:i ',strtotime($data->is_reached_service_centre_date))}}</p>
            <p>Assign Engineer Name: {{$data->servicePartner->company_name ?? 'N/A'}}</p>
            @endif
            </div>
            <div>
            @if($data->quotation_status == 1)
            <span class="btn btn-success select-md">DAP Quotation Status</span>
            <p>Quotation Generated</p>
            @else
            <span class="btn btn-danger select-md">DAP Quotation Status</span>
            <p>Yet to Generated</p>
            @endif
            </div>
            <div>
            @if ($data->is_paid == 0)
            <span class="btn btn-warning select-md">Payment Status</span>
            <p>Processing</p>
            @elseif($data->is_paid == 2)
            <span class="btn btn-warning select-md">Payment Status</span>
            <p>Pending</p>
            @elseif ($data->is_paid == 1)
            <span class="btn btn-success select-md">Payment Status</span>
            <p>Paid</p>
            <p>Payment Date : {{date('Y-m-d H:i ',strtotime($data->payment_date))}}</p>
            @endif  
            </div>
            <div>
            @if ($data->service_centre_dispatch == 0)
            <span class="btn btn-danger select-md">Dispatch from Service Centre</span>
            Yet To Dispatch</span>
            @elseif($data->service_centre_dispatch == 1 )
            <span class="btn btn-success select-md">Dispatch from Service Centre</span>
                <p>Dispatched</p>
                <p>Dispatched at: {{date('Y-m-d H:i ',strtotime($data->service_centre_dispatch_date))}}</p>
                @if($data->return_type == 'by_vehicle')
                <p>Return Vehicle Number: {{$data->return_vehicle_number}} </p>
                @elseif($data->return_type == 'by_transport')
                <p>Tranport File:  <a href="{{ asset($data->return_transport_file) }}" target="_blank" class="btn btn-outline-primary btn-sm">View</a> </p>
                @else
                <p>Tranported By Hand</p>
                @endif
            @endif  
            </div>
            <div>
            @if ($data->is_received_at_branch == 0)
            <span class="btn btn-danger select-md">Received at showroom</span>
            <p>Yet To received</p>
            @elseif($data->is_received_at_branch == 1 )
            <span class="btn btn-success select-md">Received at showroom</span>
            <p>Received Showroom Name: {{ $data->return_branch->name ?? 'N/A' }}</p>
            <p>Received at :{{date('Y-m-d H:i ',strtotime($data->is_received_at_branch_date))}}</p>
            @endif  
            </div>
            <div>
            @if ($data->verify_delivery_otp == 0)
            <span class="btn btn-danger select-md">Delivered to the Customer</span>
            <p>Yet To Deliver</p>
            @elseif($data->verify_delivery_otp == 1 )
            <span class="btn btn-success select-md">Delivered to the Customer</span>
            <p>Delivered</p>
            <p>Delived at: {{date('Y-m-d H:i ',strtotime($data->customer_delivery_time))}}</p>
            @endif
            </div>
            <div>
            <span class="btn btn-secondary select-md">Spares Details</span>
            <a href="{{ route('dap-services.dap-invoice', Crypt::encrypt($data->id)) }}" class="btn btn-outline-success select-md">Download Invoice </a>

                @if($data->FinalSpareParts)
                <table class="table">
                    <thead>
                        <tr>
                            <th class="sr_no">#</th>
                            <th class="primary_column">Title</th>
                            <th>Defective barcode no. </th>
                            <th>Action</th>
                            <th>Warranty status</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($data->FinalSpareParts as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->title }}</td>
                            <td>
                                <div class="col-12 page_bar" id="print_div" style="width: 260px;">
                                    <div class="barcode_image" style="margin: 0 auto 4px">
                                    <img class="" alt="Barcoded value {{$item->new_spare_barcode}}" src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{$item->new_spare_barcode}}&height=6&textsize=14&scale=6&includetext">
                                    <span>(DEFECTIVE)</span>
                                    </div>
                                </div>   
                            </td>
                            <td><a onclick='printResultHandler()' class="btn btn-outline-primary select-md">Download Barcode</a></br>
                            </td>
                            <td>{{ $item->warranty_status == 1 ? 'In-warranty' : 'Out of warranty' }}</td>
                            <td>{{ $item->final_amount }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">No record found</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                @endif
                </div>
            <!-- <a href="{{ route('dap-services.dap-invoice', Crypt::encrypt($data->id)) }}" class="btn btn-outline-success select-md">Download Invoice </a> -->

                <div>
                <span class="btn btn-secondary select-md">Bill Details</span><br>
                Total Amount:{{number_format($data->total_amount,2)}}<br>
                Discount Amount:{{number_format($data->discount_amount,2)}}<br>
                service Charge Amount:{{number_format($data->total_service_charge,2)}}<br>
                </div>



            </div>
  
        

</section>
<script>
    //  function downloadImage(name){
    //     var url = "https://bwipjs-api.metafloor.com/?bcid=code128&includetext&text="+name;
    //     console.log('Fetching URL:', url); // Debugging line
    //     fetch(url)
    //         .then(resp => resp.blob())
    //         .then(blob => {
    //             const url = window.URL.createObjectURL(blob);
    //             const a = document.createElement('a');
    //             a.style.display = 'none';
    //             a.href = url;
    //             // the filename you want
    //             a.download = name;
    //             document.body.appendChild(a);
    //             a.click();
    //             window.URL.revokeObjectURL(url);
    //         })
    //         .catch(() => alert('An error sorry'));
    // }

    function printResultHandler() {
        //Get the HTML of div
        var print_header = '';
        var divElements = document.getElementById("print_div").innerHTML;
        var print_footer = '';

        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML =
                "<html><head><title></title></head><body><font size='2'>" +
                divElements + "</font>" + print_footer + "</body>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
        //bindUnbind();
    }
</script>
@endsection

