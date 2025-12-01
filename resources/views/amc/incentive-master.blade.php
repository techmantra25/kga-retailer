@extends('layouts.app')
@section('content')
@section('page', 'AMC Incentives Master')

<section>
    <ul class="breadcrumb_menu">        
        <li>Set AMC Incentives for &nbsp; Service Partner  &nbsp; &nbsp;/&nbsp;&nbsp;    Service Head  &nbsp; &nbsp;& &nbsp;&nbsp;    Head-Office </li>
    </ul>
    <div class="col mb-2 mb-sm-0">
                @if (Session::has('message'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('message') }}
                </div>
                @endif
    </div>
    <table class="table">
        <thead>
            <tr class="text-center">
                <th class="primary_column">Incentives For</th>
				<th>Percentage (%)</th>
               <!-- <th>Action</th>  -->
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td><strong>Service Partner </strong></td>
                <td><strong>{{number_format($service_partner_intensive,2)}}</strong></td>
                <!-- <td><button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#service_partner"> Change </button></td>  -->
            </tr>       
            <tr class="text-center">
                <td><strong>Service Head</strong></td>
                <td><strong>{{number_format($service_head_intensive,2)}}</strong></td>
                <!--  <td><button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#service_head"> Change </button></td>  -->
            </tr>       
            <tr class="text-center">
                <td><strong>Admin</strong></td>
                <td><strong>{{number_format($head_office_intensive,2)}}</strong></td>
             <!--    <td><button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#head_office"> Change </button></td>  -->
            </tr> 
			 <tr class="text-center">
                <td><strong>Head Office</strong></td>
                <td><strong>{{number_format($new_head_office_incentive,2)}}</strong></td>
             <!--    <td><button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#head_office"> Change </button></td>  -->
            </tr> 
			 <tr class="text-center">
                <td><strong>Service Centre</strong></td>
                <td><strong>{{number_format($service_centre_incentive,2)}}</strong></td>
             <!--    <td><button type="button" class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#head_office"> Change </button></td>  -->
            </tr> 
        </tbody>
    </table>
    <!-- service_partner Modal-->
    <div class="modal fade" id="service_partner" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('amc.intensive-update') }}" method="post">
                @csrf
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Service Partner Incentives</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Percentage (%): <input type="number" step="0.01" class="form-control" name="service_partner_intensive" value="{{ number_format($service_partner_intensive,2) }}" />
                    <p class="text-muted">Note : Upon selling the AMC package to the customer, this incentive percentage of AMC package value will be reflected in the service partner's ledger.</p>
                    <input type="hidden" name="intensive_for" value="service_partner">
                    <input type="hidden" name="browser_name" class="browser_name">
                    <input type="hidden" name="navigator_useragent" class="navigator_useragent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- service_head Modal-->
    <div class="modal fade" id="service_head" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('amc.intensive-update') }}" method="post">
                @csrf
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Service Head Incentives</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Percentage (%): <input type="number" step="0.01" class="form-control" name="service_head_intensive" value="{{ number_format($service_head_intensive,2) }}" />
                    <p class="text-muted">Note : Upon selling the AMC package to the customer, this incentive percentage of AMC package value will be reflected in the service head's ledger.</p>
                    <input type="hidden" name="intensive_for" value="service_head">
                    <input type="hidden" name="browser_name" class="browser_name">
                    <input type="hidden" name="navigator_useragent" class="navigator_useragent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
            </div>
        </div>
    </div>
    <!-- head_office Modal-->
    <div class="modal fade" id="head_office" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('amc.intensive-update') }}" method="post">
                @csrf
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Head Office Incentives</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Percentage (%): <input type="number" step="0.01" class="form-control" name="head_office_intensive" value="{{ number_format($head_office_intensive,2) }}" />
                    <p class="text-muted">Note : Upon selling the AMC package to the customer, this incentive percentage of AMC package value will be reflected in the head's offic person ledger.</p>
                    <input type="hidden" name="intensive_for" value="head_office">
                    <input type="hidden" name="browser_name" class="browser_name">
                    <input type="hidden" name="navigator_useragent" class="navigator_useragent">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            <form>
            </div>
        </div>
    </div>


</section>
<!-- <script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
	$('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>   -->

<script>
    var navigator_useragent = '';
    function getBrowserType() {
        const test = regexp => {
            return regexp.test(navigator.userAgent);
        };
        console.log(navigator.userAgent);
        navigator_useragent = navigator.userAgent;
                
        $('#navigator_useragent').val(navigator_useragent);
        if (test(/opr\//i) || !!window.opr) {
            return 'Opera';
        } else if (test(/edg/i)) {
            return 'Microsoft Edge';
        } else if (test(/chrome|chromium|crios/i)) {
            return 'Google Chrome';
        } else if (test(/firefox|fxios/i)) {
            return 'Mozilla Firefox';
        } else if (test(/safari/i)) {
            return 'Apple Safari';
        } else if (test(/trident/i)) {
            return 'Microsoft Internet Explorer';
        } else if (test(/ucbrowser/i)) {
            return 'UC Browser';
        } else if (test(/samsungbrowser/i)) {
            return 'Samsung Browser';
        } else {
            return 'Unknown browser';
        }
    }
    const browserType = getBrowserType();
    console.log(browserType);
    $('.browser_name').val(browserType);
    
    $(document).ready(function(){
        $('.browser_name').val(browserType);
        $('.navigator_useragent').val(navigator_useragent);
        

    })
</script>
@endsection 