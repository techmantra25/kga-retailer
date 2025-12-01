@extends('layouts.app')
@section('content')
@section('page', 'Credit Note')
<section>
    <form action="" id="searchForm">
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                <a href="{{route('accounting.add-credit-note')}}" class="btn btn-outline-primary select-md">Add New</a>    
               
            </div>
           
        </div>
    </div>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                    {{ Session::forget('message') }}
                </div>
                @endif
            </div>
            
        </div>
    </div>
    </form>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th width="10%">ID</th>
                <th>Entry Date</th>
                <th>Service Partner</th>
				<th>Ho Sale</th>
                <th>Amount</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
        @php
                 
            if(empty($page) || $page == 1){                
                $i=1;
            } else {
                $i = ((($page-1)*$paginate)+1);
            } 
        @endphp
        @forelse ($data as $item)
            <tr>
                <td>{{$i}}</td>
                <td>
                    {{$item->transaction_id}}
                </td>
                <td> {{date('j M Y, l', strtotime($item->entry_date))}} </td>
                <td>
                    <p class="small text-muted mb-1">
                        <span>
                            Name:
                            <strong>
                                {{$item->service_partner ? $item->service_partner->person_name : ""}} - {{$item->service_partner ? $item->service_partner->company_name : ""}}
                            </strong>                                
                        </span> <br/>
                        <span>
                            Phone / Email:
                            <strong>
                                {{$item->service_partner ? $item->service_partner->phone : ""}} / {{$item->service_partner ? $item->service_partner->email : ""}}
                            </strong>
                        </span>
                        
                    <p>
                </td>
				<td>
                   <p class="small text-muted mb-1">
						<span>
							Name:
							<strong>{{ optional($item->ho_sale)->name }}</strong>
						</span><br>
						<span>
							Phone / Email:
							<strong>{{ optional($item->ho_sale)->phone }} / {{ optional($item->ho_sale)->email }}</strong>
						</span>
					</p>
                </td>
                <td>
                    Rs. {{ number_format((float)$item->amount, 2, '.', '') }}
                </td>
                
                <td>
                    <!-- Remark modal -->
                    <button type="button" class="btn btn-outline-success select-md" title="{{$item->remarks}}"  data-bs-toggle="modal" data-bs-target="#remarkData{{$item->id}}">
                        View
                    </button>                    
                    <!-- Modal Remark -->
                    <div class="modal fade" id="remarkData{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropNarration" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropNarration"> {{$item->transaction_id}}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        <h6>REFERNCE ID:
											@if($item->call_type == 'amc')
											{{$item->amc_unique_number}}
											@else
											{{$item->call_no}}
											@endif
										</h6>
                                        @if (!empty($item->remarks))
                                            <p>{{$item->remarks}}</p>
                                        @else
                                            <p> No remarks found </p>
                                        @endif
                                        
                                    </div>                                        
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="7" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table>
    {{$data->links()}}
    
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#type').on('change', function(){
        $('#searchForm').submit();
    })
    $('#paginate').on('change',function(){
        $('#searchForm').submit();
    })

</script>  
@endsection 