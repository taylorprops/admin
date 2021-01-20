<?php

namespace App\Http\Controllers\Esign;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

use Eversign\File;
use Eversign\Field;
use Eversign\Client;
use Eversign\Signer;
use Eversign\Document;
use Eversign\Recipient;
use Eversign\InitialsField;
use Eversign\SignatureField;
use Eversign\TextField;
use Eversign\DateSignedField;

use App\Models\Esign\EsignFields;
use App\Models\Esign\EsignSigners;
use App\Models\Esign\EsignDocuments;
use App\Models\Esign\EsignEnvelopes;
use App\Models\Esign\EsignDocumentsImages;
use App\Models\Esign\EsignTemplates;

use App\Models\DocManagement\Resources\ResourceItems;

use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;


class EsignController extends Controller {


    ///////////// Dashboard //////////////

    public function esign(Request $request) {

        return view('/esign/esign');
    }

    public function get_drafts(Request $request) {

        $drafts = EsignEnvelopes::where('is_draft', 'yes') -> with('signers') -> get();

        return view('/esign/get_drafts_html', compact('drafts'));

    }

    public function get_deleted_drafts(Request $request) {

        $deleted_drafts = EsignEnvelopes::where('is_draft', 'yes') -> onlyTrashed() -> with('signers') -> get();

        return view('/esign/get_deleted_drafts_html', compact('deleted_drafts'));

    }

    public function get_sent(Request $request) {

        $envelopes = EsignEnvelopes::where('status', 'sent') -> with('signers') -> get();

        return view('/esign/get_sent_html', compact('envelopes'));

    }

    public function get_completed(Request $request) {

        $envelopes = EsignEnvelopes::where('status', 'completed') -> with('signers') -> get();

        return view('/esign/get_completed_html', compact('envelopes'));

    }

    public function get_templates(Request $request) {

        $templates = EsignTemplates::with('signers') -> get();

        return view('/esign/get_templates_html', compact('templates'));

    }

    public function get_deleted_templates(Request $request) {

        $deleted_templates = EsignTemplates::onlyTrashed() -> with('signers') -> get();

        return view('/esign/get_deleted_templates_html', compact('deleted_templates'));

    }


    ////  Drafts ////

    public function save_as_draft(Request $request) {

        $envelope_id = $request -> envelope_id;
        $draft_name = $request -> draft_name;
        $envelope = EsignEnvelopes::find($envelope_id) -> update(['is_draft' => 'yes', 'draft_name' => $draft_name]);

        return response() -> json(['status' => 'success']);

    }

    public function delete_draft(Request $request) {

        $envelope_id = $request -> envelope_id;
        $delete_draft = EsignEnvelopes::find($envelope_id) -> delete();

        return response() -> json(['status' => 'success']);

    }

    public function restore_draft(Request $request) {

        $envelope_id = $request -> envelope_id;
        $restore_draft = EsignEnvelopes::withTrashed() -> where('id', $envelope_id) -> restore();

        return response() -> json(['status' => 'success']);

    }

    //// Template ////

    public function save_as_template(Request $request) {

        $template_id = $request -> template_id;
        $template_name = $request -> template_name;

        $template = EsignTemplates::find($template_id) -> update(['template_name' => $template_name]);

        return response() -> json(['status' => 'success']);

    }

    public function delete_template(Request $request) {

        $envelope_id = $request -> envelope_id;
        $delete_template = EsignEnvelopes::find($envelope_id) -> delete();

        return response() -> json(['status' => 'success']);

    }

    public function restore_template(Request $request) {

        $envelope_id = $request -> envelope_id;
        $restore_template = EsignEnvelopes::withTrashed() -> where('id', $envelope_id) -> restore();

        return response() -> json(['status' => 'success']);

    }



    ///////////// End Dashboard //////////////

