<?php

namespace App\Http\Controllers\Esign;

use Eversign\File;
use Eversign\Field;

use Eversign\Client;

use Eversign\Signer;
use Eversign\Document;
use Eversign\Recipient;
use Eversign\InitialsField;
use Eversign\SignatureField;
use Illuminate\Http\Request;

use Eversign\DateSignedField;
use App\Models\Esign\EsignFields;
use App\Models\Esign\EsignSigners;
use App\Http\Controllers\Controller;
use App\Models\Esign\EsignDocuments;
use App\Models\Esign\EsignEnvelopes;
use Illuminate\Support\Facades\Storage;
use App\Models\Esign\EsignDocumentsImages;

use App\Models\DocManagement\Resources\ResourceItems;

use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;


class EsignController extends Controller {


    public function esign(Request $request) {

        return view('/esign/esign');
    }

    public function esign_add_documents(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? null;
        $Contract_ID = $request -> Contract_ID ?? null;
        $Referral_ID = $request -> Referral_ID ?? null;
        $User_ID = $request -> User_ID ?? null;
        $Agent_ID = $request -> Agent_ID ?? null;
        $transaction_type = $request -> transaction_type ?? null;

        $document_ids = [];
        $documents = null;
        $docs_to_display = null;

        if($request -> document_ids) {

            $document_ids = explode(',', $request -> document_ids);

            // need documents to be in order of checked docs
            $documents = collect();
            $tmp_dir = Storage::disk('public') -> path('tmp');
            $docs_to_display = [];

            foreach($document_ids as $document_id) {

                $doc = TransactionDocuments::where('file_id', $document_id) -> with('images_converted') -> first();
                $documents = $documents -> merge($doc);

                $file_name = $doc -> file_name;

                $file = Storage::disk('public') -> path(str_replace('/storage', '', $doc -> file_location));
                exec('cp '.$file.' '.$tmp_dir.'/'.$file_name);

                $image_name = str_replace('.pdf', '.jpg', $file_name);

                exec('convert -density 200 -quality 100 '.$tmp_dir.'/'.$file_name.'[0] -flatten -fuzz 1%  '.$tmp_dir.'/'.$image_name);

                $file_location = str_replace('/var/www/admin/storage/app/public', '/storage', $tmp_dir).'/' . $file_name;
                $image_location = str_replace('/var/www/admin/storage/app/public', '/storage', $tmp_dir).'/' . $image_name;

                $details = [
                    'document_id' => $doc -> id,
                    'file_type' => $doc -> file_type,
                    'file_name' => $file_name,
                    'file_location' => $file_location,
                    'image_location' => $image_location
                ];

                $docs_to_display[] = $details;

            }

        }

        return view('/esign/esign_add_documents', compact('Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type', 'User_ID', 'Agent_ID', 'document_ids', 'documents', 'docs_to_display'));

    }

    public function upload(Request $request) {

        $files = $request -> file('esign_file_upload');

        $User_ID = $request -> User_ID;
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;

        $docs_to_display = [];

        foreach($files as $file) {

            $ext = $file -> getClientOriginalExtension();
            $file_name = $file -> getClientOriginalName();

            $file_name_remove_numbers = preg_replace('/[0-9-_\s\.]+\.' . $ext . '/', '.' . $ext, $file_name);
            $file_name_remove_numbers = preg_replace('/^[0-9-_\s\.]+/', '', $file_name_remove_numbers);
            $file_name_no_ext = str_replace('.' . $ext, '', $file_name_remove_numbers);
            $clean_file_name = sanitize($file_name_no_ext);
            $file_name_display = $clean_file_name . '.' . $ext;
            $new_file_name = date('YmdHis') . '_' . $file_name_display;

            $tmp_dir = Storage::disk('public') -> path('tmp');

            // convert to pdf if image
            if($ext != 'pdf') {
                $file_name_display = $clean_file_name . '.pdf';
                $new_file_name = date('YmdHis') . '_' . $file_name_display;
                $create_images = exec('convert -quality 100 -density 300 -page letter ' . $file . ' '.$tmp_dir.'/' . $new_file_name, $output, $return);
            } else {
                move_uploaded_file($file, $tmp_dir.'/'.$new_file_name);
            }

            $new_image_name = str_replace('.pdf', '.jpg', $new_file_name);

            exec('convert -density 200 -quality 100 '.$tmp_dir.'/'.$new_file_name.'[0] -flatten -fuzz 1%  '.$tmp_dir.'/'.$new_image_name);

            $file_location = str_replace('/var/www/admin/storage/app/public', '/storage', $tmp_dir).'/' . $new_file_name;
            $image_location = str_replace('/var/www/admin/storage/app/public', '/storage', $tmp_dir).'/' . $new_image_name;

            $details = [
                'file_name' => $file_name_display,
                'file_location' => $file_location,
                'image_location' => $image_location
            ];

            $docs_to_display[] = $details;

        }

        return compact('docs_to_display');

    }

