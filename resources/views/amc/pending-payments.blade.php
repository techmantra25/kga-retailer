@extends('layouts.app')

@section('content')
@section('page', 'Pending Payments For Amc')

<section>
   <ul class="breadcrumb_menu d-flex justify-content-between">
        <a href="{{ route('amc.ho-sale') }}" class="btn btn-outline-danger select-md" title="Back to Ho-sale">Back to Ho-Sale</a>
  </ul>

    <form action="{{ route('amc.pending-payment') }}" method="GET">
        <div class="search__filter">
            <div class="row align-items-center justify-content-between d-flex">
                <div class="col-3">
                    <input type="search" name="search" value="{{ $search }}" 
                           class="form-control" placeholder="Search by name, mobile, serial...">
                </div>
                
              <!--  <div class="col-3">
                    <input type="date" name="from_date" value="{{ $from_date }}" 
                           class="form-control" placeholder="From date">
                    <input type="date" name="to_date" value="{{ $to_date }}" 
                           class="form-control mt-1" placeholder="To date">
                </div>  
				<div class="col-auto">
					Number of rows:
				</div>
                <div class="col-2"> 
                    <select name="paginate" class="form-control">
                        <option value="25" @if($paginate == 25) selected @endif>25 rows</option>
                        <option value="50" @if($paginate == 50) selected @endif>50 rows</option>
                        <option value="100" @if($paginate == 100) selected @endif>100 rows</option>
                    </select>
                </div>
				-->
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('amc.pending-payment') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
							<th>KGA-Sales-Id</th>
							<th>AMC Unique Number</th>
							<th>Package Details</th>
                            <th>Amounts</th>
							<th>Status</th>
                            <th>Request Date</th>
							<th>Process Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPayments as $payment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
							<td><a href="{{ route('amc.ho-sale',['search' => $payment->kga_sales_id,'search_by' => 'id'])}}" class="btn btn-outline-dark select-md">{{$payment->kga_sales_id }}</a></td>
							<td>
                                  {{ $payment->amc_unique_number }}
                            </td>
							<td>
								@if($payment->productAmc && $payment->productAmc->AmcPlanData)
								<p class="small text-muted mb-1">
									<span>Plan Name: <strong>{{$payment->productAmc->AmcPlanData->name}}</strong></span> <br/>
									<span>Duration: <strong>{{$payment->productAmc->duration}} Days</strong></span> <br/>
								</p>
								@endif
					        </td>
                            <td>
                                <div class="amount-details">
                                    <div>Actual: ₹{{ number_format($payment->actual_amount, 2) }}</div>
                                    <div>Discount: ₹{{ number_format($payment->discount, 2) }}</div>
                                    <div class="font-weight-bold">
                                        Net: ₹{{ number_format($payment->purchase_amount, 2) }}
                                    </div>
                                </div>
                            </td>
							<td>
							  @if($payment->status == 0)
								<span class="badge bg-warning text-dark">Pending</span>
							  @endif 
							</td>
                            <td>{{ $payment->created_at->format('d M Y h:i A') }}</td>
							<td>
								<a href="{{ route('amc.prepare-for-purchase-amc-plan', [
										'kga_sale_id' => $payment->kga_sales_id,
										'amc_id' => Crypt::encrypt($payment->amc_id)
									]) }}" 
									   class="btn btn-sm btn-success"
									   title="Process Payment">
										<i class="fas fa-credit-card"></i> Pay Now
								</a>
							</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No pending payments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>Showing {{ $pendingPayments->firstItem() }} to {{ $pendingPayments->lastItem() }} of {{ $totalResult }} entries</div>
                <div>{{ $pendingPayments->links() }}</div>
            </div>
        </div>
    </div>
</section>




<script>
$(document).ready(function() {
    $('.view-details').click(function() {
        const details = JSON.parse($(this).data('details'));
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Customer Details</h6>
                    <p>Name: ${details.kga_sale_data.customer_name}<br>
                    Mobile: ${details.kga_sale_data.mobile}<br>
                    Address: ${details.kga_sale_data.address}</p>
                </div>
                <div class="col-md-6">
                    <h6>Payment Details</h6>
                    <p>AMC Number: ${details.amc_unique_number}<br>
                    Requested At: ${new Date(details.created_at).toLocaleString()}<br>
                    Last Updated: ${new Date(details.updated_at).toLocaleString()}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h6>Amount Breakdown</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Actual Amount</th>
                            <td>₹${details.actual_amount.toFixed(2)}</td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td>₹${details.discount.toFixed(2)}</td>
                        </tr>
                        <tr class="table-primary">
                            <th>Net Amount</th>
                            <td>₹${details.purchase_amount.toFixed(2)}</td>
                        </tr>
                    </table>
                </div>
            </div>
        `;
        
    });
});
</script>


@endsection