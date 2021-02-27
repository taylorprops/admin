<!doctype html>
<html lang="{{ str_replace('_', '-', app() -> getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Document Management')</title>

        <link href="https://fonts.googleapis.com/css?family=Baskervville|Karma|Lato|Maitree|Roboto&display=swap" rel="stylesheet">

        <link href="/css/app.css" rel="stylesheet">
        <link href="/vendor/fontawesome/fontawesome/css/all.css" rel="stylesheet">
        {{-- toaster --}}
        <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
        {{-- datatables --}}
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/af-2.3.5/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/cr-1.5.2/fh-3.1.7/kt-2.5.3/r-2.2.6/sc-2.0.3/sp-1.2.1/datatables.min.css"/>
        {{-- slider input --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css" crossorigin="anonymous" />


        <script src="{{ mix('/js/app.js') }}"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        {{-- make jquery ui slide null since we are using bootstrap-slider --}}
        <script>$.fn.slider = null</script>
        {{-- popper --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        {{-- toastr --}}
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        {{-- datatables --}}
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
        <script type="text/javascript" src="//cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/af-2.3.5/b-colvis-1.6.5/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/cr-1.5.2/fh-3.1.7/kt-2.5.3/r-2.2.6/sc-2.0.3/sp-1.2.1/datatables.min.js"></script>
        {{-- slider input --}}
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" crossorigin="anonymous"></script>
        {{-- text editor --}}
        <script src="//cdn.tiny.cloud/1/t3u7alod16y8nsqt07h4m5kwfw8ob9sxbvy2rlmrqo94zrui/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

        @larabugJavaScriptClient
        {{-- xhr.open("POST", window.location.protocol+'//'+window.location.hostname + '/larabug-api/javascript-report', true); --}}


        @yield('js_scripts')


    </head>

    <body class="@if(Request::is('*/edit_files/*')) y-scroll-none @endif @if(Request::is('*/document_review')) overflow-y-hidden @endif">

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


        <main>
        @yield('content')
        </main>

        @include('layouts.includes.common_includes.modals.modals')

        @yield('js')
    </body>

</html>