    public function esign_create_envelope(Request $request) {

        $files = json_decode($request -> file_data, true);

        // Create envelope
        $envelope = new EsignEnvelopes();

        $envelope -> Listing_ID = $request -> Listing_ID;
        $envelope -> Contract_ID = $request -> Contract_ID;
        $envelope -> Referral_ID = $request -> Referral_ID;
        $envelope -> User_ID = $request -> User_ID;
        $envelope -> Agent_ID = $request -> Agent_ID;
        $envelope -> transaction_type = $request -> transaction_type;
        $envelope -> save();
        $envelope_id = $envelope -> id;

        // Add documents and images to envelope and add files to storage
        foreach($files as $file) {

            // add doc
            $add_esign_doc = new EsignDocuments();
            $add_esign_doc -> envelope_id = $envelope_id;
            $add_esign_doc -> file_name = $file['file_name'];
            $add_esign_doc -> save();
            $add_esign_document_id = $add_esign_doc -> id;

            // create directories for doc and images
            $document_folder = 'esign/'.$envelope_id.'/'.$add_esign_document_id;

            $doc_to_location = Storage::disk('public') -> path($document_folder);
            if(!is_dir($doc_to_location)) {
                Storage::disk('public') -> makeDirectory($document_folder);
            }

            $image_to_location = Storage::disk('public') -> path($document_folder.'/images');
            if(!is_dir($image_to_location)) {
                Storage::disk('public') -> makeDirectory($document_folder.'/images');
            }

            if($file['document_id'] > 0) {

                // transfer files and images from transactions docs
                $doc = TransactionDocuments::where('file_id', $file['document_id']) -> with('images_converted') -> first();

                // copy document
                $doc_from_location = Storage::disk('public') -> path(str_replace('/storage/', '', $doc -> file_location_converted));
                exec('cp -p '.$doc_from_location.' '.$doc_to_location);

                // get file location
                $file_name = basename($doc_from_location);
                $add_esign_doc_file_location = '/storage/esign/'.$envelope_id.'/'.$file_name;

                // page count
                $pages_total = exec('pdftk ' . $doc_from_location . ' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

                // update file location
                $add_esign_doc -> file_location = $add_esign_doc_file_location;
                $add_esign_doc -> pages_total = $pages_total;
                $add_esign_doc -> save();

                // get images for doc
                $images = $doc -> images_converted;

                foreach($images as $image) {

                    // copy images
                    $image_from_location = Storage::disk('public') -> path(str_replace('/storage/', '', $image -> file_location));
                    exec('cp -p '.$image_from_location.' '.$image_to_location);

                    // get file name
                    $file_name = basename($image_from_location);
                    $add_esign_image_file_location = '/storage/'.$document_folder.'/images/'.$file_name;

                    $add_esign_image = new EsignDocumentsImages();
                    $add_esign_image -> image_location = $add_esign_image_file_location;
                    $add_esign_image -> envelope_id = $envelope_id;
                    $add_esign_image -> document_id = $add_esign_document_id;
                    $add_esign_image -> page_number = $image -> page_number;
                    $add_esign_image -> save();

                }

            } else {

                // add files from tmp and create images

                // copy document
                $doc_from_location = Storage::disk('public') -> path(str_replace('/storage/', '', $file['file_location']));
                exec('cp -p '.$doc_from_location.' '.$doc_to_location);

                // get file location
                $file_name = basename($doc_from_location);
                $add_esign_doc_file_location = '/storage/'.$document_folder.'/'.$file_name;

                // update location
                $add_esign_doc -> file_location = $add_esign_doc_file_location;
                $add_esign_doc -> save();

                $input_file = $doc_from_location;
                $output_images = $image_to_location.'/page_%02d.jpg';

                // add individual images to images directory
                // $create_images = exec('convert -density 300 -gaussian-blur 0.05 -quality 80% -resize 1200 '.$input_file.' -background white -alpha remove -strip '.$output_images, $output, $return);
                $create_images = exec('convert -density 200 -quality 80% -resize 1200 '.$input_file.' -background white -alpha remove -strip '.$output_images, $output, $return);

                // get all image files images_storage_path to use as file location
                $images_public_path = '/storage/'.$document_folder.'/images';
                $saved_images_directory = Storage::disk('public') -> files($document_folder.'/images');


                foreach ($saved_images_directory as $saved_image) {
                    // get just filename
                    $images_file_name = basename($saved_image);
                    $add_esign_image_file_location = $images_public_path.'/'.$images_file_name;
                    $page_number = preg_match('/page_([0-9]+)\.jpg/', $images_file_name, $matches);
                    $match = $matches[1];
                    if(substr($match, 0, 1 == 0)) {
                        $match = substr($match, 1);
                    }
                    $page_number = count($matches) > 1 ? $match + 1 : 1;

                    // add images to database
                    $add_esign_image = new EsignDocumentsImages();
                    $add_esign_image -> image_location = $add_esign_image_file_location;
                    $add_esign_image -> envelope_id = $envelope_id;
                    $add_esign_image -> document_id = $add_esign_document_id;
                    $add_esign_image -> page_number = $page_number;
                    $add_esign_image -> save();

                }

            }

        }

        return compact('envelope_id');
    }

    public function esign_add_signers(Request $request) {

        $envelope_id = $request -> envelope_id;
        $envelope = EsignEnvelopes::find($envelope_id);
        $Listing_ID = $envelope -> Listing_ID;
        $Contract_ID = $envelope -> Contract_ID;

        if($envelope -> transaction_type == 'listing') {

            $members = Members::where('Listing_ID', $Listing_ID) -> get();

        } else if($envelope -> transaction_type == 'contract') {

            $members = Members::where('Contract_ID', $Contract_ID)
                -> orWhere(function($query) use ($Listing_ID) {
                    if($Listing_ID > 0) {
                        $query -> where('Listing_ID', $Listing_ID);
                    }
                }) -> get();

        } else {
            $members = null;
        }

        $resource_items = new ResourceItems();

        if($members) {

            $members -> map(function ($member) use ($resource_items) {

                $member_type = $resource_items -> getResourceName($member -> member_type_id);
                $member['member_type'] = $member_type;

                $order = [
                    'Seller' => 1,
                    'Buyer' => 2,
                    'Listing Agent' => 3,
                    'Buyer Agent' => 4
                ][$member_type] ?? 15;
                $member['order'] = $order;

                return $member;

            });

            $members = $members -> sortBy('order');

        }

        return view('/esign/esign_add_signers', compact('envelope_id', 'envelope', 'members', 'resource_items'));

    }

    public function esign_add_signers_to_envelope(Request $request) {

        $envelope_id = $request -> envelope_id;
        $signers = json_decode($request -> signers_data);
        $recipients = json_decode($request -> recipients_data);

        foreach($signers as $signer) {
            $add_signer = new EsignSigners();
            $add_signer -> envelope_id = $envelope_id;
            $add_signer -> signer_name = $signer -> name;
            $add_signer -> signer_email = $signer -> email;
            $add_signer -> signer_role = $signer -> role;
            $add_signer -> signer_order = $signer -> order;
            $add_signer -> recipient_only = 'no';
            $add_signer -> save();
        }

        foreach($recipients as $recipient) {
            $add_recipient = new EsignSigners();
            $add_recipient -> envelope_id = $envelope_id;
            $add_recipient -> signer_name = $recipient -> name;
            $add_recipient -> signer_email = $recipient -> email;
            $add_recipient -> signer_role = $recipient -> role;
            $add_recipient -> signer_order = $recipient -> order;
            $add_recipient -> recipient_only = 'yes';
            $add_recipient -> save();
        }

        return compact('envelope_id');

    }

    public function esign_add_fields(Request $request) {

        $envelope_id = $request -> envelope_id;

        $documents = EsignDocuments::where('envelope_id', $envelope_id) -> with('images') -> get();

        $signers = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> orderBy('signer_order') -> get();
        $recipients = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'yes') -> get();

        $signers_options = [];

        foreach($signers as $signer) {
            $signers_options[] = '<option class="signer-select-option" value="'.$signer -> signer_name.'" data-role="'.$signer -> signer_role.'" data-name="'.$signer -> signer_name.'" data-signer-id="'.$signer -> id.'">'.$signer -> signer_name.' - '.$signer -> signer_role.'</option>';
        }


        return view('/esign/esign_add_fields', compact('envelope_id', 'documents', 'signers', 'signers_options'));

        /* $Listing_ID = $request -> Listing_ID;
        $Contract_ID = $request -> Contract_ID;
        $Referral_ID = $request -> Referral_ID;
        $User_ID = $request -> User_ID;
        $Agent_ID = $request -> Agent_ID;
        $transaction_type = $request -> transaction_type;
        $document_ids = explode(',', $request -> document_ids);

        // need documents to be in order of checked docs
        $documents = collect();
        foreach($document_ids as $document_id) {
            $docs = TransactionDocuments::where('file_id', $document_id) -> with('images_converted') -> get();
            $documents = $documents -> merge($docs);
        }

        return view('/esign/esign_add_fields', compact('Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type', 'User_ID', 'Agent_ID', 'document_ids', 'documents')); */

    }

