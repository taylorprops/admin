<?php

namespace App\Http\Controllers\Calendar;

use App\Models\Tasks\Tasks;
use Illuminate\Http\Request;
use App\Models\Calendar\Calendar;
use App\Http\Controllers\Controller;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;

class CalendarController extends Controller
{

    public function calendar(Request $request) {

        return view('/calendar/calendar');
    }

    public function calendar_events(Request $request) {

        $calendar_select = ['id', 'group_id', 'start_date', 'start_time' ,'end_date', 'end_time', 'all_day', 'event_title', 'repeat_frequency', 'repeat_interval', 'repeat_until'];
        $calendar_events = Calendar::select($calendar_select) -> where('user_id', auth() -> user() -> id) -> get();

        foreach($calendar_events as $calendar_event) {
            $calendar_event -> event_type = 'calendar';
        }

        $tasks_select = [
            'id',
            'task_date as start_date',
            'task_time as start_time',
            'task_title as event_title',
            'reminder',
            'Listing_ID',
            'Contract_ID',
            'transaction_type'
        ];

        $tasks_events = Tasks::select($tasks_select)
                -> whereHas('members', function($query) {
                    $query -> where('member_email', auth() -> user() -> email);
            })
            -> get();


        if(count($tasks_events) > 0) {

            foreach($tasks_events as $tasks_event) {

                $start_time = $tasks_event -> start_time;
                if($tasks_event -> start_time == '' || $tasks_event -> start_time == '00:00:00') {
                    $start_time = '09:00:00';
                }

                $tasks_event -> event_type = 'tasks';
                $tasks_event -> group_id = null;
                $tasks_event -> all_day = $tasks_event -> reminder;
                $tasks_event -> start_time = $start_time;
                $tasks_event -> end_date = $tasks_event -> start_date;
                $tasks_event -> end_time = date("H:i:s", strtotime("$start_time +1 hour"));

                $tasks_event -> repeat_frequency = 'none';
                $tasks_event -> repeat_interval = null;
                $tasks_event -> repeat_until = null;

            }


            $events = $calendar_events -> merge($tasks_events);

        } else {

            $events = $calendar_events;

        }

        foreach ($events as $event) {

            $id = $event -> id;
            $group_id = $event -> group_id;
            $title = $event -> event_title;
            $all_day = $event -> all_day == 1 ? true : false;
            $start = $event -> start_date;
            $start_time = $event -> start_time;
            $end = $event -> end_date;
            $end_time = $event -> end_time;

            $repeat_frequency = $event -> repeat_frequency;
            $repeat_interval = $event -> repeat_interval;
            $repeat_until = $event -> repeat_until;

            $extendedProps = [];
            $extendedProps['end_actual'] = $end;
            $extendedProps['event_type'] = $event -> event_type;

            if($event -> transaction_type) {

                if($event -> transaction_type == 'listing') {
                    $property = Listings::find($event -> Listing_ID, ['Listing_ID', 'FullStreetAddress', 'City', 'StateOrProvince', 'PostalCode']);
                    $property_id = $property -> Listing_ID;
                } else {
                    $property = Contracts::find($event -> Contract_ID, ['Listing_ID', 'FullStreetAddress', 'City', 'StateOrProvince', 'PostalCode']);
                    $property_id = $property -> Contract_ID;
                }
                $extendedProps['property_link'] = '/agents/doc_management/transactions/transaction_details/'.$property_id.'/'.$event -> transaction_type;
                $extendedProps['property_address'] = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;

            }

            if($all_day == true) {

                if($start != $end) {
                    $extendedProps['multiple'] = 'multiple';
                    $end = date("Y-m-d", strtotime("{$end} +1 day"));
                }

            } else if($all_day == false) {

                $start .= 'T'.$start_time;
                $end .= 'T'.$end_time;

            }

            $event_details = [
                'id' => $id,
                'title' => $title,
                'allDay' => $all_day,
                'start' => $start,
                'end' => $end,
                'groupId' => $group_id,
                'extendedProps' => $extendedProps
            ];

            if($repeat_frequency != 'none') {

                $event_details['extendedProps']['freq'] = $repeat_frequency;
                $event_details['extendedProps']['interval'] = $repeat_interval;
                $event_details['extendedProps']['dtstart'] = $start;
                $event_details['extendedProps']['until'] = !stristr($repeat_until, '0000') ? $repeat_until : '';

                $event_details['rrule']['freq'] = $repeat_frequency;
                $event_details['rrule']['interval'] = $repeat_interval;
                $event_details['rrule']['dtstart'] = $start;
                $event_details['rrule']['until'] = !stristr($repeat_until, '0000') ? $repeat_until : '';

            }


            $calendar_events[] = $event_details;

        }

        return $calendar_events;

    }

    public function calendar_update(Request $request) {

        $event_id = $request -> event_id;
        $event_type = $request -> event_type;
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

            if($event_type == 'calendar') {

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

            } else if($event_type == 'tasks') {

                $event = Tasks::where('id', $event_id) -> first();
                if($event -> start_date != $start_date) {
                    $event -> task_option_days = 0;
                    $event -> task_option_position = '';
                    $event -> task_action = 0;
                }
                $event -> task_title = $event_title;
                $event -> task_date = $start_date;
                $event -> task_time = $start_time;
                $event -> save();

            }
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

        if($request -> event_type == 'calendar') {
            $delete = Calendar::find($request -> event_id) -> delete();
        } else if($request -> event_type == 'tasks') {
            $delete = Tasks::find($request -> event_id) -> delete();

            Tasks::where('task_action_task', $request -> event_id) -> update([
                'task_action' => null,
                'task_action_task' => null,
                'task_option_days' => 0
            ]);
        }

        return response() -> json(['status' => 'success']);

    }

}
