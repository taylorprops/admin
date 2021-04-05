<link href="https://fonts.googleapis.com/css?family=Baskervville|Karma|Lato|Maitree|Roboto&display=swap" rel="stylesheet">

<link href="/css/app.css" rel="stylesheet">

<link href="/vendor/fontawesome/fontawesome/css/all.css" rel="stylesheet">

{{-- toaster --}}
<link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

{{-- datatables --}}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/af-2.3.5/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/cr-1.5.2/fh-3.1.7/kt-2.5.3/r-2.2.6/sc-2.0.3/sp-1.2.1/datatables.min.css"/>

{{-- slider input --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/css/bootstrap-slider.min.css" crossorigin="anonymous" />


{{-- Calendar --}}
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.css' rel='stylesheet' />



<script src="{{ mix('/js/app.js') }}"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>

{{-- make jquery ui slide null since we are using bootstrap-slider --}}
<script>$.fn.slider = null</script>

{{-- popper --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

{{-- toastr --}}
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>


<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

{{-- datatables --}}
<script type="text/javascript" src="//cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.22/af-2.3.5/b-colvis-1.6.5/b-1.6.5/b-flash-1.6.5/b-html5-1.6.5/cr-1.5.2/fh-3.1.7/kt-2.5.3/r-2.2.6/sc-2.0.3/sp-1.2.1/datatables.min.js"></script>

{{-- slider input --}}
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/11.0.2/bootstrap-slider.min.js" crossorigin="anonymous"></script>

{{-- text editor --}}
<script src="//cdn.tiny.cloud/1/t3u7alod16y8nsqt07h4m5kwfw8ob9sxbvy2rlmrqo94zrui/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

{{-- calendar --}}
<script src='https://cdn.jsdelivr.net/npm/rrule@2.6.4/dist/es5/rrule.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.6.0/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/rrule@5.5.0/main.global.min.js'></script>

{{-- set user details as javascript variale --}}
@if(auth() -> user())
<script>
    let global_user = {!! collect(['name' => auth() -> user() -> name, 'group' => auth() -> user() -> group]) -> toJson() !!};
</script>
@endif
