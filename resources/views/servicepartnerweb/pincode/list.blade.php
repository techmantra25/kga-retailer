@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'My PIN Codes')
<section>
    <div class="search__filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>
            
            <div class="col-auto">
                <form action="" id="searchForm">
                <div class="row g-3 align-items-center">
                   
                    <div class="col-auto">
                        <input type="search" name="search" value="{{$search}}" class="form-control" placeholder="Search PIN Code ">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <form action="{{ route('service-partner.removepincdoebulk',$service_partner_id) }}" method="POST">
        @csrf
        <div class="filter">
            <div class="row align-items-center justify-content-between">
                <div class="col">
                    @if (Session::has('message'))
                    <div class="alert alert-success" role="alert">
                        {{ Session::get('message') }}
                    </div>
                    @endif
                </div>   
                <div class="col-auto">
                    <p>{{$totalResult}} Items</p>
                </div>         
            </div>
        </div>
        <ul class="pincodeclass">
            @foreach ($data as $item)
            <li>{{$item->number}}</li>
            @endforeach
            
            
        </ul>
    </form>
       
</section>
<script>
        
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    
</script>  
@endsection 