<?php

namespace App\Jobs\Calendar;

use App\User;
use App\Models\Tasks\Tasks;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\GlobalNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class TasksRemindersJob implements ShouldQueue
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
        $tasks = Tasks::where('status', 'active')
            -> where('task_date', date('Y-m-d'))
            -> where('task_time', date('H:i:00'))
            -> where('reminder', 1)
            -> with(['members', 'listing:Listing_ID,FullStreetAddress,City,StateOrProvince,PostalCode', 'contract:Contract_ID,FullStreetAddress,City,StateOrProvince,PostalCode'])
            -> get();

        foreach ($tasks as $task) {

            $listing = $task -> listing;
            $contract = $task -> contract;

            $notification = config('notifications.user_task_notification');

            if($notification['on_off'] == 'on') {

                $task_members = $task -> members;

                foreach($task_members as $task_member) {

                    $user = User::where('id', $task_member -> user_id) -> first();

                    if($user) {

                        $id = $task -> Listing_ID;
                        $sub_type = 'listing';
                        $property = $listing;
                        if($task -> transaction_type == 'contract') {
                            $id = $task -> Contract_ID;
                            $sub_type = 'contract';
                            $property = $contract;
                        }

                        $address = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
                        $address_email = $property -> FullStreetAddress.'<br>'.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;

                        $subject = 'Reminder Notification - '.$address;
                        $message = 'Reminder Notification<br>'.$address.'<br><strong>'.$task -> task_title.'</strong>';
                        $message_email = '
                        <div style="font-size: 15px;">
                            Reminder for:
                                <br><br>
                            '.$address_email.'
                            <br><br>
                            <strong>'.$task -> task_title.'</strong>
                            <br><br>
                            <a href="'.config('app.url').'/agents/doc_management/transactions/transaction_details/'.$id.'/'.$sub_type.'" target="_blank">View Transaction</a>
                            <br><br>
                            Thank You,<br>
                            Taylor Properties
                        </div>';


                        $notification['type'] = 'task_due';
                        $notification['sub_type'] = $sub_type;
                        $notification['sub_type_id'] = $id;
                        $notification['subject'] = $subject;
                        $notification['message'] = $message;
                        $notification['message_email'] = $message_email;

                        Notification::send($user, new GlobalNotification($notification));

                    }

                }

            }

        }

        return response() -> json(['status' => 'success']);
    }
}
