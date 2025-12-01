@extends('layouts.app')
@section('content')
@section('page', 'Payment')
<section>
    <form action="" id="searchForm">
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            <div class="col-auto">
                <a href="{{route('accounting.payment-add')}}" class="btn btn-outline-primary select-md">Add Payment</a>    
               
            </div>
            <div class="col-auto">
                <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search here.." autocomplete="off">
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
            
            <div class="col-auto">
                <p>Total {{$totalResult}} Records</p>
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
				 <th>To</th>
                <th>Amount</th>
                <th>Payment Mode</th>
                <th>Narration</th>
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
                    {{$item->voucher_no}}
                </td>
                <td> {{date('j M Y, l', strtotime($item->entry_date))}} </td>
                <td>
                    @if ($item->user_type == 'servicepartner')
                        <p class="small text-muted mb-1">
                            <strong>SERVICE PARTNER</strong> <br/>
                            <span>
                                Name:
                                <strong>
                                    {{$item->service_partner ? $item->service_partner->person_name : "" }} - {{$item->service_partner ? $item->service_partner->company_name : ""}}
                                </strong>                                
                            </span> <br/>
                            <span>
                                Phone / Email:
                                <strong>
                                    {{$item->service_partner ? $item->service_partner->phone : ""}} / {{$item->service_partner ? $item->service_partner->email : ""}}
                                </strong>
                            </span>
                           
                        <p>
                    @endif
                </td>
				 <td>
                    @if ($item->user_type == 'ho_sale')
                        <p class="small text-muted mb-1">
                            <strong>Ho Sale</strong> <br/>
                            <span>
                                Name:
                                <strong>
                                    {{$item->ho_sale ? $item->ho_sale->name : "" }} 
                                </strong>                                
                            </span> <br/>
                            <span>
                                Phone / Email:
                                <strong>
                                    {{$item->ho_sale ? $item->ho_sale->phone : ""}} / {{$item->ho_sale ? $item->ho_sale->email : ""}}
                                </strong>
                            </span>
                           
                        <p>
                    @endif
                </td>
                <td>
                    Rs. {{ number_format((float)$item->amount, 2, '.', '') }}
                </td>
                <td>
                    
                    <p class="small text-muted mb-1">
                        <strong>{{ strtoupper($item->payment_mode) }}</strong> <br/>
                        @if (!empty($item->bank_name))
                        <span>
                            Bank Name: <strong>{{ $item->bank_name }}</strong>
                        </span> <br/>
                        @endif
                        @if (!empty($item->bank_name))
                        <span>
                            Cheque / UTR No: <strong>{{ $item->chq_utr_no }}</strong>
                        </span> <br/>
                        @endif
                    </p>
                </td>
                <td>
                    <!-- Remark modal -->
                    <button type="button" class="btn btn-outline-success select-md" title="{{$item->narration}}"  data-bs-toggle="modal" data-bs-target="#remarkData{{$item->id}}">
                        View
                    </button>                    
                    <!-- Modal Remark -->
                    <div class="modal fade" id="remarkData{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropNarration" aria-hidden="true">
                        <div class="modal-dialog modal-md">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropNarration"> {{$item->voucher_no}}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div>
                                        @if (!empty($item->narration))
                                            <p>{{$item->narration}}</p>
                                        @else
                                            <p> No narration found </p>
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