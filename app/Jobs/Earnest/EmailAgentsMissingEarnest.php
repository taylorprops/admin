<?php

namespace App\Jobs\Earnest;

use App\Mail\DefaultEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\DocManagement\Transactions\Contracts\Contracts;

class EmailAgentsMissingEarnest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contract_ids;
    protected $subject;
    protected $message;
    protected $from_address;
    protected $from_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contract_ids, $subject, $message, $from_address, $from_name)
    {
        $this -> contract_ids = $contract_ids;
        $this -> subject = $subject;
        $this -> message = $message;
        $this -> from_address = $from_address;
        $this -> from_name = $from_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        $contract_ids = $this -> contract_ids;
        $subject = $this -> subject;
        $message = $this -> message;
        $from_address = $this -> from_address;
        $from_name = $this -> from_name;

        $email = [];
        $email['from'] = ['address' => $from_address, 'name' => $from_name];

        $select = [
            'Agent_ID',
            'Contract_ID',
            'FullStreetAddress',
            'City',
            'StateOrProvince',
            'PostalCode'
        ];
        $contracts = Contracts::select($select)
            -> whereIn('Contract_ID', $contract_ids)
            -> with('agent:id,first_name,full_name,email')
            -> get();

        foreach($contracts as $contract) {

            $agent = $contract -> agent;
            $to_name = $agent -> full_name;
            $to_address = $agent -> email;
            $property_address = $contract -> FullStreetAddress.' '.$contract -> City.', '.$contract -> StateOrProvince.' '.$contract -> PostalCode;
            $subject = preg_replace('/%%PropertyAddress%%/', $property_address, $subject);
            $message = preg_replace('/%%PropertyAddress%%/', $property_address, $message);
            $message = preg_replace('/%%FirstName%%/', $agent -> first_name, $message);

            $email['subject'] = $subject;
            $email['message'] = $message;

            $to_name = $agent -> full_name;
            $to_address = $agent -> email;

            $new_mail = new DefaultEmail($email);

            // update last sent date

            Mail::to(['name' => $to_name, 'email' => $to_address])
                -> send($new_mail);

        }
    }
}
