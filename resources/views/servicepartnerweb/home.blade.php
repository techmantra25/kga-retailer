@extends('servicepartnerweb.layouts.app')
@section('content')
@section('page', 'Dashboard')
    <section>        
        <div class="row">            
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('servicepartnerweb.notification.list-installation',['closing_type'=>'pending'])}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Pending Installation <i class="fi fi-br-calendar-minus"></i></h4>
                        <h2> {{$count_pending_installation}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('servicepartnerweb.notification.list-repair',['closing_type'=>'pending'])}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Pending Repair <i class="fi fi-br-calendar-minus"></i></h4>
                        <h2> {{$count_pending_repair}}</h2>
                    </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('servicepartnerweb.notification.list-customer-repair-point',['closing_type'=>'pending'])}}'" style="cursor: pointer">
                    <div class="card-body">
                        <h4>Pending Customer Repair Point <i class="fi fi-br-calendar-minus"></i></h4>
                        <h2> {{$count_pending_customer_repair_point}}</h2>
                    </div>
                </div>
            </div>
        </div>  
		
		<div class="row">            
            <div class="col-sm-3">
				 <div class="card home__card bg-gradient-danger" onclick="location.href='{{route('servicepartnerweb.maintenance.list',['closing_type'=>'pending'])}}'" style="cursor: pointer">
					 <div class="card-body">
						  <h4>Chimney Cleaning <i class="fi fi-br-calendar-minus"></i></h4>
						 <h2>{{$count_pending_chimney_cleaning}}</h2>
					 </div>
				</div>
			</div>
		</div>
    </section>  
@endsection     