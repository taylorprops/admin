<header>

    <nav class="navbar navbar-expand-xl fixed-top navbar-sticky navbar-dark bg-primary navbar-hover" id="main_nav_bar">

        <a class="header-logo-link text-center mr-5" href="javascript: void(0)"><img src="{{ \Session::get('header_logo_src') }}" class="header-logo"></a>

        <div class="d-flex justify-content-start mr-5">
            <input class="main-search-input top search-ele" type="text" placeholder="Search" aria-label="Search">
            <a href="javascript:void(0)" class="hide-search hidden"><i class="fal fa-times text-danger fa-2x ml-2 mt-1"></i></a>
        </div>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav_collapse" aria-controls="main_nav_collapse" aria-expanded="false" aria-label="Navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse search-ele" id="main_nav_collapse">

            <ul class="navbar-nav mr-5 mt-4 mt-xl-auto">

                @if(auth() -> user())

                @include('layouts.includes/menus/'.auth() -> user() -> group)

                @endif

            </ul>
            <div class="d-flex justify-content-start ml-5">
                <input class="main-search-input bottom search-ele" type="text" placeholder="Search" aria-label="Search">
                <a href="javascript:void(0)" class="hide-search hidden"><i class="fal fa-times text-danger fa-2x ml-2 mt-1"></i></a>
            </div>

            <hr class="d-block d-xl-none">

            <ul class="navbar-nav w-100">

                <li class="w-100">

                    <div class="d-flex justify-content-start justify-content-xl-end flex-wrap align-items-center">
                        <div class="mr-5">
                            <a class="nav-link text-white" href="javascript: void(0)"><i class="far fa-comments mr-2"></i> Support</a>
                        </div>
                        <div class="d-flex justify-content-around">
                            <a class="nav-link text-white py-0" href="javascript: void(0)"><i class="fas fa-user mr-2"></i>@if(auth() -> user()) {{ str_replace(' ', '/', ucwords(str_replace('_', ' ', auth() -> user() -> group))).' - '.auth() -> user() -> name }} @endif</a>
                            <a class="nav-link text-white py-0 float-right ml-3" href="/logout">Logout</a>
                        </div>
                    </div>

                </li>

            </ul>

        </div>

    </nav>

    <div class="main-search-results-container">

        <div class="main-search-results-div">

            <div class="main-search-results shadow search-ele"></div>

        </div>

    </div>

</header>
