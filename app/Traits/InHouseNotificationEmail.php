<?php

namespace App\Traits;

use App\Mail\DefaultEmail;
use Illuminate\Support\Facades\Mail;


trait InHouseNotificationEmail {

    public function in_house_notification_email($to_addresses, $from_address, $from_name, $subject, $message) {

        /* USAGE
        $to_addresses = [
            ['name' => '', 'email' => config('in_house_notification_routing.notification_email_title')]
        ];
        $from_address = 'DoNotReply@TaylorProps.com';
        $from_name = 'Taylor Properties';
        $subject = 'subject here';
        $message = 'message here';

        $notify_title = $this -> InHouseNotificationEmail($to_addresses, $from_address, $from_name, $subject, $message);
            */

        $to_addresses = $to_addresses;
        $from_address = $from_address;
        $from_name = $from_name;
        $subject = $subject;
        $message = $message;

        $email = [];

        foreach($to_addresses as $to_address) {
            $email['tos'][] = ['name' => $to_address['name'], 'email' => $to_address['email']];
        }
        $email['ccs'] = [];
        $email['bccs'] = [];

        $email['from'] = ['name' => $from_name, 'email' => $from_address];
        $email['subject'] = $subject;
        $email['message'] = $message;

        $email['attachments'] = [];

        $email_notification = new DefaultEmail($email);

        Mail::to($email['tos'])
            -> cc($email['ccs'])
            -> bcc($email['bccs'])
            -> queue($email_notification);
    }

}
