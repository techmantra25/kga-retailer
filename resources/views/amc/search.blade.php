@extends('layouts.app')
@section('content')
@section('page', 'AMC Search')
@section('small', '(AMC Plan List In Days With Products)')

<section>
    <div class="col mb-2 mb-sm-0">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            <strong>{{ Session::get('message') }}</strong>
        </div>
        @endif
        @if (Session::has('error'))
        <div class="alert alert-danger" role="alert">
            {{ Session::get('error') }}
        </div>
        @endif
    </div>
    <form action="" id="searchForm">
        <div class="search__filter">
            <div class="row align-items-center justify-content-between">
                <div class="col">

                </div>
                <div class="col-2">
                    <select name="plan_type" class="form-control select-md" id="plan_type">
                        <option value="">Search by AMC Types</option>
                        @foreach($amc_plan as $item)
                        <option value="{{$item->id}}" @if($plan_type == $item->id) selected @endif >{{$item->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2">
                    <select name="duration_type" class="form-control select-md" id="duration_type">
                        <option value="">Search by Duration( days )</option>
                        @foreach($amc_duration as $item)
                        <option value="{{$item->duration}}" @if($duration_type == $item->duration) selected @endif  >{{$item->duration}}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-4">
                    <input type="search" name="search" value="{{$search}}" class="form-control select-md" placeholder="Search by product name here..">
                    
                </div>
                <div class="col-auto">
                    <a href="{{ route('amc.search') }}" class="btn btn-outline-warning select-md">Reset Page</a>   
                </div>
                
            </div>
        </div>
        <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <span class="small filter-waiting-text" id=""></span>                
            </div>
            <div class="col-auto">
                Number of rows:
            </div><div class="col-auto p-0">
                <select name="paginate" id="paginate" class="form-control select-md" id="">
                    <option value="25" @if($paginate == 25) selected @endif>25</option>
                    <option value="50" @if($paginate == 50) selected @endif>50</option>
                    <option value="100" @if($paginate == 100) selected @endif>100</option>
                    <option value="200" @if($paginate == 200) selected @endif>200</option>
                </select>
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
                <th class="sr_no">#</th>
                <th class="primary_column">Name</th>
                <th>Plan Name</th>
                <th>Duration( days )</th>
                <th>Amount</th>
                {{-- for only Admin role --}}
                @if($logedInUser->role_id==1)
                    <th>Action</th>
                @endif
            </tr>
        </thead>
        <tbody>
        @php
            // echo $request->page; die;
            // $page = Request::get('page')?Request::get('page'):1;   
                  
            if(empty($page) || $page == 1){                
                $i=1;
            } else {
                $i = ((($page-1)*$paginate)+1);
            } 
        @endphp
        @forelse ($data as $item)
            <tr>
                <td class="sr_no">{{$i}}</td>
                <td class="sr_no">{{$item->title}}</td>
                <td class="sr_no">{{$item->AmcPlanData?$item->AmcPlanData->name:"No Plan Found"}}</td>
                <td class="sr_no">{{$item->duration}}</td>
                <td class="sr_no">{{number_format($item->amount,2)}}</td>
                @if($logedInUser->role_id==1)
                    <td class="sr_no"><button  class="btn btn-outline-primary select-md" data-bs-toggle="modal" data-bs-target="#amc_amount_update{{$item->id}}" title="Edit Amount" > Edit Amount</button></td>
                @endif	
            </tr>


                 <!-- amount update Modal-->
                <div class="modal fade" id="amc_amount_update{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('amc.update-amc-product-amount') }}" method="post">
                            @csrf
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">AMC Plan (Product Amount Update)</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h6>Product Name: <span class="badge rounded-pill bg-secondary">{{$item->title}}</span></h6>
                                <h6>Plan Name: <span class="badge rounded-pill bg-secondary">{{$item->AmcPlanData?$item->AmcPlanData->name:"No Plan Found"}}</span></h6>
                                <h6>Duration: <span class="badge rounded-pill bg-secondary">{{$item->duration}}</span></h6>
                                <input type="number" class="form-control" placeholder="Enter amount" name="amount" value="{{$item->amount}}" required/>
                                <input type="hidden" name="id" value="{{$item->id}}">
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
            @php
                $i++;
            @endphp
        @empty
            <tr>
                <td colspan="12" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table>
    {{$data->links()}}
    
</section>
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


   $('#plan_type').on('change', function(){
        $('#searchForm').submit();
    })
   $('#duration_type').on('change', function(){
        $('#searchForm').submit();
    })
   $('#paginate').on('change', function(){
        $('#searchForm').submit();
    })
</script>  
@endsection 