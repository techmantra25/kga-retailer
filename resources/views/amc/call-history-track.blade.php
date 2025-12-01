@extends('layouts.app')
@section('content')
@section('page', 'AMC Call History')


<section>
    <ul class="breadcrumb_menu justify-content-between">        
        <li>KGA-Sales-Id : <strong>{{$id}}</li>
        <a href="{{ route('amc.ho-sale') }}" class="btn btn-outline-primary select-md"> Back </a>
    </ul>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 17%;">#</strong></th>
                <th>Status</th>
                <th class="primary_column">Call Date</th>
                <th>Reminder Date</th>
                <th>Attened By</th>
                <th>Remarks</th>
                <th>Ip</th>

            </tr>
        </thead>
        <tbody>
       
        @forelse ($data as $item)
            <tr>
                <td class="sr_no">{{$loop->iteration }}</td>
                <td>
                    {!! $item->type === 1 
                        ? '<span class="bg-success text-white px-2 py-1">Call Back</span>' 
                        : '<span class="bg-danger text-white px-2 py-1">Refused</span>' !!}
                </td>
                <td>{{ date('j M Y, l ,H:i A', strtotime($item->created_at)) }}</td>
                <td>{{ $item->reminder_date?date('j M Y, l', strtotime($item->reminder_date)):"N/A" }}</td>
                <td>{{ $item->userData? $item->userData->name:"N/A" }}</td>
                <td>{{ $item->remarks }}</td>
                <td>{{ $item->ip }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" style="text-align: center;">
                    No data found
                </td>
            </tr>
        @endforelse
            
        </tbody>
    </table>
    
</section>
<script>

</script>  
@endsection 