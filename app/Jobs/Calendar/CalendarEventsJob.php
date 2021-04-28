<?php

namespace App\Jobs\Calendar;

use App\User;
use Illuminate\Bus\Queueable;
use App\Models\Calendar\Calendar;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\GlobalNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CalendarEventsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

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
                $subject = 'Calendar Event Notification';
                $message = 'Calendar Event<br>'.$calendar_event -> event_title.'</strong>';
                $message_email = '
                <div style="font-size: 15px;">
                Calendar Event
                    <br><br>
                    '.date_mdy(date('Y-m-d')).' - '.date('g:ia').'
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

    }
}
