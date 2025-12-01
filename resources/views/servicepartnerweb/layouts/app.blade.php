<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="{{url('assets')}}/css/bootstrap.min.css" rel="stylesheet">
        <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/uicons-bold-rounded/css/uicons-bold-rounded.css'>
        <link href="{{url('assets')}}/css/style.css" rel="stylesheet">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="{{url('assets')}}/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/30.0.0/classic/ckeditor.js"></script>
        <script type="text/javascript" src="{{url('assets')}}/js/custom.js"></script>
        
        <title>KGA - Service Partner | @yield('page')</title>
    </head>
    <body oncontextmenu="return false">
        {{-- <div id="app-preloader">
            <div class="preloader-spinner">
                <svg class="preloader-spinner-icon" viewBox="0 0 24 24">
                    <path d="M 22.49772,12.000001 A 10.49772,10.497721 0 0 1 12,22.497722 10.49772,10.497721 0 0 1 1.5022797,12.000001 10.49772,10.497721 0 0 1 12,1.5022797 10.49772,10.497721 0 0 1 22.49772,12.000001 Z" fill="none" stroke-linecap="round" />
                </svg>
            </div>
        </div> --}}
        
        <aside class="side__bar shadow-sm">
            <div class="admin__logo">
                <div class="logo">
                <img src="{{url('assets')}}/images/kga_logo.png">
                </div>
                <div class="admin__info">
                <h1>{{Auth::user()->company_name}}</h1>
                </div>
            </div>
            <nav class="main__nav">
                <ul>
                    <li class="@if(request()->is('servicepartnerweb/dashboard')) {{'active'}} @endif">
                        <a href="{{route('servicepartnerweb.dashboard')}}"><i class="fi fi-br-home"></i> <span>Home</span></a>
                    </li>
                    <li class="@if(request()->is('servicepartnerweb/pincode/*')) {{'active'}} @endif">
                        <a href="{{ route('servicepartnerweb.pincode.list') }}"><i class="fi fi-br-location-alt"></i><span>Pincodes</span></a>
                    </li>
                    <li>
                        <a href="#"><i class="fi fi-br-bell"></i> <span>Service Notifications</span></a>
                        <ul>
                            <li @if(request()->is('servicepartnerweb/notification/list-installation')) class="active" @endif>
                                <a href="{{ route('servicepartnerweb.notification.list-installation') }}"><i class="fi fi-br-tool-box"></i> <span>Installation</span></a>
                            </li>
                            <li @if(request()->is('servicepartnerweb/notification/list-repair') || request()->is('servicepartnerweb/repair-spare/*')) class="active" @endif>
                                <a href="{{ route('servicepartnerweb.notification.list-repair') }}"><i class="fi fi-br-hammer"></i> <span>Repair</span></a>
                            </li>
                            
                        </ul>
                    </li> 
                    <li>
                        <a href="#"><i class="fi fi-br-bell"></i> <span>Chimney Maintenance & Repair Notifications</span></a>
                        <ul>
                            
                            <li @if(request()->is('servicepartnerweb/maintenance/*')) class="active" @endif>
                                <a href="{{ route('servicepartnerweb.maintenance.list') }}"><i class="fi fi-br-hammer"></i> <span>List Request</span></a>
                            </li>
                            {{-- <li @if(request()->is('servicepartnerweb/maintenance/list/motor')) class="active" @endif>
                                <a href="{{ route('servicepartnerweb.maintenance.list') }}/motor"><i class="fi fi-br-hammer"></i> <span>Motor Services</span></a>
                            </li> --}}
                            
                        </ul>
                    </li>  
                 <!--   <li>
                        <a href="#"><i class="fi fi-br-bell"></i> <span>AMC Management</span></a>
                        <ul>
                            
                            <li @if(request()->is('servicepartnerweb/amc/add*')) class="active" @endif>
                                <a href="{{ route('servicepartnerweb.amc.add') }}"><i class="fi fi-br-hammer"></i> <span>Add Request</span></a>
                            </li>
                            <li @if(request()->is('servicepartnerweb/amc/peding-discount-request-list*')) class="active" @endif>
                                <a href="{{ route('servicepartnerweb.amc.peding-discount-request-list') }}"><i class="fi fi-br-hammer"></i> <span>Pending Discount Request</span></a>
                            </li>
                            <li @if(request()->is('servicepartnerweb/amc/subscription-amc-data*')) class="active" @endif>
                                <a href="{{ route('servicepartnerweb.amc.subscription-amc-data') }}"><i class="fi fi-br-hammer"></i> <span>Subscription</span></a>
                            </li>
                        </ul>
                    </li>  -->
                    <li>
                        <a href="#"><i class="fi fi-br-money-bill-wave-alt"></i> <span>Report</span></a>
                        <ul>
                            <li @if(request()->is('servicepartnerweb/report/ledger')) class="active" @endif>
                                <a href="{{ route('servicepartnerweb.report.ledger') }}"><i class="fi fi-br-money-bills"></i> <span>Ledger</span></a>
                            </li>                     
                        </ul>
                    </li>
                    
                </ul>
            </nav>
            <div class="nav__footer">
                <a class="dropdown-item" href=""
                 onclick="if (confirm('Are You Sure?')){ event.preventDefault();  document.getElementById('logout-form').submit(); }  else { return false; } "
                 >
                    <i class="fi fi-br-sign-out"></i> <span>{{ __('Logout') }}</span>
                </a>
                <form id="logout-form" action="{{ route('servicepartnerweb.logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </aside>
        <main class="admin">
            <header>
                <div class="row align-items-center">  
                    <div class="col-auto">
                        <a href="javascript:void(0); ?>" class="menu_toggle"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg></a>
                    </div>                  
                    <div class="col-auto ms-auto">
                        <div class="dropdown profileDropdown">
                            <button class="btn dropdown-toggle dropdown-toggle-arrow" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                {{Auth::user()->person_name}}
                                <small>Logged in as Service Partner</small>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                                <li>
                                    <a class="dropdown-item" href="{{ route('servicepartnerweb.myprofile') }}">My Profile</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('servicepartnerweb.changepassword') }}">Change Password</a>
                                </li> 
                                <li>
                                    <a id="logout" class="dropdown-item" href="" 
                                    onclick="if (confirm('Are You Sure?')){ event.preventDefault();  document.getElementById('logout-form').submit(); }  else { return false; } "
                                    >
                                        <i class="fi fi-br-sign-out"></i> <span>{{ __('Logout') }}</span>
                                    </a>                    
                                    <form id="logout-form" action="{{ route('servicepartnerweb.logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            <section class="admin__title">
                <h1>@yield('page')</h1>
                <small>@yield('small')</small>
            </section>
            @yield('content')
            <footer>
                <div class="row">
                    <div class="col-12 text-end">KGA 2021-{{date('Y')}}</div>
                </div>
            </footer>
        </main>
        
    </body> 
    <script>
        // Restrict Right Click
        document.onkeydown = function(e) {
            if(e.keyCode == 123) {
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){
                return false;
            }
            if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){
                return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)){
                return false;
            }
        }
    </script>   
</html>