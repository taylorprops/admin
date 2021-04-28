<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Tasks\Tasks;
use Illuminate\Http\Request;
use App\Models\Calendar\Calendar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Notifications\GlobalNotification;
use Illuminate\Support\Facades\Notification;


class TestController extends Controller
{
    public function test(Request $request) {



        try {


            $calendar_events = Calendar::where('start_date', date('Y-m-d'))
            -> where('start_time', date('H:i:00'))
            -> where('all_day', 0)
            -> get();

            foreach ($calendar_events as $calendar_event) {

                $notification = config('notifications.user_calendar_event_notification');

                if($notification['on_off'] == 'on') {

                    $user = User::find($calendar_event -> user_id);

                    $id = '';
                    $sub_type = '';
                    $subject = 'Reminder Notification';
                    $message = 'Reminder<br>'.$calendar_event -> event_title.'</strong>';
                    $message_email = '
                    <div style="font-size: 15px;">
                        Reminder
                        <br><br>
                        '.date_mdy(date('Y-m-d')).' - All Day Event
                        <br><br>
                        <strong>'.$calendar_event -> event_title.'</strong>
                        <br><br>
                        Thank You,<br>
                        Taylor Properties
                    </div>';


                    $notification['type'] = 'calendar_event';
                    $notification['sub_type'] = $sub_type;
                    $notification['sub_type_id'] = $id;
                    $notification['subject'] = $subject;
                    $notification['message'] = $message;
                    $notification['message_email'] = $message_email;

                    Notification::send($user, new GlobalNotification($notification));

                }

            }

            return response() -> json(['status' => 'success']);



            // $events = Calendar::where('start_date', date('Y-m-d'))
            // -> where('all_day', 1)
            // -> get();



        } catch (Throwable $exception) {


        }

    }
}
