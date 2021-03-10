<?php

namespace App\Http\Controllers\Email;

use App\Mail\DefaultEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmailController extends Controller {

    public function send_email(Request $request) {

        /**** Usage
         *
         * JavaScript
         *
         * SIMPLE
         *
         *  let from = $('#email_from').val();
            let to_addresses = [{"type":"to","address":"Mike Taylor <miketaylor0101@gmail.com>"}];
            addresses = JSON.stringify(to_addresses);
            let subject = $('#email_subject').val();
            let message = $('#email_message').val();
            let attachments = [];

            ADVANCED
         *
         *  let from = $('#email_from').val();
            let to_addresses = [];
            $('.to-addresses').each(function() {
                if($(this).find('.email-to-address').val() != '') {
                    to_addresses.push({
                        type: $(this).find('.email-address-type').val(),
                        address: $(this).find('.email-to-address').val()
                    });
                }
            });
            to_addresses = JSON.stringify(to_addresses);
            let subject = $('#email_subject').val();
            let message = $('#email_message').val();
            let attachments = [];
            $('#email_attachments').find('.attachment-row').each(function() {
                attachments.push({
                    filename: $(this).data('file-name'),
                    file_location: $(this).data('file-location')
                });
            });
            attachments = JSON.stringify(attachments);

         * Request
        from: Mike Taylor - Taylor Properties <mike@taylorprops.com>
        to_addresses: [{"type":"to","address":"Mike Taylor <miketaylor0101@gmail.com>"},{"type":"cc","address":"teset@wsdf.com"}]
        subject: 'subject'
        message: '<div>html</div>'
        attachments: [{"filename":"Rental_Acknowledgment_Of_Admin_Fee.pdf","file_location":"doc_management/transactions/listings/27/294_user/converted/RentalAcknowledgmentOfAdminFee_20210304152744.pdf"},{"filename":"Sales_Contract.pdf","file_location":"doc_management/transactions/listings/27/294_user/converted/Sales_Contract.pdf"}]
        */


        $email = [];
        $from_address = $request -> from;
        $from_name = '';

        if (preg_match('/\<.*\>/', $from_address)) {
            preg_match('/(.*)[\s]*\<(.*)\>/', $from_address, $match);
            $from_name = $match[1];
            $from_address = $match[2];
        }

        $email['from'] = ['email' => $from_address, 'name' => $from_name];

        $email['subject'] = $request -> subject;
        $email['message'] = $request -> message;

        $email['tos_array'] = [];

        foreach (json_decode($request -> to_addresses) as $to_address) {
            $address = $to_address -> address;
            // if separated by , or ;
            if (preg_match('/[,;]+/', $address, $separator)) {
                $addresses = explode($separator[0], $address);

                foreach ($addresses as $address) {
                    $to = [];
                    $to['type'] = $to_address -> type;
                    $to['address'] = trim($address);
                    $email['tos_array'][] = $to;
                }
            } else {
                $to = [];
                $to['type'] = $to_address -> type;
                $to['address'] = $to_address -> address;
                $email['tos_array'][] = $to;
            }
        }

        $email['attachments'] = [];
        $attachment_size = 0;

        if ($request -> attachments) {
            foreach (json_decode($request -> attachments) as $attachment) {
                $file = [];
                $file['name'] = $attachment -> filename;
                $file['location'] = $attachment -> file_location;
                $email['attachments'][] = $file;
                $attachment_size += filesize(Storage::disk('public') -> path($attachment -> file_location));
            }

            $attachment_size = get_mb($attachment_size);
            if ($attachment_size > 20) {
                $fail = json_encode(['fail' => true, 'attachment_size' => $attachment_size]);

                return $fail;
            }
        }

        $email['tos'] = [];
        $email['ccs'] = [];
        $email['bccs'] = [];

        foreach ($email['tos_array'] as $to) {
            $to_address = $to['address'];
            $to_name = '';

            if (preg_match('/\<.*\>/', $to['address'])) {
                preg_match('/(.*)[\s]*\<(.*)\>/', $to['address'], $match);
                $to_name = $match[1];
                $to_address = $match[2];
            }

            if ($to['type'] == 'to') {
                $email['tos'][] = ['name' => $to_name, 'email' => $to_address];
            } elseif ($to['type'] == 'cc') {
                $email['ccs'][] = ['name' => $to_name, 'email' => $to_address];
            } elseif ($to['type'] == 'bcc') {
                $email['bccs'][] = ['name' => $to_name, 'email' => $to_address];
            }
        }

        $new_mail = new DefaultEmail($email);

        //return ($new_mail) -> render();

        Mail::to($email['tos'])
            -> cc($email['ccs'])
            -> bcc($email['bccs'])
            -> queue($new_mail);
    }
}
