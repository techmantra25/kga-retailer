@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Ledger')
<section>
    <ul class="breadcrumb_menu">
        <li>Report</li>
        <li>User Ledger</li>
    </ul>
    <div class="search__filter">
        <form action="" id="searchForm">
        <div class="row align-items-center justify-content-between">
            <div class="col">
               
            </div>            
            <div class="col-auto">
                   
            </div>           
            <div class="col-auto">                
                <div class="row g-3 align-items-center">
					<div class="col-auto">
						<label for="filter_type">Type</label>
						<select name="filter_type" class="form-control" id="filter_type">
							<option value="all" {{ ($filter_type ?? '') == 'all' ? 'selected' : '' }}>All</option>
						<!--	<option value="amc" {{ ($filter_type ?? '') == 'amc' ? 'selected' : '' }}>AMC Incentive</option> -->
						</select>
					</div>

                    <div class="col-auto">
                        <label for="">From</label>
                        <input type="date" name="from_date" class="form-control" id="from_date" value="{{ $from_date }}" >
                    </div>
                    <div class="col-auto">
                        <label for="">To</label>
                        <input type="date" name="to_date" value="{{ $to_date }}" class="form-control" id="to_date" max="{{ date('Y-m-d') }}" >
                    </div> 
                    <div class="col-auto align-self-end">
                        <input type="submit" value="Search" class="btn btn-success">
                        <a href="{{ route('servicepartnerweb.report.ledger') }}" class="btn btn-warning">Reset</a>
                        <a href="javascript:void(0)" onclick="downloadLedger('csv');" class="btn btn-outline-success">CSV</a>
                    </div>                    
                </div>                
            </div>
        </div>
        </form>
    </div>        
    <table class="table" id="installationTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Purpose</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Closing</th>
            </tr>
        </thead>
        <tbody>       
        @php
            $i=1;
            $net_value = $cred_value = $deb_value = 0;
            $cred_ob_amount = $deb_ob_amount = $zero_ob_amount = $cr_ob_amount = $dr_ob_amount = $zero_ob_amount = "";
            $net_value += $ob_amount;

            $ob_amount_cr_dr = getCrDr($ob_amount);
            if($ob_amount_cr_dr == 'Cr'){
                $cr_ob_amount = $ob_amount;
            } else if ($ob_amount_cr_dr == 'Dr'){
                $dr_ob_amount = $ob_amount;
            } else if ($ob_amount_cr_dr == ''){
                $zero_ob_amount = '';
            }
               
        @endphp 
        @if (!empty($data) && !empty($is_transaction))
        <tr>
            <td>
                {{ date('d/m/Y', strtotime($from_date)) }}
            </td>
            <td>
                
            </td>
            <td>
                Opening Balance
            </td>
            <td>
                <span class="text-danger">{{ $dr_ob_amount }}</span>
            </td>
            <td>
                <span class="text-success">{{ $cr_ob_amount }}</span>
            </td>
            <td>
                {{ replaceMinusSign($ob_amount) }} 
                        
                {{ getCrDr($ob_amount) }}
            </td>
        </tr>
        @endif
        @forelse ($data as $item)
        @php
            $debit_amount = $credit_amount = '';
            if(($item->type == 'credit')){
                $credit_amount = $item->amount;
                $net_value += $item->amount;
                $cred_value += $item->amount;
            }
            if(($item->type == 'debit')){
                $debit_amount = $item->amount;
                $net_value -= $item->amount;
                $deb_value += $item->amount;
            }
            
        @endphp
            <tr>
                <td>
                    {{ date('d/m/Y', strtotime($item->entry_date)) }}
                </td>
                <td>
                    {{ $item->transaction_id }}
                </td>
                <td>
                    {{ ucwords(str_replace("_"," ",$item->purpose)) }}
                </td>
                <td>
                    <span class="text-danger">{{ $debit_amount }}</span>
                </td>
                <td>
                    <span class="text-success">{{ $credit_amount }}</span>
                </td>
                <td>
                    {{ replaceMinusSign($net_value) }} 
                            
                    {{ getCrDr($net_value) }}
                </td>
            </tr>
            
            @php
                $i++;
            @endphp
        @empty
            @if (empty($is_transaction))
                <tr>
                    <td colspan="7" style="text-align: center;">
                        No data found
                    </td>
                </tr>
            @endif
        @endforelse
        
        @if (!empty($is_transaction))
        <tr class="table-info">
            <td><strong>Closing Amount</strong>  </td>
            {{-- <td></td> --}}
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>                            
                <strong>                                                               
                    {{ replaceMinusSign($net_value) }} {{ getCrDr($net_value)}}
                </strong>
            </td>
        </tr> 
        @endif
        
        
        </tbody>
    </table>
    
    
    
</section>
<style>
    .table tbody tr.table-info td {
        background: #cff4fc !important;
    }
</style>
<script>
    
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        $('#from_date').attr('max' ,  to_date);
        $('#to_date').attr('min' ,  from_date);
        
    })

    $('input[type=date]').on('change', function(){
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        $('#from_date').attr('max' ,  to_date);
        $('#to_date').attr('min' ,  from_date);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });

    function downloadLedger(e){        
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var filter_type = $('#filter_type').val();
        // if(user_type == ''){
        //     alert("Please get the record first");
        //     return true;
        // }

        var dataString = "from_date="+from_date+"&to_date="+to_date+"&filter_type="+filter_type ;
        
        if(e == 'csv'){
            window.location.href = "{{ route('servicepartnerweb.report.ledger-csv') }}?"+dataString; 
        }
        
    }
    
</script>  
@endsection 