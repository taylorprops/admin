<!doctype html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Document Management')</title>

        @include('/layouts/includes/header_scripts')

        @yield('js_scripts')

        @if(!auth() -> user())
        <script type="text/javascript">
        window.location.href = '/login';
        </script>
        @endif


    </head>

    <body class="animate__animated animate__faster animate__fadeIn @if(Request::is('*/edit_files/*')) y-scroll-none @endif @if(Request::is('*/document_review')) overflow-y-hidden @endif">

        <div class="loading-bg">
            <div class="loading-spinner">
                <div class="spinner-grow text-success" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-danger" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-warning" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="spinner-grow text-info" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <div class="loading-spinner-html h4 text-white mt-0 mx-2 mx-sm-auto">Loading...</div>
        </div>

        @include('layouts.includes.header')

        {{-- <main>
        @yield('content')
        </main> --}}

        @include('layouts.includes.common_includes.modals.modals')

        @yield('js')

        <input type="hidden" id="global_active_states" value="{{ implode(',', config('global.active_states')) }}">
    </body>

</html>
