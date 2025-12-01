@extends('layouts.app')
@section('content')
@section('page', 'PIN Codes')
<section>
    <div class="row">
        <div class="col-xl-7 order-2 order-xl-1">
            <div class="search__filter">
                <div class="row align-items-center justify-content-between">
                    <div class="col">
                        <ul>
                            <li @if(!Request::get('status') || (Request::get('status') == 'all')) class="active" @endif><a href="{{route('service-partner.list-pincode')}}">All <span class="count">({{$total}})</span></a></li>
                            <li @if(Request::get('status') == 'active' ) class="active" @endif><a href="{{route('service-partner.list-pincode',['status'=>'active'])}}">Active <span class="count">({{$totalActive}})</span></a></li>
                            <li @if(Request::get('status') == 'inactive' ) class="active" @endif><a href="{{route('service-partner.list-pincode',['status'=>'inactive'])}}">Inactive <span class="count">({{$totlInactive}})</span></a></li>
                        </ul>
                    </div>
                    <div class="col-auto">
                                     
                    </div>
                    <div class="col-auto">
                        <form action="" id="searchForm">
                        <input type="hidden" name="status" value="{{$status}}">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <input type="search" name="search" value="{{$search}}" class="form-control" placeholder="Search here..">
                            </div>
                            <div class="col-auto">
                                {{-- <button type="submit" class="btn btn-outline-primary btn-sm">Search </button> --}}
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="filter">
                <div class="row align-items-center justify-content-between">
                    <div class="col">
                        @if (Session::has('message'))
                        <div class="alert alert-success" role="alert">
                            {{ Session::get('message') }}
                        </div>
                        @endif
                        @if (Session::has('errmessage'))
                        <div class="alert alert-danger" role="alert">
                            {{ Session::get('errmessage') }}
                        </div>
                        @endif
                    </div>
                    
                    <div class="col-auto">
                        <p>{{$totalResult}} Items</p>
                    </div>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Created At</th>
                        <th>Pin Code</th>
                        <th>Created From</th>        
                        <th>Status</th>
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
                        <td>
                        {{date('d/m/Y', strtotime($item->created_at))}}
                        <div class="row__action">                            
                            @if(!empty($item->status))
                            <a href="{{route('service-partner.toggle-status-pincode', [$item->id,Request::getQueryString()])}}" class="text-danger">Inactive</a>
                            @else
                            <a href="{{route('service-partner.toggle-status-pincode', [$item->id,Request::getQueryString()])}}" class="text-success">Active</a>
                            @endif
                        </div>
                        </td>
                        <td>{{$item->number}}</td>
                        <td>
                            @if(!empty($item->is_csv_uploaded))
                            <span class="badge bg-warning">CSV</span>
                            @else
                            <span class="badge bg-warning">Manually</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($item->status))
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>       
                    @php
                        $i++;
                    @endphp     
                @empty
                <tr>
                    <td>
                        No data found
                    </td>
                </tr>
                @endforelse
                    
                </tbody>
            </table>
            {{$data->links()}}
        </div>
        <div class="col-xl-5 order-2 order-xl-1">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('service-partner.save-pincode') }}">
                        @csrf
                        <h4 class="page__subtitle">Add New PIN Code</h4>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-12">
                                <div class="form-group mb-3">
                                    <label class="label-control">Number <span class="text-danger">*</span> </label>
                                    <input type="text" autocomplete="off" name="number" placeholder="Enter pin number" class="form-control" value="{{ old('number') }}" onkeypress="return isNumberKey(this, event);" maxlength="10"
                                    >
                                    @error('number') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>                                
                            </div>                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm btn-success">Save</button>
                            </div>
                        </div>
                    </form>
                    <h4 class="page__subtitle">Or</h4>
                    <form id="myUploadForm" action="{{ route('service-partner.upload-pincodes') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <h4 class="page__subtitle">Upload CSV for PIN Code</h4>
                        <div class="row">
                            <div class="col-12 col-md-6 col-xl-12">
                                <div class="form-group mb-3">
                                    <label class="label-control">Upload CSV <span class="text-danger">*</span> </label>
                                    <input type="file" name="csv" class="form-control" id="" accept=".csv">
                                    @error('csv') <p class="small text-danger">{{ $message }}</p> @enderror
                                </div>                                
                            </div>                            
                            <div class="form-group">
                                <button type="submit" id="uploadBtn" class="btn btn-sm btn-success">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>            
        </div>
    </div>
</section>
<script>
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    })
    $("#myUploadForm").submit(function() {
        $('input').attr('readonly', 'readonly');
        $('#uploadBtn').attr('disabled', 'disabled');     
        $('#uploadBtn').html('<i class="fi fi-br-refresh"></i>');   
        return true;
    });
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    function isNumberKey(txt, evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 46) {
            //Check if the text already contains the . character
            if (txt.value.indexOf('.') === -1) {
                return true;
            } else {
                return false;
            }
        } else {
            if (charCode > 31 &&
                (charCode < 48 || charCode > 57))
                return false;
        }
        return true;
    }
</script>  
@endsection 