    public function esign_send_for_signatures(Request $request) {

        $envelope_id = $request -> envelope_id;
        $document_ids = explode(',', $request -> document_ids);

        $fields = json_decode($request -> fields, true);
        $fields = collect($fields) -> map(function ($fields) {
            return (object) $fields;
        });

        // add fields to db
        foreach($fields as $field) {

            $add_field = new EsignFields();
            $add_field -> envelope_id = $envelope_id;
            $add_field -> document_id = $field -> document_id;
            $add_field -> signer_id = $field -> signer_id;
            $add_field -> field_id = $field -> field_id;
            $add_field -> field_type = $field -> field_type;
            $add_field -> required = $field -> required;
            $add_field -> page = $field -> page;
            $add_field -> left_perc = $field -> left_perc;
            $add_field -> top_perc = $field -> top_perc;
            $add_field -> height_perc = $field -> height_perc;
            $add_field -> width_perc = $field -> width_perc;
            $add_field -> save();

        }

        $subject = $request -> subject;
        $message = $request -> message;

        // update esign_envelope table with subject and message
        $envelope = EsignEnvelopes::find($envelope_id) -> update([
            'subject' => $subject,
            'message' => $message
        ]);

        $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));

        $file_to_sign = new document();
        $file_to_sign -> setSandbox(true);
        $file_to_sign -> setTitle($subject);
        $file_to_sign -> setMessage($message);
        $file_to_sign -> setEmbeddedSigningEnabled(true);
        //$file_to_sign -> setFlexibleSigning(false); // remove all fields to try this
        $file_to_sign -> setUseHiddenTags(true);
        $file_to_sign -> setRequireAllSigners(true);
        $file_to_sign -> setUseSignerOrder(true);
        $file_to_sign -> setCustomRequesterName(auth() -> user() -> name);
        $file_to_sign -> setCustomRequesterEmail(auth() -> user() -> email);

        $date = new \DateTime();
        $date -> add(new \DateInterval('P14D'));
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

                // $image_info = getImageGeometry(base_path().$document -> file_location_converted);
                // dd($image_info);
                // $height = $image_info[0];
                // $width = $image_info[1];
                $c = 0;

                foreach($fields -> where('document_id', $document_id) as $field) {

                    // increase move down and right
                    $x = ($field -> left_perc/100) * 612;
                    $y = ($field -> top_perc/100) * 792;
                    $w = ($field -> width_perc/100) * 612;
                    $h = ($field -> height_perc/100) * 792;

                    if($field -> field_type == 'signature') {

                        $document_field = new SignatureField();
                        $document_field -> setRequired($field -> required == 'yes' ? true : false);
                        $document_field -> setY($y + 5);

                    } else if($field -> field_type == 'initials') {

                        $document_field = new InitialsField();
                        $document_field -> setRequired($field -> required == 'yes' ? true : false);
                        $document_field -> setY($y + 5);

                    } else if($field -> field_type == 'date') {

                        $document_field = new DateSignedField();
                        $document_field -> setTextSize(11);
                        //$dateSignedField -> setTextStyle('U');
                        $document_field -> setY($y);

                    }

                    $document_field -> setIdentifier($document_id.$c);
                    $document_field -> setFileIndex($file_index);
                    $document_field -> setPage($field -> page);
                    $document_field -> setX($x);
                    $document_field -> setWidth($w);
                    $document_field -> setHeight($h);
                    $document_field -> setSigner($field -> signer_id);

                    $file_to_sign -> appendFormField($document_field, $c);

                    $c += 1;

                }

                $file_index += 1;

            }

        }

        //Saving the created file_to_sign to the API.
        $newlyCreatedDocument = $client -> createDocument($file_to_sign);
        dump($newlyCreatedDocument);


    }

}
