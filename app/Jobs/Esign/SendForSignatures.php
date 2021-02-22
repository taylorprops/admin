<?php

namespace App\Jobs\Esign;

use Illuminate\Support\Facades\App;

use Eversign\File;
use Eversign\Field;
use Eversign\Client;
use Eversign\Signer;
use Eversign\Document;
use Eversign\Recipient;
use Eversign\TextField;
use Eversign\InitialsField;
use Eversign\SignatureField;
use Eversign\DateSignedField;
use Illuminate\Bus\Queueable;
use App\Models\Esign\EsignSigners;
use App\Models\Esign\EsignDocuments;
use App\Models\Esign\EsignEnvelopes;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendForSignatures implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $envelope_id;
    protected $template_id;
    protected $document_ids;
    protected $subject;
    protected $message;
    protected $fields;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($envelope_id, $template_id, $document_ids, $subject, $message, $fields) {

        $this -> envelope_id = $envelope_id;
        $this -> template_id = $template_id;
        $this -> document_ids = $document_ids;
        $this -> subject = $subject;
        $this -> message = $message;
        $this -> fields = $fields;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        // update esign_envelope table with subject and message
        $envelope = EsignEnvelopes::find($envelope_id) -> update([
            'subject' => $subject,
            'message' => $message,
            'is_draft' => 'no',
            'draft_name' => ''
        ]);

        $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));

        $file_to_sign = new document();
        $file_to_sign -> setSandbox(true);
        $file_to_sign -> setTitle($subject);
        $file_to_sign -> setMessage($message);
        //$file_to_sign -> setEmbeddedSigningEnabled(true);
        //$file_to_sign -> setFlexibleSigning(false); // remove all fields to try this
        $file_to_sign -> setUseHiddenTags(true);
        $file_to_sign -> setRequireAllSigners(true);
        $file_to_sign -> setUseSignerOrder(true);
        $file_to_sign -> setCustomRequesterName(auth() -> user() -> name);
        $file_to_sign -> setCustomRequesterEmail(auth() -> user() -> email);
        if(App::environment() != 'local') {
            $site_address = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'].'/esign_callback';
            $file_to_sign -> setRedirect($site_address);
        }

        $days = config('global.vars.app_stage') == 'development' ? 'P1D' : 'P7D';
        $date = new \DateTime();
        $date -> add(new \DateInterval($days));
        $file_to_sign -> setExpires($date);

        // Add Signers
        $signers = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> orderBy('signer_order') -> get();

        foreach($signers as $signer) {

            $add_signer = new Signer();
            $add_signer -> setId($signer -> id);
            $add_signer -> setName($signer -> signer_name);
            $add_signer -> setEmail($signer -> signer_email);
            $add_signer -> setRole($signer -> signer_role);
            $add_signer -> setDeliverEmail(true);
            //$signer -> setLanguage('en');
            $file_to_sign -> appendSigner($add_signer);

        }

        // Add Recipients
        $recipients = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'yes') -> get();

        foreach($recipients as $recipient) {

            $add_recipient = new Recipient();
            $add_recipient -> setName($recipient -> signer_name);
            $add_recipient -> setEmail($recipient -> signer_email);
            //$add_recipient -> setLanguage('en');
            $file_to_sign -> appendRecipient($add_recipient);

        }


        $file_index = 0;

        foreach($document_ids as $document_id) {

            $document = EsignDocuments::where('id', $document_id) -> first();

            if(count($fields -> where('document_id', $document_id)) > 0) {

                //Add a File to the Document
                $file = new File();
                $file -> setName($document -> file_name);
                $file -> setFilePath(getcwd().$document -> file_location);
                $file_to_sign -> appendFile($file);

                $c = 0;

                $width = $document -> width;
                $height = $document -> height;

                foreach($fields -> where('document_id', $document_id) as $field) {

                    // increase move down and right
                    $x = ($field -> left_perc/100) * $width;
                    $y = ($field -> top_perc/100) * $height;
                    $w = ($field -> width_perc/100) * $width;
                    $h = ($field -> height_perc/100) * $height;

                    if($field -> field_type == 'signature') {

                        $document_field = new SignatureField();
                        $document_field -> setSigner($field -> signer_id);
                        $document_field -> setRequired($field -> required);
                        $document_field -> setY($y + 3);

                    } else if($field -> field_type == 'initials') {

                        $document_field = new InitialsField();
                        $document_field -> setSigner($field -> signer_id);
                        $document_field -> setRequired($field -> required);
                        $document_field -> setY($y);

                    } else if($field -> field_type == 'date') {

                        $document_field = new DateSignedField();
                        $document_field -> setSigner($field -> signer_id);
                        $document_field -> setTextSize(10);
                        //$dateSignedField -> setTextStyle('U');
                        $document_field -> setY($y + 2);

                    } else if($field -> field_type == 'name') {

                        $document_field = new TextField();
                        $document_field -> setSigner($field -> signer_id);
                        $document_field -> setValue($field -> signer);
                        $document_field -> setTextSize(9);
                        $document_field -> setY($y + 3);
                        $document_field -> setRequired(0);

                    } else if($field -> field_type == 'text') {

                        $document_field = new TextField();
                        $document_field -> setSigner('OWNER');
                        $document_field -> setReadonly(1);
                        $document_field -> setValue($field -> field_value);
                        $document_field -> setTextSize(9);
                        $document_field -> setY($y + 3);
                        $document_field -> setRequired(0);

                    }

                    $document_field -> setIdentifier($document_id.$c);
                    $document_field -> setFileIndex($file_index);
                    $document_field -> setPage($field -> page);
                    $document_field -> setX($x);
                    $document_field -> setWidth($w);
                    $document_field -> setHeight($h);



                    $file_to_sign -> appendFormField($document_field, $c);

                    $c += 1;

                }

                $file_index += 1;

            }

        }

        //Saving the created file_to_sign to the API.
        $newlyCreatedDocument = $client -> createDocument($file_to_sign);
        $hash = $newlyCreatedDocument -> getDocumentHash();

        // update esign_envelope table with new hash
        $envelope = EsignEnvelopes::find($envelope_id) -> update([
            'document_hash' => $hash
        ]);

    }
}
