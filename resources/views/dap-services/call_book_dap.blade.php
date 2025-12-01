@extends('layouts.app')
@section('content')
@section('page', 'Send To Service Centre')
<section>
<div class="row">        
<p>Total Items: {{ $totalResult }}</p>
        <table class="table">
            <thead>
                <tr>
                    <th class="sr_no">#</th>
                    <th>Item</th>   
                    <th>Dap_ID</th>   
                    <th>Barcode</th> 
                    <th>Road Challan</th> 
                    <th>Dispatched</th>
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
                        <p class="small text-muted mb-1">
                            <span>Serial: <strong>{{ $item->serial }}</strong></span> <br/>
                            <span>Item: <strong>{{ $item->item }}</strong></span> <br/>
                            <span>Class: <strong>{{ $item->class_name }}</strong></span> <br/>
                            <span>Barcode: <button class="showdetails" title="Download Barcode" onclick="downloadImage('{{$item->barcode}}')">{{ $item->barcode }}</button></span> <br/>
                        </p>
                    </td>  
                    <td>{{$item->unique_id}}</td>

                    <td> <a href="{{route('dap-services.dap-barcode',  Crypt::encrypt($item->id))}}" class="btn btn-outline-primary btn-sm" title="Edit">Barcode</a></td>
                    <td>
                        @if($item->wearhouse_id == null && $item->vehicle_number == null)
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal{{$item->id}}">
                        Generate Road  challan
                    </button>
                        @else
                        <a href="{{route('dap-services.download-road-challan-new',$item->unique_id)}}" class="btn btn-success">Download Road Challan</a>
                        @endif
                    </td>    
                    <td>
                        @if ($item->is_dispatched_from_branch == 0)
                        <span class="badge bg-danger">Yet To Dispatch</span>
                        @else
                        <span class="badge bg-success">Dispatched</span>
                        @endif
                    </td>  


                    <div class="modal fade" id="exampleModal{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Road Challan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post" action="{{route('dap-services.generate-road-challan-new')}}">
                                @csrf
                            <div class="modal-body">
                             <p>Service Issue Note No : <strong>{{$item->unique_id}}</strong></p>
                             <label>Send to wearhoue:</label>
                                <select class="form-control" name="wearhouse">
                                    @foreach ($serviceCentres as $centre)
                                        <option selected value="{{ $centre->id }}">{{ $centre->name }}</option>
                                    @endforeach
                                </select>
                             <label>Enter Vehicle Number</label>
                             <input type="text" name="vehicle_number" class="form-control">
                             <input type="hidden" name="dap_id" value="{{$item->id}}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Generate</button>
                            </div>
                            </form>
                            </div>
                        </div>
                    </div>
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
</script>
@endsection