<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Notifications\Notification;

use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Notifications\Messages\MailMessage;


class GlobalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $notification;
    public $tries = 1;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification) {


        $this -> notification = $notification;
        $this -> notify_by = ['database'];

        $this -> notification['link_url'] = '';
        $this -> notification['link_text'] = '';
        if(isset($notification['show_link']) && $notification['show_link'] == 'no') {
            $this -> notification['show_link'] = 'no';
        } else {
            $this -> notification['show_link'] = 'yes';
        }



        if($notification['notify_by_email'] == 'yes') {
            $this -> notify_by[] = 'mail';
        }
        if($notification['notify_by_text'] == 'yes') {
            $this -> notify_by[] = 'nexmo';
        }


        // %%%%%% Commission %%%%%% //
        if($notification['type'] == 'commission') {

            $this -> notification['link_url'] = '/agents/doc_management/transactions/transaction_details/'.$notification['sub_type_id'].'/'.$notification['sub_type'].'?tab=commission';
            $this -> notification['link_text'] = 'View Commission';

        // %%%%%% Release %%%%%% //
        } else if($notification['type'] == 'release') {

            $this -> notification['link_url'] = '/doc_management/document_review/'.$notification['sub_type_id'];
            $this -> notification['link_text'] = 'View Release';

        // %%%%%% Earnest %%%%%% //
        } else if($notification['type'] == 'earnest' || $notification['type'] == 'using_heritage_title') {

            $this -> notification['link_url'] = '/agents/doc_management/transactions/transaction_details/'.$notification['sub_type_id'].'/'.$notification['sub_type'];
            $this -> notification['link_text'] = 'View Contract';

        }

        // %%%%%% Agents %%%%%% //
        // Commission Ready
        else if($notification['type'] == 'commission_ready') {

            $this -> notification['link_url'] = '/agents/doc_management/transactions/transaction_details/'.$notification['sub_type_id'].'/'.$notification['sub_type'].'?tab=commission';
            $this -> notification['link_text'] = 'View Commission';

        // Bounced Earnest
        } else if($notification['type'] == 'bounced_earnest') {

            $this -> notification['link_url'] = '/agents/doc_management/transactions/transaction_details/'.$notification['sub_type_id'].'/'.$notification['sub_type'];
            $this -> notification['link_text'] = 'View Contract';

        // %%%%%% Tasks, Reminders, Calendar Events %%%%%% //
        } else if($notification['type'] == 'task_due') {

            $this -> notification['link_url'] = '/agents/doc_management/transactions/transaction_details/'.$notification['sub_type_id'].'/'.$notification['sub_type'];
            $this -> notification['link_text'] = 'View Transaction';


        // %%%%%% System Admin %%%%%% //
        } else if($notification['type'] == 'admin') {

            if($notification['sub_type'] == 'failed_job') {

                $this -> notification['link_url'] = '';
                $this -> notification['link_text'] = 'Failed Queued Job!!';

            } else if($notification['sub_type'] == 'bug_report') {

                $this -> notification['link_url'] = '/bug_reports/view_bug_report/'.$notification['sub_type_id'];
                $this -> notification['link_text'] = 'View Bug Report';

            }

        }

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this -> notify_by;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if($this -> notification['show_link'] == 'yes') {
            return (new MailMessage)
                -> subject($this -> notification['subject'])
                -> line(new HtmlString($this -> notification['message_email']))
                -> action($this -> notification['link_text'], url($this -> notification['link_url']));
        }

        return (new MailMessage)
            -> subject($this -> notification['subject'])
            -> line(new HtmlString($this -> notification['message_email']));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => $this -> notification['type'],
            'transaction_type' => $this -> notification['sub_type'],
            'transaction_id' => $this -> notification['sub_type_id'],
            'subject' => $this -> notification['subject'],
            'message' => $this -> notification['message'],
            'link_url' => $this -> notification['link_url'],
            'link_text' => $this -> notification['link_text']
        ];
    }
}
