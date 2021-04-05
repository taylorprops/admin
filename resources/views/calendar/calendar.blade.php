@extends('layouts.main')
@section('title', 'Calendar')

@section('content')

<div class="container page-container page-calendar">

    <div class="row">

        <div class="col-12">

            <div class="relative">

                <div id="calendar_div" class="vh-100 w-100 p-5"></div>

            </div>

        </div>

    </div>

</div>

<div id="edit_event_div" class="hidden shadow bg-white border rounded p-3 hpx-300 wpx-300">
    <div class="w-100 h-100"></div>
</div>
@endsection
