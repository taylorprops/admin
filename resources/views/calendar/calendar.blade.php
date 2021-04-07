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

<div id="edit_event_div" class="hidden shadow bg-white border rounded p-3">

    <div class="d-flex justify-content-between align-items-center">
        <div class="text-primary font-12">Event Details</div>
        <a href="javascript: void(0)" id="cancel_new_event_button"><i class="fa fa-times text-danger fa-2x"></i></a>
    </div>

    <form id="edit_event_form">

        <div class="">
            <textarea class="custom-form-element form-textarea" name="event_title" id="event_title" data-label="Event Description"></textarea>
        </div>

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <input type="date" class="custom-form-element form-input date-field" name="start_date" id="start_date" data-label="Start Date">
            </div>

            <div class="end-date mx-1">
                -
            </div>

            <div class="end-date">
                <input type="date" class="custom-form-element form-input date-field" name="end_date" id="end_date" data-label="End Date">
            </div>

            <div class="times ml-2">

                <div class="d-flex justify-content-around align-items-center">

                    <div class="wpx-100">

                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel" id="start_time" name="start_time" data-label="">
                            <option value="00:00:00">12:00am</option>
                            <option value="00:15:00">12:15am</option>
                            <option value="00:30:00">12:30am</option>
                            <option value="00:45:00">12:45am</option>
                            @php
                            for($h=1; $h<24; $h++) {
                                $ampm = 'am';
                                $h_value = $h;
                                if($h < 10) {
                                    $h_value = '0'.$h;
                                }
                                $h_display = $h;
                                if($h > 12) {
                                    $h_display -= 12;
                                }
                                if($h > 11) {
                                    $ampm = 'pm';
                                }
                                for($m=0; $m<46; $m+=15) {
                                    if($m == 0) {
                                        $m = '00';
                                    }
                                    echo '<option value="'.$h_value.':'.$m.':00">'.$h_display.':'.$m.$ampm.'</option>';
                                }
                            }
                            @endphp
                        </select>

                    </div>

                    <div class="mx-1">-</div>

                    <div class="wpx-100">

                        <select class="custom-form-element form-select form-select-no-search form-select-no-cancel" id="end_time" name="end_time" data-label="">
                            <option value="00:00:00">12:00am</option>
                            <option value="00:15:00">12:15am</option>
                            <option value="00:30:00">12:30am</option>
                            <option value="00:45:00">12:45am</option>
                            @php
                            for($h=1; $h<24; $h++) {
                                $ampm = 'am';
                                $h_value = $h;
                                if($h < 10) {
                                    $h_value = '0'.$h;
                                }
                                $h_display = $h;
                                if($h > 12) {
                                    $h_display -= 12;
                                }
                                if($h > 11) {
                                    $ampm = 'pm';
                                }
                                for($m=0; $m<46; $m+=15) {
                                    if($m == 0) {
                                        $m = '00';
                                    }
                                    echo '<option value="'.$h_value.':'.$m.':00">'.$h_display.':'.$m.$ampm.'</option>';
                                }
                            }
                            @endphp
                        </select>

                    </div>

                </div>

            </div>

        </div>

        <div class="hide-multiple">

            <div class="text-gray">
                <input type="checkbox" class="custom-form-element form-checkbox" id="all_day" data-label="All Day" checked>
            </div>

            <div class="d-flex justify-content-start align-items-center">
                <div class="w-50">
                    <select class="custom-form-element form-select form-select-no-search form-select-no-cancel" id="repeat_frequency" name="repeat_frequency" data-label="Repeats">
                        <option value="none">Does Not Repeat</option>
                        <option value="daily" data-text="Day(s)">Daily</option>
                        <option value="weekly" data-text="Week(s)">Weekly</option>
                        <option value="monthly" data-text="Month(s)">Monthly</option>
                        <option value="yearly" data-text="Year(s)">Yearly</option>
                    </select>
                </div>

                <div class="mx-2 repeat">
                    every
                </div>

                <div class="wpx-80 repeat">
                    <input type="text" class="custom-form-element form-input" id="repeat_interval" name="repeat_interval" data-label="Interval">
                </div>

                <div class="repeat ml-2" id="frequency_text">Weeks</div>

            </div>

            <div class="repeat w-50">
                <input type="date" class="custom-form-element form-input date-field" name="repeat_until" id="repeat_until" data-label="Repeats Until">
            </div>

        </div>

        <hr>

        <div class="d-flex justify-content-center my-2">
            <a href="javascript: void(0)" class="btn btn-primary" id="save_event_button"><i class="fal fa-check mr-2"></i> Save Event</a>
        </div>

        <div class="d-flex justify-content-around mt-4 mb-2"><a href="javascript: void(0)" class="text-danger" id="delete_event_button" data-event-id=""><i class="fal fa-trash mr-2"></i> Delete Event</a></div>

        <input type="hidden" name="event_id" id="event_id">

    </form>

</div>

@endsection
