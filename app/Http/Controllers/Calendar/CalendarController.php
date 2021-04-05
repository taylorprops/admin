<?php

namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Models\Calendar\Calendar;
use App\Http\Controllers\Controller;

class CalendarController extends Controller
{

    public function calendar(Request $request) {

        return view('/calendar/calendar');
    }

    public function calendar_events(Request $request) {

        $events = Calendar::where('user_id', auth() -> user() -> id) -> get();

        $calendar_events = [];
        foreach ($events as $event) {

            $id = $event -> id;
            $title = $event -> event_title;

            $start = $event -> start_date;
            if($event -> start_time) {
                $start .= 'T'.$event -> start_time;
            }

            $end = '';
            if($event -> end_time) {
                $end = $event -> end_date;
                if($event -> end_time) {
                    $end .= 'T'.$event -> end_time;
                }
            }

            $event_details = [
                'id' => $id,
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'groupId' => $event -> group_id
            ];

            if($event -> repeat_frequency != '') {
                $event_details = [
                    'id' => $id,
                    'title' => $title,
                    'start' => '',
                    'end' => '',
                    'rrule' =>  [
                        'freq' =>  $event -> repeat_frequency,
                        'interval' =>  $event -> repeat_interval,
                        'dtstart' =>  $start,
                        'until' =>  $event -> repeat_until
                    ]
                ];
            }

            $calendar_events[] = $event_details;

        }

        return $calendar_events;

    }

    public function calendar_update(Request $request) {

        $event_details = $request -> event_details;
        dd($event_details);

    }

}
