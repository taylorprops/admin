<?php

namespace App\Mail\DocManagement\Emails;

use Config;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class Documents extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $from;
    public $email_attachments;

    public function __construct($email)
    {
        $this->email = $email;
        $this->from = $email['from'];
        $this->subject = $email['subject'];
        $this->email_attachments = $email['attachments'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailer = $this->from($this->from['address'], $this->from['name'])
            ->markdown('emails.doc_management.email_documents');

        if ($this->email_attachments) {
            foreach ($this->email_attachments as $attachment) {
                $mailer->attachFromStorageDisk('public', $attachment['location'], $attachment['name']);
            }
        }

        return $mailer;
    }
}
