<div class="page-wrapper theme chiller-theme toggled">

    <a id="show-sidebar" class="btn btn-primary-dark" href="javascript:void(0)">
        <i class="fal fa-bars fa-lg"></i>
    </a>
    <nav id="sidebar" class="sidebar-wrapper">
        <div class="sidebar-content">
            <div class="sidebar-brand">
                <a class="header-logo-link text-center mr-5" href="javascript: void(0)"><img src="{{ \Session::get('header_logo_src') }}" class="header-logo"></a>
                <div id="close-sidebar">
                    <i class="fal fa-bars"></i>
                </div>
            </div>

            <!-- sidebar-header  -->
            <div class="sidebar-search">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="input-group">
                        <input type="text" class="form-control search-menu main-search-input" placeholder="Search...">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>
                    <a href="#" class="hide-search hidden"><i class="fal fa-times text-danger ml-3 fa-2x"></i></a>
                </div>
            </div>
            <!-- sidebar-search  -->
            <div class="sidebar-menu">
                @if(auth() -> user())

                    @php
                    $group = auth() -> user() -> group;
                    if(auth() -> user() -> group == 'transaction_coordinator') {
                        $group = 'agent';
                    }
                    @endphp

                    @include('layouts.includes/menus/'.$group)

                @endif
            </div>

            <div class="sidebar-header">
                <div class="user-pic">
                    @if(auth() -> user() -> photo_location)
                        <img class="img-responsive img-rounded" src="{{ auth() -> user() -> photo_location }}" alt="User picture">
                    @else
                        <i class="fal fa-user fa-3x mt-2 text-white"></i>
                    @endif
                </div>
                <div class="user-info">
                    <span class="user-name font-10">{{ auth() -> user() -> name }}</span>
                    <span class="user-role font-8">{{ str_replace(' ', '/', ucwords(str_replace('_', ' ', auth() -> user() -> group))) }}</span>
                    <span class="user-status d-flex justify-content-between align-items-center font-8">
                        <div>
                            <i class="fad fa-circle font-7"></i>
                            <span>Online</span>
                        </div>
                        <div class="ml-3">
                            <a class="text-white" href="/logout">Logout <i class="fal fa-sign-out ml-2 text-orange"></i></a>
                        </div>
                    </span>
                </div>
            </div>

            <hr>

            <!-- sidebar-menu  -->
        </div>
        <!-- sidebar-content  -->
        <div class="sidebar-footer">
            <a href="#">
                <i class="fa fa-bell"></i>
                <span class="badge badge-pill badge-warning notification">3</span>
            </a>
            <a href="#">
                <i class="fa fa-envelope"></i>
                <span class="badge badge-pill badge-success notification">7</span>
            </a>
            <a href="#">
                <i class="fa fa-cog"></i>
                <span class="badge-sonar"></span>
            </a>
            <a href="/logout">
                <i class="fa fa-power-off"></i>
            </a>
        </div>
    </nav>
    <!-- sidebar-wrapper  -->

    <main class="page-content">
        @yield('content')
    </main>

</div>

<div class="main-search-results-div">

    <div class="main-search-results overflow-x-hidden shadow search-ele"></div>

</div>
