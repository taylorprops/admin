<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsEmailed;

use Config;
use Illuminate\Support\Facades\Storage;

class CheckEmailedDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doc_management:check_emailed_documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets emailed documents from mail server and imports into transactions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {

            $this -> get_emailed_docs_from_server();
            sleep(15);
            $this -> get_emailed_docs_from_server();
            sleep(15);
            $this -> get_emailed_docs_from_server();
            sleep(15);
            $this -> get_emailed_docs_from_server();

    }

    public function get_emailed_docs_from_server() {

        $username = config('mail_server.mail_server.username');
        $password = config('mail_server.mail_server.password');
        $server = config('mail_server.mail_server.address');

        $mailbox = imap_open("{".$server.":143/novalidate-cert}INBOX", $username, $password);

        $num = imap_num_msg($mailbox);

        if($num > 0) {

            for($n = 1; $n <= $num; $n++) {

                $header = imap_headerinfo($mailbox, $n);

                $to_address = $header -> to[0] -> mailbox.'@'.$header -> to[0] -> host;
                $emailed_docs_folder = 'doc_management/transactions';

                $property = null;
                $Listing_ID = 0;
                $Contract_ID = 0;
                $Referral_ID = 0;
                // subjects will contain L,C or R - 123_SomeSt_878767C@agentdocuments.com
                if(preg_match('/[0-9]+L@/', $to_address)) {
                    $property = Listings::where('PropertyEmail', $to_address) -> first();
                    $Listing_ID = $property -> Listing_ID;
                    $emailed_docs_folder .= '/listings/'.$Listing_ID.'/emailed_docs';
                    $transaction_type = 'listing';
                } else if(preg_match('/[0-9]+C@/', $to_address)) {
                    $property = Contracts::where('PropertyEmail', $to_address) -> first();
                    $Contract_ID = $property -> Contract_ID;
                    $emailed_docs_folder .= '/contracts/'.$Contract_ID.'/emailed_docs';
                    $transaction_type = 'contract';
                } else if(preg_match('/[0-9]+R@/', $to_address)) {
                    $property = Referrals::where('PropertyEmail', $to_address) -> first();
                    $Referral_ID = $property -> Referral_ID;
                    $emailed_docs_folder .= '/referrals/'.$Referral_ID.'/emailed_docs';
                    $transaction_type = 'referral';
                }

                // if message subject matched to a property
                if($property) {

                    $structure = imap_fetchstructure($mailbox, $n);

                    $attachments = array();

                    // if any attachments found...
                    if(isset($structure -> parts) && count($structure -> parts)) {

                        for($i = 0; $i < count($structure -> parts); $i++) {

                            $attachments[$i] = array(
                                'is_attachment' => false,
                                'filename' => '',
                                'name' => '',
                                'attachment' => ''
                            );

                            if($structure -> parts[$i]-> ifdparameters) {
                                foreach($structure -> parts[$i]-> dparameters as $object) {
                                    if(strtolower($object -> attribute) == 'filename') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['filename'] = $object -> value;
                                    }
                                }
                            }

                            if($structure -> parts[$i]-> ifparameters) {
                                foreach($structure -> parts[$i]-> parameters as $object) {
                                    if(strtolower($object -> attribute) == 'name') {
                                        $attachments[$i]['is_attachment'] = true;
                                        $attachments[$i]['name'] = $object -> value;
                                    }
                                }
                            }

                            if($attachments[$i]['is_attachment']) {
                                $attachments[$i]['attachment'] = imap_fetchbody($mailbox, $n, $i+1);

                                // 3 = BASE64 encoding
                                if($structure -> parts[$i]-> encoding == 3) {
                                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                                }
                                // 4 = QUOTED-PRINTABLE encoding
                                elseif($structure -> parts[$i]-> encoding == 4) {
                                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                                }
                            }

                            if($attachments[$i]['attachment'] == '') {
                                unset($attachments[$i]);
                            }

                        }

                        if(count($attachments) > 0) {

                            // add emailed_docs folder
                            if(!Storage::disk('public') -> exists($emailed_docs_folder)){
                                Storage::disk('public') -> makeDirectory($emailed_docs_folder);
                            }

                            foreach($attachments as $attachment) {

                                if($attachment['is_attachment'] == 1) {

                                    $attachment_file = $attachment['attachment'];
                                    $attachment_name = $attachment['name'];
                                    $file_name_display = $attachment_name;

                                    $ext = pathinfo($attachment_name, PATHINFO_EXTENSION);
                                    $attachment_name_no_ext = str_replace('.' . $ext, '', $attachment_name);
                                    $attachment_name = sanitize($attachment_name_no_ext).'_'.date('YmdHis') . '.' . $ext;

                                    Storage::disk('public') -> put($emailed_docs_folder.'/'.$attachment_name, $attachment_file);

                                    // add to db
                                    $attach = new TransactionDocumentsEmailed();
                                    $attach -> Agent_ID = $property -> Agent_ID;
                                    $attach -> Listing_ID = $Listing_ID;
                                    $attach -> Contract_ID = $Contract_ID;
                                    $attach -> Referral_ID = $Referral_ID;
                                    $attach -> transaction_type = $transaction_type;
                                    $attach -> email_status = 'success';
                                    $attach -> file_name_display = $file_name_display;
                                    $attach -> file_location = '/storage/'.$emailed_docs_folder.'/'.$attachment_name;
                                    $attach -> save();

                                }

                            }

                            $imapresult = imap_mail_move($mailbox, $n, 'Processed');

                        } else {

                            // if no attachments
                            $imapresult = imap_mail_move($mailbox, $n, 'Failed');

                            // add to db
                            $attach = new TransactionDocumentsEmailed();
                            $attach -> Agent_ID = $property -> Agent_ID;
                            $attach -> Listing_ID = $Listing_ID;
                            $attach -> Contract_ID = $Contract_ID;
                            $attach -> Referral_ID = $Referral_ID;
                            $attach -> transaction_type = $transaction_type;
                            $attach -> email_status = 'fail';
                            $attach -> fail_reason = 'No Attachments';
                            $attach -> save();

                        }

                    }

                } else {

                    // if message to address not matched to a property
                    $imapresult = imap_mail_move($mailbox, $n, 'Failed');

                }

                imap_expunge($mailbox);

            }

        }

        return true;
    }
}
