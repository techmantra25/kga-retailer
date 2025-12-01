@extends('layouts.app')
@section('content')
@section('page', 'Dashboard')
    <section>
        {{-- @php
            $is_service_partner_only = false;
            if(Auth::user()->id == 8){
                $is_service_partner_only = true;
                // die("Hi");
            }
        @endphp --}}

        @php
            // $is_service_partner_only = false;
            // if(Auth::user()->id == 8){
            //     $is_service_partner_only = true;
            //     // die("Hi");
            // }
            $accessUserManagment = userAccess(Auth::user()->role_id,1);
            $accessServicePartner = userAccess(Auth::user()->role_id,2);
            $accessInstallation = userAccess(Auth::user()->role_id,3);
            $accessRepair = userAccess(Auth::user()->role_id,4);
            $accessIncompleteInstallation = userAccess(Auth::user()->role_id,5);
            $accessChimneyMaintenance = userAccess(Auth::user()->role_id,6);
            $accessInhouseDapServicing = userAccess(Auth::user()->role_id,7);
            $accessProductManagement = userAccess(Auth::user()->role_id,8);
            $accessPO = userAccess(Auth::user()->role_id,9);
            $accessStock = userAccess(Auth::user()->role_id,10);
            $accessSalesOrder = userAccess(Auth::user()->role_id,11);
            $accessReport = userAccess(Auth::user()->role_id,12);
            $accessAccounting = userAccess(Auth::user()->role_id,13);
            $accessSupplier = userAccess(Auth::user()->role_id,14);
            $accessDealer = userAccess(Auth::user()->role_id,15);
            $accessamcManagement = userAccess(Auth::user()->role_id,17);
        @endphp

        
        <div class="row">    
            @if ($accessDealer)                     
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-success" onclick="location.href='{{route('dealers.list')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>Dealers <i class="fi fi-br-users"></i></h4>
                        <h2> {{$countDealers}}</h2>
                    </div>
                </div>
            </div>
            @endif   
            {{-- <div class="col-sm-3">
                <div class="card home__card bg-gradient-success" onclick="location.href='{{route('customer.list')}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Customers <i class="fi fi-br-users"></i></h4>
                        <h2> {{$countCustomers}}</h2>
                    </div>
                </div>
            </div> --}}
            @if ($accessSupplier)
                
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('supplier.list')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>Suppliers <i class="fi fi-br-users-alt"></i></h4>
                        <h2> {{$countSuppliers}}</h2>
                    </div>
                </div>
            </div>
            @endif
            @if ($accessServicePartner)
                
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-info" onclick="location.href='{{route('service-partner.list')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>Service Partners <i class="fi fi-br-users"></i></h4>
                        <h2> {{$countServicePartners}}</h2>
                    </div>
                </div>
            </div>
            @endif
            @if ($accessProductManagement)
                
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-secondary" onclick="location.href='{{route('product.list')}}?type=sp'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>Spares <i class="fi fi-br-cube"></i></h4>
                        <h2> {{$countSpares}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-secondary" onclick="location.href='{{route('product.list')}}?type=fg'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>Finish Goods <i class="fi fi-br-cube"></i></h4>
                        <h2> {{$countGoods}}</h2>
                    </div>
                </div>
            </div>
            @endif
            @if ($accessPO)
                
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-info" onclick="location.href='{{route('purchase-order.list')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>Purchase Order <i class="fi fi-br-truck-container"></i></h4>
                        <h2> {{$countPO}}</h2>
                    </div>
                </div>
            </div>
            @endif
            @if ($accessSalesOrder)
                
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('sales-order.list')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>Sales Orders <i class="fi fi-br-truck-container"></i></h4>
                        <h2> {{$countSales}}</h2>
                    </div>
                </div>
            </div>            
            
            @endif
            @if ($accessPO)
                
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-success" onclick="location.href='{{route('grn.index')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>GRN <i class="fi fi-br-truck-couch"></i></h4>
                        <h2> {{$countGRN}}</h2>
                    </div>
                </div>
            </div>
            @endif
            @if ($accessSalesOrder)
                
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-success" onclick="location.href='{{route('invoice.list')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>Invoices <i class="fi fi-br-file-invoice"></i></h4>
                        <h2> {{$countInvoice}}</h2>
                    </div>
                </div>
            </div>
            @endif
        </div>    
		@if(!$accessamcManagement || $accessUserManagment)
        <div class="row">
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-secondary" onclick="location.href='{{route('kga-daily-stock')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>KGA Daily Stock <i class="fi fi-br-cube"></i></h4>

                        <small>(Showrooms)</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-secondary" onclick="location.href='{{route('kga-daily-sales')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>KGA Daily Sales <i class="fi fi-br-cube"></i></h4>

                        <small>(Showrooms)</small>
                    </div>
                </div>
            </div>
            
        </div>
		@endif
		
        <div class="row">
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('amc.search')}}'" style="cursor: pointer" title="Click to view details">
                    <div class="card-body">
                        <h4>AMC<i class="fi fi-br-cube"></i></h4>

                        <small>(Annual Maintenance Charge)</small>
                    </div>
                </div>
            </div>
        </div>
		
        <div class="row">
            @if ($accessStock)
            <div class="col-lg-4">
                <h6>Get Barcode Details</h6>
                <form action="{{ route('stock.product-by-barcode') }}" method="GET">   
                    <input type="hidden" name="back_to" value="home">                 
                <div class="row">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="Enter Barcode To Search Item Inventory Details ... "  required autocomplete="off">
                        <div class="input-group-append">
                          <button class="btn btn-outline-success" type="submit">Search</button>
                        </div>
                      </div>
                </div>
                </form>
            </div>
            @endif
            @if ($accessInstallation)                
            <div class="col-lg-4">
                <h6>Search Installation Details</h6>
                <form action="{{ route('installation.list') }}" method="GET">   
                    <input type="hidden" name="back_to" value="home">                 
                <div class="row">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="Enter Installation ID "  required autocomplete="off">
                        <div class="input-group-append">
                          <button class="btn btn-outline-success" type="submit">Search</button>
                        </div>
                      </div>
                </div>
                </form>
            </div>
            @endif
            @if ($accessRepair)
            <div class="col-lg-4">
                <h6>Search Repair Details</h6>
                <form action="{{ route('repair.list') }}" method="GET">   
                    <input type="hidden" name="back_to" value="home">                 
                <div class="row">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="search" placeholder="Enter Repair ID "  required autocomplete="off">
                        <div class="input-group-append">
                          <button class="btn btn-outline-success" type="submit">Search</button>
                        </div>
                      </div>
                </div>
                </form>
            </div>
            @endif
        </div>
        @if (in_array(Auth::user()->role_id,[1,2]))
            
        <div class="row">
            <div class="col-lg-4">
                <h6>Changelog Report</h6>
                <form action="{{ route('export-csv-changelog') }}" method="GET">   
                    <input type="hidden" name="back_to" value="home">                 
                <div class="row">
                    <div class="input-group mb-3">
                        
                        <input @if(!empty($uploaded_at)) type="date" @else type="text" onfocus="(this.type='date')" placeholder="Select Date" @endif  class="form-control select-md"  max="{{date('Y-m-d')}}"  id="uploaded_at" required name="log_date">


                        <div class="input-group-append">
                          <button class="btn btn-outline-success" type="submit">Export CSV</button>
                        </div>
                      </div>
                </div>
                </form>
            </div>
            
        </div>
        @endif
        
        
    </section>  
@endsection   
<script>
    // $('#uploaded_at').on('change', function(){
    //     $('.filter-waiting-text').text('Please wait ... ');
    //     $('#uploaded_at_val').val(this.value);
        
    // });
    // $('#uploaded_at').on('keydown', function(){
    //     $('.filter-waiting-text').text('Please select date by clicking on calender icon ... ');
    //     $('.filter-waiting-text').delay(3000).fadeOut('slow');
    //     return false;
    // });

    
</script>  