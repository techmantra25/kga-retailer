@extends('layouts.app')
@section('content')
@section('page', 'Service Centre Reached Items')
<section>
    <ul class="breadcrumb_menu">        
        <li>Inhouse DAP Servicing</li>
        <li>Service Centre Reached Items</li>
    </ul>    
    <form action="" id="searchForm">
    <div class="search__filter">
        @if (Session::has('message'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('message') }}
        </div>
        @endif
        <div class="row  justify-content-end">
            <div class="col-md-7">
                <div class="input-group">
                    <input type="search" name="search" value="{{$search}}" class="form-control " placeholder="Search Item ...">
                    {{-- <input type="hidden" name="" value="submit">     --}}
                    <div class="input-group-append">
                      <a href="{{ route('dap-services.centre-reached-items') }}?branch_id={{$branch_id}}&branch_name={{$branch_name}}" class="btn btn-outline-secondary" id="">Reset</a>
                    </div>                    
                </div>
            </div>            
        </div>
        
        
    </div>
    <div class="search__filter">
        <div class="row  justify-content-end">
            <div class="col">

            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="branch_name" class="form-control" placeholder="Search branch where item dropped at..." onkeyup="searchBranch(this.value);" id="branch_name" value="{{ $branch_name }}" autocomplete="off">
                    <input type="hidden" name="branch_id" id="branch_id" value="{{ $branch_id }}">
                
                    
                    <div class="input-group-append">
                      <a href="{{ route('dap-services.centre-reached-items') }}?search={{$search}}" class="btn btn-outline-secondary" id="">Reset</a>
                    </div>                    
                </div>
                <div class="respBranch" id="respBranch" style="position: relative;"></div>

            </div>
            <div class="col-md-3">
                <select name="service_centre_id" class="form-control" id="service_centre_id">
                    <option value="" hidden selected>Select Service Centre</option>
                    @forelse ($sc as $c)
                    <option value="{{$c['id']}}" @if($service_centre_id == $c['id']) selected @endif>{{$c['name']}}</option>
                    @empty
                        
                    @endforelse
                    
                </select>
            </div>            
            
            {{-- <div class="col-auto">
                <a href="{{ route('dap-services.centre-reached-items') }}?search={{$search}}" class="btn btn-warning ">Reset Branch & Centre</a>
            </div> --}}
        </div>
    </div>
    <div class="search__filter">        
        <input type="hidden" name="receive_type" value="{{$received_type}}">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                <ul>
                    <li @if(empty($received_type)) class="active" @endif><a href="{{ route('dap-services.centre-reached-items') }}">All </a></li>
                    <li @if($received_type == 'notreceived') class="active" @endif><a href="{{ route('dap-services.centre-reached-items') }}?received_type=notreceived">Yet To Receive </a></li>
                    <li @if($received_type == 'received') class="active" @endif><a href="{{ route('dap-services.centre-reached-items') }}?received_type=received"> Received </a></li>
                </ul>
            </div>            
        </div>        
    </div>
    </form>
    <div class="filter">
        <div class="row align-items-center justify-content-between">
            <div class="col">
                
            </div>            
            <div class="col-auto">
                <p>{{$totalResult}} Items</p>
            </div>
        </div>
    </div>
    <div class="row">        
        <table class="table">
            <thead>
                <tr>
                    <th class="sr_no">#</th>
                    <th class="primary_column">ID</th>
                    <th>Showroom</th>
                    <th>Item</th>   
                    <th>Receiving Status</th>
                    <th>Action</th>
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
                    <td class="sr_no">{{$i}}</td>
                    <td class="primary_column">
                        {{ $item->dap_request->unique_id }}
                        <button type="button" class="toggle_table"></button>
                    </td>
                    <td data-colname="Showroom">{{ $item->dap_request->branch->name }}</td>
                    <td data-colname="Item">
                        <p class="small text-muted mb-1">
                            <span>Serial: <strong>{{ $item->serial }}</strong></span> <br/>
                            <span>Item: <strong>{{ $item->item }}</strong></span> <br/>
                            <span>Class: <strong>{{ $item->dap_request->class_name }}</strong></span> <br/>
                            <span>Barcode: <button class="showdetails" title="Download Barcode" onclick="downloadImage('{{$item->barcode}}')">{{ $item->barcode }}</button></span> <br/>
                        </p>
                    </td>
                   
                    <td data-colname="Receiving Status">
                        @if (!empty($item->is_service_centre_received))
                            <span class="badge bg-success">Received</span>
                        @else
                            <span class="badge bg-danger">Yet To Receive</span>
                        @endif
                    </td>
                    <td data-colname="Action">
                        
                    </td>
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
    $(document).ready(function(){
        $('div.alert').delay(3000).slideUp(300);
    }); 
    
    $('input[type=search]').on('search', function () {
        // search logic here
        // this function will be executed on click of X (clear button)
        $('#searchForm').submit();
    });
    $('#service_centre_id').on('change', function(){
        $('#searchForm').submit();
    })
    function searchBranch(search){
        if(search.length > 0) {
            $.ajax({
                url: "{{ route('ajax.search-branches') }}",
                method: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    search: search
                },
                success: function(result) {
                    console.log(result);
                    var content = '';
                    if (result.length > 0) {
                        content += `<div class="dropdown-menu show  branch-dropdown select-md" aria-labelledby="dropdownMenuButton" style="width: 100%;">`;

                        $.each(result, (key, value) => {                            
                            content += `<a class="dropdown-item" href="javascript: void(0)" onclick="fetchBranch(${value.id},'${value.name}')">${value.name} </a>`;
                        })
                        content += `</div>`;
                        // $($this).parent().after(content);
                    } else {
                        content += `<div class="dropdown-menu show  branch-dropdown select-md" aria-labelledby="dropdownMenuButton"><li class="dropdown-item">No branch found</li></div>`;
                    }
                    $('#respBranch').html(content);
                }
            });
        } else {
            $('.branch-dropdown').hide()
        }
        
    }

    function fetchBranch(id,name) {
        $('.branch-dropdown').hide()
        $('#branch_id').val(id);
        $('#branch_name').val(name);
        $('#searchForm').submit();
    }
    function downloadImage(name){
        var url = "https://bwipjs-api.metafloor.com/?bcid=code128&includetext&text="+name;

        fetch(url)
            .then(resp => resp.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                // the filename you want
                a.download = name;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(() => alert('An error sorry'));
    }
    $('.toggle_table').click(function(){
		$(this).parents('tr').toggleClass('is-expanded');
	});
</script>
@endsection