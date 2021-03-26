<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GlobalNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($notification) {

        $this -> notification = $notification;
        $this -> notify_by = ['database'];

        if($notification['notify_by_email'] == 'yes') {
            $this -> notify_by[] = 'mail';
        }
        if($notification['notify_by_text'] == 'yes') {
            $this -> notify_by[] = 'nexmo';
        }

        if($notification['type'] == 'commission') {

            $this -> link_url = '/agents/doc_management/transactions/transaction_details/'.$notification['transaction_id'].'/'.$notification['transaction_type'].'?tab=commission';
            $this -> link_text = 'View Commission';

        } else if($notification['type'] == 'release') {

            $this -> link_url = '/doc_management/document_review/'.$notification['transaction_id'];
            $this -> link_text = 'View Release';

        } else if($notification['type'] == 'earnest' || $notification['type'] == 'using_heritage_title') {

            $this -> link_url = '/agents/doc_management/transactions/transaction_details/'.$notification['transaction_id'].'/'.$notification['transaction_type'].'';
            $this -> link_text = 'View Contract';

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
        return (new MailMessage)
            -> subject($this -> notification['subject'])
            -> line($this -> notification['message_email'])
            -> action($this -> link_text, url($this -> link_url));
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
            'transaction_type' => $this -> notification['transaction_type'],
            'transaction_id' => $this -> notification['transaction_id'],
            'message' => $this -> notification['message'],
            'link_url' => $this -> link_url,
            'link_text' => $this -> link_text
        ];
    }
}
