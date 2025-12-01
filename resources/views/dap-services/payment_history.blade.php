@extends('layouts.app')
@section('content')
@section('page', 'DAP TRANSACTION HISTORY')
<section>
    <ul class="breadcrumb_menu">        
        <li>DAP TRANSACTION</li>
    </ul>    
    <form action="" id="searchForm">
    <div class="search__filter">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="row  justify-content-end">
           <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" value="{{ request()->input('start_date') }}" >
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" value="{{ request()->input('end_date') }}" >
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control form-control-sm" name="keyword" placeholder="Global Search..." value="{{request()->input('keyword')??""}}" class="w-100"/>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-warning">Search</button>
                <a href="{{route('dap-services.payment-history')}}" class="btn btn-warning">Reset</a>
            </div>
        </div>
    </div>
    </form>
    <div class="row">        
        <table class="table">
            <thead>
                <tr>
                    <th class="sr_no">#</th>
                    <th class="primary_column">DAP_ID</th>
                    <th>Payment Id</th>
                    <th>Customer Name</th>
                    <th>Customer Phone</th>
                    <th>Amount</th>
                    <th>Cashfree Customer Id</th>
                    <th>Cashfree Order Id</th>   
                    <th>Status</th> 
                    <th>Date</th> 
                
                </tr>
            </thead>
            <tbody>
                @php
                    if(empty(Request::get('page')) || Request::get('page') == 1){
                        $i=1;
                    } else {
                        $i = (((Request::get('page')-1)*$paginate)+1);
                    } 
                @endphp
                @forelse ($data as $item)
                <tr>
                    <td>{{$i}}</td>
                    <td>{{$item->dap_service_id}}</td>
                    <td>{{$item->payment_id}}</td>
                    <td>{{$item->customer_name}}</td>
                    <td>{{$item->customer_phone}}</td>
                    <td>{{number_format($item->amount,2)}}</td>
                    <td>{{$item->cashfree_customer_id}}</td>
                    <td>{{$item->cashfree_order_id}}</td>
                    <td>{{$item->status}}</td>
                    <td>{{date('d-M-Y', strtotime($item->created_at))}}</td>
                    

                </tr>

                @php
                    $i++;
                @endphp
                @empty
                <tr>
                    <td colspan="9" style="text-align: center;">No record found</td>
                </tr>  
                @endforelse
            </tbody>
        </table>
        {{$data->links()}}
    </div>  
          
</section>
<script>
  

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