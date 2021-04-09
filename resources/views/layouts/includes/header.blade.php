<div class="page-wrapper theme chiller-theme toggled">

    {{-- <div class="d-flex justify-content-between align-items-center show-sidebar w-100">

        <a id="show_sidebar" class="btn btn-primary" href="javascript:void(0)">
            <i class="fal fa-bars fa-lg"></i>
        </a>
        <div class="top-search mr-1 mr-sm-3">
            <div class="d-flex justify-content-start align-items-center">
                <div class="input-group">
                    <input type="text" class="form-control search-menu main-search-input top" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>
                <a href="#" class="hide-search hidden"><i class="fal fa-times text-danger ml-3 fa-2x"></i></a>
            </div>
        </div>

    </div> --}}

    <div class="show-sidebar">
        <a id="show_sidebar" class="btn btn-primary" href="javascript:void(0)">
            <i class="fal fa-bars fa-lg"></i>
        </a>
    </div>

    <nav id="sidebar" class="sidebar-wrapper ">
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
                        <input type="text" class="form-control search-menu main-search-input sidebar" placeholder="Search...">
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
                </div>

            </div>

            <div class="user-auth d-flex justify-content-around align-items-center font-8">
                <div>
                    <i class="fa fa-user mr-2 text-white"></i> <a href="/users/user_profile" class="text-light">Edit Profile</a>
                </div>
                <div>
                    <a class="text-light" href="/logout">Logout <i class="fal fa-sign-out ml-2 text-orange"></i></a>
                </div>
            </div>

            <hr>

            <!-- sidebar-menu  -->
        </div>
        <!-- sidebar-content  -->
        <div class="sidebar-footer">

            <a href="/logout">
                <i class="fal fa-sign-out fa-lg text-orange"></i>
            </a>

            <a href="/calendar">
                <i class="fad fa-calendar-alt fa-lg text-white"></i>
            </a>

            <a id="notifications_control" data-toggle="collapse" href="#notifications_collapse" role="button" aria-expanded="false" aria-controls="notifications_collapse">
                <i class="fa fa-bell fa-lg"></i>
                <span class="badge badge-pill bg-orange text-white notification notifications-unread-count">0</span>
            </a>

            <div class="collapse bg-primary p-1 mb-3 rounded" id="notifications_collapse">

                <div class="d-flex justify-content-between align-items-center bg-primary text-white px-3 py-2 font-12">
                    <div>
                        <i class="fad fa-bell mr-2"></i> Notifications
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <div>
                            <span class="badge bg-orange text-white notifications-unread-count"></span>
                        </div>
                        <div class="ml-3">
                            <a data-toggle="collapse" href="#notifications_collapse" role="button" aria-expanded="false" aria-controls="notifications_collapse"><i class="fal fa-times text-danger mt-2"></i></a>
                        </div>
                    </div>
                </div>

                <div class="notifications-container">
                    <div class="global-notifications-div bg-white p-2 rounded"></div>
                </div>

            </div>

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
