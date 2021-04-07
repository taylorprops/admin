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

            $all_day = $event -> all_day == 1 ? true : false;

            $start = $event -> start_date;
            if($event -> start_time && $all_day == false) {
                $start .= 'T'.$event -> start_time;
            }

            $end = '';
            if($event -> end_date) {
                $end = $event -> end_date;
            }
            if($event -> end_time && $all_day == false) {
                $end .= 'T'.$event -> end_time;
            }

            $extendedProps = [];
            $end_actual = $end;
            if($event -> start_date != $event -> end_date) {
                $extendedProps['multiple'] = 'multiple';
                $end = date("Y-m-d", strtotime("$end +1 day"));
            }

            $extendedProps['end_actual'] = $end_actual;

            $event_details = [
                'id' => $id,
                'title' => $title,
                'allDay' => $all_day,
                'start' => $start,
                'end' => $end,
                'groupId' => $event -> group_id,
                'extendedProps' => $extendedProps
            ];

            if($event -> repeat_frequency != 'none') {
                $event_details = [
                    'id' => $id,
                    'title' => $title,
                    'start' => $start,
                    'end' => $end,
                    'extendedProps' => [
                        'freq' =>  $event -> repeat_frequency,
                        'interval' =>  $event -> repeat_interval,
                        'dtstart' =>  $start,
                        'until' =>  !stristr($event -> repeat_until, '0000') ? $event -> repeat_until : ''
                    ],
                    'rrule' =>  [
                        'freq' =>  $event -> repeat_frequency,
                        'interval' =>  $event -> repeat_interval,
                        'dtstart' =>  $start,
                        'until' =>  !stristr($event -> repeat_until, '0000') ? $event -> repeat_until : ''
                    ]
                ];
            }


            $calendar_events[] = $event_details;

        }

        return $calendar_events;

    }

    public function calendar_update(Request $request) {

        $event_id = $request -> event_id;
        $event_title = $request -> event_title;
        $all_day = $request -> all_day == 'true' ? 1 : 0;
        $start_date = $request -> start_date;
        $end_date = $request -> end_date;
        $start_time = $request -> start_time;
        $end_time = $request -> end_time;
        $repeat_frequency = $request -> repeat_frequency;
        $repeat_interval = $request -> repeat_interval;
        $repeat_until = $request -> repeat_until;


        if($event_id > 0) {
            $event = Calendar::where('id', $event_id) -> first() -> update([
                'user_id' => auth() -> user() -> id,
                'event_title' => $event_title,
                'all_day' => $all_day,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'repeat_frequency' => $repeat_frequency,
                'repeat_interval' => $repeat_interval,
                'repeat_until' => $repeat_until
            ]);
        } else {
            $event = new Calendar;
            $event -> user_id = auth() -> user() -> id;
            $event -> event_title = $event_title;
            $event -> all_day = $all_day;
            $event -> start_date = $start_date;
            $event -> end_date = $end_date;
            $event -> start_time = $start_time;
            $event -> end_time = $end_time;
            $event -> repeat_frequency = $repeat_frequency;
            $event -> repeat_interval = $repeat_interval;
            $event -> repeat_until = $repeat_until;
            $event -> save();

            $event_id = $event -> id;
        }


        return response() -> json([
            'status' => 'success',
            'event_id' => $event_id
            ]);


    }

    public function calendar_delete(Request $request) {

        $delete = Calendar::find($request -> event_id) -> delete();

        return response() -> json(['status' => 'success']);

    }

}
