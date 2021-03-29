<!doctype html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'title here')</title>

        @include('/layouts/includes/header_scripts');

    </head>

    <body>

        <main>
        @yield('content')
        <input type="hidden" id="login_page">
        </main>

        @yield('js')

        @include('layouts.includes.common_includes.modals.modals')

    </body>

</html>