    public function esign_add_documents(Request $request) {

        $is_template = 'no';
        if($request -> template) {
            $is_template = 'yes';
        }

        $Listing_ID = $request -> Listing_ID ?? null;
        $Contract_ID = $request -> Contract_ID ?? null;
        $Referral_ID = $request -> Referral_ID ?? null;
        $User_ID = auth() -> user() -> id;
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
                $template_id = $doc -> template_id;

                $file_name = $doc -> file_name;

                $file = Storage::disk('public') -> path(str_replace('/storage', '', $doc -> file_location));
                exec('cp '.$file.' '.$tmp_dir.'/'.$file_name);

                $image_name = str_replace('.pdf', '.jpg', $file_name);

                exec('convert -density 200 -quality 100 '.$tmp_dir.'/'.$file_name.'[0] -flatten -fuzz 1%  '.$tmp_dir.'/'.$image_name);

                $file_location = str_replace('/var/www/admin/storage/app/public', '/storage', $tmp_dir).'/' . $file_name;
                $image_location = str_replace('/var/www/admin/storage/app/public', '/storage', $tmp_dir).'/' . $image_name;

                $details = [
                    'document_id' => $doc -> file_id,
                    'file_type' => $doc -> file_type,
                    'file_name' => $file_name,
                    'file_location' => $file_location,
                    'image_location' => $image_location,
                    'template_id' => $template_id
                ];

                $docs_to_display[] = $details;

            }

        }

        $templates = EsignTemplates::get();

        return view('/esign/esign_add_documents', compact('is_template', 'Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type', 'User_ID', 'Agent_ID', 'document_ids', 'documents', 'docs_to_display', 'templates'));

    }

    public function apply_template(Request $request) {

        $envelope_id = $request -> envelope_id;

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

        $is_template = $request -> is_template;

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $User_ID = $request -> User_ID ?? 0;
        $Agent_ID = $request -> Agent_ID ?? 0;
        $transaction_type = $request -> transaction_type ?? null;

        if($is_template == 'no') {

            // Create envelope
            $template_id = 0;

            $envelope = new EsignEnvelopes();

            $envelope -> Listing_ID = $Listing_ID;
            $envelope -> Contract_ID = $Contract_ID;
            $envelope -> Referral_ID = $Referral_ID;
            $envelope -> User_ID = $User_ID;
            $envelope -> Agent_ID = $Agent_ID;
            $envelope -> transaction_type = $transaction_type;
            $envelope -> save();
            $envelope_id = $envelope -> id;

        } else {

            // Create template
            $template_id = date('YmdHis');

            $template = new EsignTemplates();
            $template -> User_ID = $request -> User_ID;
            $template -> template_name = $request -> template_name;
            $template -> save();
            $template_id = $template -> id;

        }

        // Add documents and images to envelope and add files to storage
        foreach($files as $file) {

            if($is_template == 'yes') {

                // Create envelope
                $envelope = new EsignEnvelopes();
                $envelope -> is_template = $is_template;
                $envelope -> User_ID = $request -> User_ID;
                $envelope -> template_id = $template_id;
                $envelope -> save();
                $envelope_id = $envelope -> id;

            }

            $applied_template_id = $file['template_applied_id'];

            // add doc
            $add_esign_doc = new EsignDocuments();
            $add_esign_doc -> envelope_id = $envelope_id;
            $add_esign_doc -> template_id = $template_id;
            $add_esign_doc -> template_applied_id = $applied_template_id;
            $add_esign_doc -> file_name = $file['file_name'];
            $add_esign_doc -> save();
            $add_esign_document_id = $add_esign_doc -> id;

            // if template applied add fields and signers
            if($applied_template_id > 0) {

                $template_envelopes = EsignTemplates::where('id', $applied_template_id) -> with('signers') -> with('fields') -> get();

                $signers = $template_envelopes -> first() -> signers;

                $fields = $template_envelopes -> first() -> fields;

                // get signer names from members
                $members = null;
                if($transaction_type) {

                    if($transaction_type == 'listing') {

                        $members = Members::where('Listing_ID', $Listing_ID) -> get();

                    } else if($transaction_type == 'contract') {

                        $members = Members::where('Contract_ID', $Contract_ID)
                            -> orWhere(function($query) use ($Listing_ID) {
                                if($Listing_ID > 0) {
                                    $query -> where('Listing_ID', $Listing_ID);
                                }
                            }) -> get();

                    }

                }

                if($members) {

                    $seller_order = 'One';
                    $buyer_order = 'One';

                    foreach($members as $member) {

                        $member_role = ResourceItems::getResourceName($member -> member_type_id);

                        if($member_role == 'Seller') {

                            $member -> member_role = 'Seller '.$seller_order;
                            if($seller_order == 'One') {
                                $seller_order = 'Two';
                            }

                        } else if($member_role == 'Buyer') {

                            $member -> member_role = 'Buyer '.$buyer_order;
                            if($buyer_order == 'One') {
                                $buyer_order = 'Two';
                            }

                        } else {

                            $member -> member_role = $member_role;

                        }

                    }

                }

                foreach($signers as $signer) {

                    $role = $signer -> template_role;
                    $new_signer = $signer -> replicate();
                    $new_signer -> envelope_id = $envelope_id;
                    $new_signer -> template_id = 0;

                    if($members) {
                        foreach($members as $member) {
                            if($member -> member_role == $role) {
                                $new_signer -> signer_name = $member -> first_name.' '.$member -> last_name;
                                $new_signer -> signer_email = $member -> email;
                            }
                        }
                    }

                    $new_signer -> save();

                }

                foreach($fields as $field) {

                    $field_signer = $field -> signer;
                    $field_signer_role = $field_signer -> template_role;
                    $new_signer_id = '';

                    $new_signer = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> where('template_role', $field_signer_role) -> pluck('id');

                    $new_field = $field -> replicate();
                    $new_field -> envelope_id = $envelope_id;
                    $new_field -> template_id = 0;
                    $new_field -> document_id = $add_esign_document_id;
                    $new_field -> signer_id = $new_signer[0];
                    $new_field -> save();

                }

            }

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
                $doc = TransactionUpload::where('Transaction_Docs_ID', $file['document_id']) -> with('images') -> first();

                // copy document
                $doc_from_location = Storage::disk('public') -> path(str_replace('/storage/', '', $doc -> file_location_converted));
                exec('cp -p '.$doc_from_location.' '.$doc_to_location);

                // get file location
                $file_name = basename($doc_from_location);
                $add_esign_doc_file_location = '/storage/'.$document_folder.'/'.$file_name;

                // page count
                $pages_total = exec('pdftk ' . $doc_from_location . ' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

                // update file location
                $add_esign_doc -> file_location = $add_esign_doc_file_location;
                $add_esign_doc -> pages_total = $pages_total;
                $add_esign_doc -> save();

                // get images for doc
                $images = $doc -> images;

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

        return compact('envelope_id', 'is_template', 'template_id');
    }

    public function esign_add_signers(Request $request) {

        $is_template = $request -> is_template;
        $envelope_id = $request -> envelope_id;
        $template_id = $request -> template_id;

        $envelope = EsignEnvelopes::find($envelope_id);

        $transaction_type = $envelope -> transaction_type;
        $Listing_ID = $envelope -> Listing_ID;
        $Contract_ID = $envelope -> Contract_ID;

        if($transaction_type == 'listing') {

            $members = Members::where('Listing_ID', $Listing_ID) -> get();

        } else if($transaction_type == 'contract') {

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

        $signers = [];
        $recipients = [];
        if($is_template == 'no') {
            $signers = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> orderBy('signer_order') -> get();
            $recipients = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'yes') -> orderBy('signer_order') -> get();
        }

        return view('/esign/esign_add_signers', compact('is_template', 'template_id', 'envelope_id', 'envelope', 'members', 'signers', 'recipients', 'resource_items'));

    }

    public function esign_add_signers_to_envelope(Request $request) {

        $is_template = $request -> is_template;
        $envelope_id = $request -> envelope_id;
        $template_id = $request -> template_id;
        $signers = json_decode($request -> signers_data);
        $recipients = json_decode($request -> recipients_data);

        $signer_ids = [];
        foreach($signers as $signer) {
            $signer_ids[] = $signer -> id;
        }
        foreach($recipients as $recipient) {
            $signer_ids[] = $recipient -> id;
        }

        if($is_template == 'yes') {
            $envelope_id = 0;
        } else {
            // delete current signers
            $delete_signers = EsignSigners::where('envelope_id', $envelope_id) -> whereNotIn('id', $signer_ids) -> delete();
        }

        $template_role = '';
        foreach($signers as $signer) {

            if($signer -> role == 'Buyer' || $signer -> role == 'Seller') {
                if($template_role == '') {
                    $template_role = $signer -> role.' One';
                } else if($template_role == $signer -> role.' One') {
                    $template_role = $signer -> role.' Two';
                }
            } else {
                $template_role = $signer -> role;
            }

            if($signer -> id > 0) {
                $add_signer = EsignSigners::find($signer -> id);
            } else {
                $add_signer = new EsignSigners();
            }
            $add_signer -> envelope_id = $envelope_id;
            $add_signer -> template_id = $template_id;
            $add_signer -> signer_name = $signer -> name;
            $add_signer -> signer_email = $signer -> email;
            $add_signer -> signer_role = $signer -> role;
            $add_signer -> template_role = $template_role;
            $add_signer -> signer_order = $signer -> order;
            $add_signer -> recipient_only = 'no';
            $add_signer -> save();
        }

        $template_role = '';
        foreach($recipients as $recipient) {

            if($recipient -> role == 'Buyer' || $recipient -> role == 'Seller') {
                if($template_role == '') {
                    $template_role = $recipient -> role.' One';
                } else if($template_role == $recipient -> role.' One') {
                    $template_role = $recipient -> role.' Two';
                }
            } else {
                $template_role = $recipient -> role;
            }

            if($recipient -> id > 0) {
                $add_recipient = EsignSigners::find($recipient -> id);
            } else {
                $add_recipient = new EsignSigners();
            }
            $add_recipient -> envelope_id = $envelope_id;
            $add_recipient -> template_id = $template_id;
            $add_recipient -> signer_name = $recipient -> name;
            $add_recipient -> signer_email = $recipient -> email;
            $add_recipient -> signer_role = $recipient -> role;
            $add_recipient -> template_role = $template_role;
            $add_recipient -> signer_order = $recipient -> order;
            $add_recipient -> recipient_only = 'yes';
            $add_recipient -> save();
        }

        return compact('envelope_id');

    }

    public function esign_add_fields(Request $request) {

        $is_template = $request -> is_template;
        $template_id = $request -> template_id;
        $envelope_id = $request -> envelope_id;

        $draft_name = '';
        $property_address = '';
        $template_name = '';

        if($envelope_id > 0) {

            $envelope = EsignEnvelopes::find($envelope_id);

            $draft_name = $envelope -> draft_name ?? null;
            $property_address = null;
            if($envelope -> transaction_type != '') {
                $property = Listings::GetPropertyDetails($envelope -> transaction_type, [$envelope -> Listing_ID, $envelope -> Contract_ID, $envelope -> Referral_ID]);
                $property_address = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
            }

            $documents = EsignDocuments::where('envelope_id', $envelope_id) -> with('images') -> with('fields') -> get();

            $signers = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> orderBy('signer_order') -> get();

        } else {

            $envelopes = EsignEnvelopes::where('template_id', $template_id) -> get();

            $template = EsignTemplates::find($template_id);
            $template_name = $template -> template_name;

            $documents = collect();
            foreach($envelopes as $envelope) {

                $doc = EsignDocuments::where('envelope_id', $envelope -> id) -> with('images') -> with('fields') -> get();
                $documents = $documents -> merge($doc);

            }

            $signers = EsignSigners::where('template_id', $template_id) -> where('recipient_only', 'no') -> orderBy('signer_order') -> get();

        }


        //$recipients = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'yes') -> get();

        $signers_options = [];

        foreach($signers as $signer) {

            $signer_name = $signer -> signer_name;
            $signers_options[] = '<option class="signer-select-option" value="'.$signer_name.'" data-role="'.$signer -> signer_role.'" data-name="'.$signer_name.'" data-signer-id="'.$signer -> id.'">'.$signer_name.' - '.$signer -> signer_role.'</option>';

            $signer_options_template[] = '<option class="signer-select-option" value="'.$signer -> template_role.'" data-role="'.$signer -> signer_role.'" data-template-role="'.$signer -> template_role.'" data-name="'.$signer -> template_role.'" data-signer-id="'.$signer -> id.'">'.$signer -> template_role.'</option>';

        }


        return view('/esign/esign_add_fields', compact('is_template', 'template_id', 'envelope_id', 'template_name', 'draft_name', 'property_address', 'documents', 'signers', 'signers_options', 'signer_options_template'));

    }

    public function esign_send_for_signatures(Request $request) {

        $envelope_id = $request -> envelope_id;
        $template_id = $request -> template_id;
        $document_ids = explode(',', $request -> document_ids);

        $fields = json_decode($request -> fields, true);
        $fields = collect($fields) -> map(function ($fields) {
            return (object) $fields;
        });

        if($template_id > 0) {
            $delete_current_fields = EsignFields::where('template_id', $template_id) -> delete();
        } else {
            $delete_current_fields = EsignFields::where('envelope_id', $envelope_id) -> delete();
        }

        // add fields to db
        foreach($fields as $field) {

            $add_field = new EsignFields();
            $add_field -> envelope_id = $envelope_id;
            $add_field -> template_id = $template_id;
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

        if($request -> is_draft == 'yes') {
            return response() -> json(['status' => 'draft saved']);
        } else if($request -> is_template == 'yes') {
            return response() -> json(['status' => 'template saved']);
        }

        $subject = $request -> subject;
        $message = $request -> message;

        // update esign_envelope table with subject and message
        $envelope = EsignEnvelopes::find($envelope_id) -> update([
            'subject' => $subject,
            'message' => $message,
            'is_draft' => 'no',
            'draft_name' => '',
            'status' => 'sent'
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

                    } else if($field -> field_type == 'name') {

                        $document_field = new TextField();
                        $document_field -> setValue($field -> signer);
                        $document_field -> setTextSize(11);
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
        $hash = $newlyCreatedDocument -> getDocumentHash();

        // update esign_envelope table with new hash
        $envelope = EsignEnvelopes::find($envelope_id) -> update([
            'document_hash' => $hash
        ]);

        return response() -> json(['status' => 'sent']);

    }



}
