<!doctype html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'title here')</title>

        <link href="https://fonts.googleapis.com/css?family=Baskervville|Karma|Lato|Maitree|Roboto&display=swap" rel="stylesheet">

        <link href="/css/app.css" rel="stylesheet">
        <link href="/vendor/fontawesome/fontawesome/css/all.css" rel="stylesheet">
        {{-- toaster --}}
        <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
        {{-- datatables --}}
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/af-2.3.5/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/cr-1.5.2/fh-3.1.7/kt-2.5.3/r-2.2.6/sc-2.0.3/sp-1.2.1/datatables.min.css"/>
        {{-- slider input --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css" crossorigin="anonymous" />


        <script src="/js/app.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        {{-- make jquery ui slide null since we are using bootstrap-slider --}}
        <script>$.fn.slider = null</script>
        {{-- mdbootsrap --}}
        {{-- <script type="text/javascript" src="/resources/mdbootstrap/js/popper.min.js"></script>
        <script type="text/javascript" src="/resources/mdbootstrap/js/mdb.min.js"></script> --}}
        {{-- <script type="text/javascript" src="/resources/mdbootstrap/js/addons/datatables.min.js"></script> --}}
        {{-- mdbootsrap stepper --}}
        {{-- <script type="text/javascript" src="/resources/mdbootstrap/js/addons-pro/steppers.min.js"></script> --}}

        {{-- toastr --}}
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        {{-- datatables --}}
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/af-2.3.5/b-colvis-1.6.5/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/cr-1.5.2/fh-3.1.7/kt-2.5.3/r-2.2.6/sc-2.0.3/sp-1.2.1/datatables.min.js"></script>
        {{-- slider input --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" crossorigin="anonymous"></script>
        {{-- text editor --}}
        <script src="https://cdn.tiny.cloud/1/t3u7alod16y8nsqt07h4m5kwfw8ob9sxbvy2rlmrqo94zrui/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
        {{-- google address search --}}
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('global.vars.google_api_key') }}&libraries=places&outputFormat=json"></script>


    </head>

    <body class="@if(Request::is('*/edit_files/*')) y-scroll-none @endif @if(Request::is('*/document_review')) overflow-y-hidden @endif">

        @include('layouts.includes.header')


        <main>
        @yield('content')
        </main>

        @include('layouts.includes.common_includes.modals.modals')

        @yield('js')
    </body>

</html>
