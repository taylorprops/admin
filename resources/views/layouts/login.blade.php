<!doctype html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'title here')</title>

        <link href="/css/app.css" rel="stylesheet">
        <link href="/vendor/fontawesome/fontawesome/css/all.css" rel="stylesheet">

        <script src="/js/app.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        {{-- toastr --}}
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    </head>

    <body>

        <header>

        </header>



        <main>
        @yield('content')
        </main>

        @include('layouts.includes.common_includes.modals.modals')


        @yield('js')

    </body>

</html>
