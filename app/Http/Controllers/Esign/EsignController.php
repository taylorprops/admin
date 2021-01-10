<?php

namespace App\Http\Controllers\Esign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;

use Eversign\Client;
use Eversign\Document;
use Eversign\Field;
use Eversign\Signer;
use Eversign\Recipient;
use Eversign\File;
use Eversign\SignatureField;
use Eversign\InitialsField;
use Eversign\DateSignedField;

class EsignController extends Controller {

    public function esign(Request $request) {

        return view('/esign/esign');
    }

    public function esign_add_documents(Request $request) {

        return view('/esign/esign_add_documents');
    }

    public function esign_add_signers(Request $request) {

        return view('/esign/esign_add_signers');
    }

    public function esign_add_fields(Request $request) {

        $Listing_ID = $request -> Listing_ID;
        $Contract_ID = $request -> Contract_ID;
        $Referral_ID = $request -> Referral_ID;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;
        $document_ids = explode(',', $request -> document_ids);

        /* $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, $Referral_ID]);
        if($transaction_type == 'contract') {
            $members = Members::where('Contract_ID', $Contract_ID) -> where('transaction_type', $transaction_type) -> get();
        } else if($transaction_type == 'listing') {
            $members = Members::where('Listing_ID', $Listing_ID) -> where('transaction_type', $transaction_type) -> get();
        } */

        // need documents to be in order of checked docs
        $documents = collect();
        foreach($document_ids as $document_id) {
            $docs = TransactionDocuments::where('file_id', $document_id) -> with('images_converted') -> get();
            $documents = $documents -> merge($docs);
        }

        return view('/esign/esign_add_fields', compact('Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type', 'Agent_ID', 'document_ids', 'documents'));

    }

    public function esign_send_for_signatures(Request $request) {

        $Agent_ID = $request -> Agent_ID;
        $document_ids = explode(',', $request -> document_ids);

        $fields = json_decode($request -> fields, true);

        $fields = collect($fields) -> map(function ($fields) {
            return (object) $fields;
        });


        $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));

        $file_to_sign = new document();
        $file_to_sign -> setSandbox(true);
        $file_to_sign -> setTitle('Form Test');
        $file_to_sign -> setMessage('Test Message');
        $file_to_sign -> setEmbeddedSigningEnabled(true);
        //$file_to_sign -> setFlexibleSigning(false); // remove all fields to try this
        $file_to_sign -> setUseHiddenTags(true);
        $file_to_sign -> setRequireAllSigners(true);
        $file_to_sign -> setUseSignerOrder(true);
        $file_to_sign -> setCustomRequesterName('Mike');
        $file_to_sign -> setCustomRequesterEmail('info@taylorprops.com');

        $date = new \DateTime();
        $date -> add(new \DateInterval('P14D'));
        $file_to_sign -> setExpires($date);

        //Create a Signer for the file_to_sign
        $signer = new Signer();
        $signer -> setName('John Doe');
        $signer -> setEmail('miketaylor0101@gmail.com');
        $signer -> setDeliverEmail(true); // only used if embedded_signing_enabled is used
        $signer -> setLanguage('en');
        $file_to_sign -> appendSigner($signer);

        //Create a Recipient for the Document
        /* $recipient = new Recipient();
        $recipient -> setName('Office');
        $recipient -> setEmail('info@taylorprops.com');
        $recipient -> setLanguage('en');
        $file_to_sign -> appendRecipient($recipient); */

        $file_index = 0;
        foreach($document_ids as $document_id) {

            $document = TransactionDocuments::where('file_id', $document_id) -> first();

            if(count($fields -> where('document_id', $document_id)) > 0) {

                //Add a File to the Document
                $file = new File();
                $file -> setName($document -> file_name_display);
                $file -> setFilePath(getcwd().$document -> file_location_converted);
                $file_to_sign -> appendFile($file);

                /* $image_info = getImageGeometry(base_path().$document -> file_location_converted);
                dd($image_info);
                $height = $image_info[0];
                $width = $image_info[1]; */
                $c = 0;

                foreach($fields -> where('document_id', $document_id) as $field) {

                    // increase move down and right
                    $x = ($field -> left_perc/100) * 610;
                    $y = ($field -> top_perc/100) * 792;
                    $w = ($field -> width_perc/100) * 610;
                    $h = ($field -> height_perc/100) * 792;

                    if($field -> field_type == 'signature') {

                        $signatureField = new SignatureField();
                        $signatureField -> setIdentifier($document_id.$c);
                        $signatureField -> setFileIndex($file_index);
                        $signatureField -> setPage($field -> page);
                        $signatureField -> setX($x);
                        $signatureField -> setY($y + 5);
                        $signatureField -> setWidth($w);
                        $signatureField -> setHeight($h);
                        $signatureField -> setRequired($field -> required == 'yes' ? true : false);
                        $signatureField -> setSigner('1');
                        $file_to_sign -> appendFormField($signatureField, $c);

                    } else if($field -> field_type == 'initials') {

                        $initialsField = new InitialsField();
                        $initialsField -> setIdentifier($document_id.$c);
                        $initialsField -> setFileIndex($file_index);
                        $initialsField -> setPage($field -> page);
                        $initialsField -> setX($x);
                        $initialsField -> setY($y + 5);
                        $initialsField -> setWidth($w);
                        $initialsField -> setHeight($h);
                        $initialsField -> setRequired($field -> required == 'yes' ? true : false);
                        $initialsField -> setSigner('1');
                        $file_to_sign -> appendFormField($initialsField, $c);

                    } else if($field -> field_type == 'date') {

                        $dateSignedField = new DateSignedField();
                        $dateSignedField -> setIdentifier($document_id.$c);
                        $dateSignedField -> setFileIndex($file_index);
                        $dateSignedField -> setPage($field -> page);
                        $dateSignedField -> setX($x);
                        $dateSignedField -> setY($y);
                        $dateSignedField -> setWidth($w);
                        $dateSignedField -> setHeight($h);
                        $dateSignedField -> setSigner('1');
                        $dateSignedField -> setTextSize(11);
                        //$dateSignedField -> setTextStyle('U');
                        $file_to_sign -> appendFormField($dateSignedField, $c);

                    }

                    $c += 1;

                }

                $file_index += 1;

            }

        }


        //Saving the created file_to_sign to the API.
        $newlyCreatedDocument = $client -> createDocument($file_to_sign);
        echo $newlyCreatedDocument -> getDocumentHash();


    }

}
