<?php

namespace App\Http\Controllers\Esign;

use Throwable;
use Eversign\File;
use Eversign\Field;
use Eversign\Client;
use Eversign\Signer;
use Eversign\Document;

use Eversign\Recipient;
use Eversign\TextField;
use Eversign\InitialsField;
use Eversign\SignatureField;
use Illuminate\Http\Request;
use Eversign\DateSignedField;
use App\Models\Esign\EsignFields;

use App\Models\Esign\EsignSigners;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Models\Esign\EsignCallbacks;
use App\Models\Esign\EsignDocuments;
use App\Models\Esign\EsignEnvelopes;
use App\Models\Esign\EsignTemplates;
// use App\Jobs\Esign\SendForSignatures;
use Illuminate\Support\Facades\Storage;
use App\Models\Esign\EsignDocumentsImages;
use App\Models\Esign\EsignTemplatesFields;
use App\Models\Esign\EsignTemplatesSigners;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\Esign\EsignTemplatesDocumentImages;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;

class EsignController extends Controller
{
    ///////////// Dashboard //////////////

    public function esign(Request $request) {

		return view('/esign/esign');
    }

    public function get_drafts(Request $request) {

		$drafts = EsignEnvelopes::where('is_draft', 'yes') -> with(['signers', 'documents']) -> orderBy('created_at', 'desc') -> get();

        return view('/esign/get_drafts_html', compact('drafts'));
    }

    public function get_deleted_drafts(Request $request) {

		$deleted_drafts = EsignEnvelopes::onlyTrashed() -> where('is_draft', 'yes') -> with(['signers', 'documents']) -> orderBy('created_at', 'desc') -> get();

        return view('/esign/get_deleted_drafts_html', compact('deleted_drafts'));

    }

    public function get_in_process(Request $request) {

		$envelopes = EsignEnvelopes::whereIn('status', ['Created', 'Viewed', 'Sent', 'Signed'])
            -> with(['signers', 'callbacks', 'listing', 'contract', 'referral', 'documents'])
            -> orderBy('created_at', 'desc') -> get();

        return view('/esign/get_in_process_html', compact('envelopes'));

    }

    public function get_completed(Request $request) {

		$envelopes = EsignEnvelopes::where('status', 'completed') -> with(['signers', 'documents']) -> get();

        return view('/esign/get_completed_html', compact('envelopes'));
    }

    public function get_templates(Request $request) {

		$templates = EsignTemplates::where('template_type', 'user') -> where('user_id', auth() -> user() -> id) -> with(['signers']) -> get();

        return view('/esign/get_templates_html', compact('templates'));
    }

    public function get_deleted_templates(Request $request) {

		$deleted_templates = EsignTemplates::onlyTrashed()
        -> where('user_id', auth() -> user() -> id)
        -> where(function ($query) {
            $query -> where('upload_file_id', '0')
                -> orWhere('upload_file_id', '')
                -> orWhereNull('upload_file_id');
        })
        -> with(['envelopes', 'signers']) -> get();

        return view('/esign/get_deleted_templates_html', compact('deleted_templates'));
    }

    public function get_system_templates(Request $request) {

		$templates = EsignTemplates::where('upload_file_id', '>', '0') -> with(['envelopes', 'signers']) -> get();

        return view('/esign/get_system_templates_html', compact('templates'));
    }

    public function get_deleted_system_templates(Request $request) {

		$deleted_templates = EsignTemplates::onlyTrashed() -> where('upload_file_id', '>', '0') -> with(['envelopes', 'signers']) -> get();

        return view('/esign/get_deleted_system_templates_html', compact('deleted_templates'));
    }

    public function get_canceled(Request $request) {

		$envelopes = EsignEnvelopes::whereIn('status', ['Declined', 'Signer Removed', 'Signer Bounced', 'Canceled', 'Expired']) -> with(['signers', 'documents']) -> orderBy('created_at', 'desc') -> get();

        return view('/esign/get_canceled_html', compact('envelopes'));
    }

    public function cancel_envelope(Request $request) {

		$envelope_id = $request -> envelope_id;
        $envelope = EsignEnvelopes::find($envelope_id);

        $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));
        $document = $client -> getDocumentByHash($envelope -> document_hash);

        if($document -> getIsCancelled() == true) {
            $envelope -> update([
                'status' => 'Canceled'
            ]);
            return response() -> json(['status' => 'canceled']);
        }

        $client -> cancelDocument($document);

        return response() -> json(['status' => 'success']);

    }

    public function resend_envelope(Request $request) {

		$signer_id = $request -> signer_id;
        $envelope_id = $request -> envelope_id;
        $envelope = EsignEnvelopes::find($envelope_id);

        $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));
        $document = $client -> getDocumentByHash($envelope -> document_hash);

        if($document -> getIsCancelled() == true) {
            $envelope -> update([
                'status' => 'Canceled'
            ]);
            return response() -> json(['status' => 'canceled']);
        }

        $signers = $document -> getSigners();
        $signer = null;
        foreach ($signers as $signer) {
            if ($signer -> getStatus() == 'waiting_for_signature') {
                $client -> sendReminderForDocument($document, $signer);
            }
        }

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

		$template_id = $request -> template_id;
        $delete_template = EsignTemplates::find($template_id) -> delete();

        return response() -> json(['status' => 'success']);
    }

    public function restore_template(Request $request) {

		$template_id = $request -> template_id;
        $restore_template = EsignTemplates::withTrashed() -> where('id', $template_id) -> restore();

        return response() -> json(['status' => 'success']);
    }

    public function delete_system_template(Request $request) {

		$template_id = $request -> template_id;
        $delete_template = EsignTemplates::find($template_id) -> delete();

        return response() -> json(['status' => 'success']);
    }

    public function restore_system_template(Request $request) {

		$template_id = $request -> template_id;
        $restore_template = EsignTemplates::withTrashed() -> where('id', $template_id) -> restore();

        return response() -> json(['status' => 'success']);
    }

    ///////////// End Dashboard //////////////

    public function esign_add_documents(Request $request) {

        // from agent documents
        $Listing_ID = $request -> Listing_ID ?? null;
        $Contract_ID = $request -> Contract_ID ?? null;
        $Referral_ID = $request -> Referral_ID ?? null;
        $User_ID = auth() -> user() -> id;
        $Agent_ID = $request -> Agent_ID ?? null;
        $transaction_type = $request -> transaction_type ?? null;

        $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, $Referral_ID]);
        $address = null;

        if($property != 'not found') {
            $address = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
        }

        // from uploads
        $from_upload = 'no';
        $document_id = $request -> document_id ?? null;
        if ($document_id) {
            $from_upload = 'yes';
        }

        $document_ids = [];
        $documents = null;
        $docs_to_display = null;

        if ($request -> document_ids || $request -> document_id) {
            $document_ids = [$request -> document_id];
            if ($request -> document_ids) {
                $document_ids = explode(',', $request -> document_ids);
            }

            // need documents to be in order of checked docs
            $documents = collect();
            $tmp_folder = date('YmdHis');
            Storage::makeDirectory('tmp/'.$tmp_folder);
            $tmp_dir = Storage::path('tmp/'.$tmp_folder);

            $docs_to_display = [];

            foreach ($document_ids as $document_id) {

                $transaction_doc = TransactionDocuments::find($document_id);
                $signed = null;
                if($transaction_doc -> signed == 'yes') {
                    $signed = 'yes';
                }

                if ($from_upload == 'yes') {
                    $doc = Upload::where('file_id', $document_id) -> with(['images']) -> first();
                    $documents = $documents -> merge($doc);
                    $doc_template_id = $doc -> template_id;

                    $file_name = $doc -> file_name;
                    $file_name_display = $doc -> file_name_display;
                    $file_location = $doc -> file_location;
                    $file_id = '';
                    $file_type = 'system';
                    $data_upload_id = $doc -> file_id;
                } else {
                    $doc = TransactionDocuments::with(['images_converted']) -> find($document_id);
                    $documents = $documents -> merge($doc);
                    $doc_template_id = $doc -> template_id;

                    $file_name = $doc -> file_name;
                    $file_name_display = $doc -> file_name_display;
                    $file_location = $doc -> file_location;
                    $file_id = $doc -> id;
                    $file_type = $doc -> file_type;
                    $data_upload_id = '';
                }

                $file = Storage::path(str_replace('/storage/', '', $file_location));
                exec('cp '.$file.' '.$tmp_dir.'/'.$file_name);

                $image_name = str_replace('.pdf', '.jpg', $file_name);

                exec('convert -density 200 -quality 100 '.$tmp_dir.'/'.$file_name.'[0] -flatten -fuzz 1%  '.$tmp_dir.'/'.$image_name);

                $file_location = str_replace(Storage::path(''), '/storage', $tmp_dir).'/'.$file_name;
                $image_location = str_replace(Storage::path(''), '/storage', $tmp_dir).'/'.$image_name;

                $details = [
                    'document_id' => $file_id,
                    'file_type' => $file_type,
                    'file_name' => $file_name,
                    'file_name_display' => $file_name_display,
                    'file_location' => $file_location,
                    'image_location' => $image_location,
                    'template_id' => $doc_template_id,
                    'data_upload_id' => $data_upload_id,
                    'signed' => $signed
                ];

                $docs_to_display[] = $details;
            }
        }

        $templates = EsignTemplates::where('user_id', auth() -> user() -> id) -> with(['signers']) -> get();

        return view('/esign/esign_add_documents', compact('address', 'from_upload', 'Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type', 'User_ID', 'Agent_ID', 'document_ids', 'documents', 'docs_to_display', 'templates'));
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

        foreach ($files as $file) {
            $ext = $file -> getClientOriginalExtension();
            $file_name = $file -> getClientOriginalName();

            $file_name_remove_numbers = preg_replace('/[0-9-_\s\.]+\.'.$ext.'/', '.'.$ext, $file_name);
            $file_name_remove_numbers = preg_replace('/^[0-9-_\s\.]+/', '', $file_name_remove_numbers);
            $file_name_no_ext = str_replace('.'.$ext, '', $file_name_remove_numbers);
            $clean_file_name = sanitize($file_name_no_ext);
            $file_name_display = $clean_file_name.'.'.$ext;
            $new_file_name = date('YmdHis').'_'.$file_name_display;

            $tmp_dir = Storage::path('tmp');

            // convert to pdf if image
            if ($ext != 'pdf') {
                $file_name_display = $clean_file_name.'.pdf';
                $new_file_name = date('YmdHis').'_'.$file_name_display;
                $convert_to_pdf = exec('convert -quality 100 -density 300 -page a4 '.$file.' '.$tmp_dir.'/'.$new_file_name, $output, $return);
            } else {
                move_uploaded_file($file, $tmp_dir.'/'.$new_file_name);
            }

            $new_image_name = str_replace('.pdf', '.jpg', $new_file_name);

            exec('convert -flatten -density 200 -quality 80 '.$tmp_dir.'/'.$new_file_name.'[0]  '.$tmp_dir.'/'.$new_image_name);

            $file_location = str_replace(Storage::path(''), '/storage', $tmp_dir).'/'.$new_file_name;
            $image_location = str_replace(Storage::path(''), '/storage', $tmp_dir).'/'.$new_image_name;

            $details = [
                'file_name' => $file_name_display,
                'file_location' => $file_location,
                'image_location' => $image_location,
            ];

            $docs_to_display[] = $details;
        }

        return compact('docs_to_display');
    }

    public function esign_create_envelope(Request $request) {

		$files = json_decode($request -> file_data, true);

        $from_upload = $request -> from_upload;

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $User_ID = $request -> User_ID ?? 0;
        $Agent_ID = $request -> Agent_ID ?? 0;
        $transaction_type = $request -> transaction_type ?? null;
        $document_id = $files[0]['upload_id'] ?? 0;

        $envelope_id = 0;
        $docs_added = 'no';

        // Create envelope
        $envelope = new EsignEnvelopes();

        $envelope -> Listing_ID = $Listing_ID;
        $envelope -> Contract_ID = $Contract_ID;
        $envelope -> Referral_ID = $Referral_ID;
        $envelope -> User_ID = $User_ID;
        $envelope -> Agent_ID = $Agent_ID;
        $envelope -> transaction_type = $transaction_type;
        $envelope -> save();
        $envelope_id = $envelope -> id;

        // Add documents and images to envelope and add files to storage
        $added_signers = [];
        foreach ($files as $file) {

            $applied_template_id = $file['template_applied_id'];

            if ($docs_added == 'no') {
                $transaction_document_id = $file['document_id'] ?? 0;
                // add doc
                $add_esign_doc = new EsignDocuments();
                $add_esign_doc -> envelope_id = $envelope_id;
                $add_esign_doc -> transaction_document_id = $transaction_document_id;
                $add_esign_doc -> template_applied_id = $applied_template_id;
                $add_esign_doc -> file_name = preg_replace('/[_]*[0-9]{14}[_]*/', '', $file['file_name']); // remove YmdHis from file name
                $add_esign_doc -> save();
                $add_esign_document_id = $add_esign_doc -> id;
            }

            // if template applied add fields and signers
            if ($applied_template_id > 0) {

                $template = EsignTemplates::with(['signers', 'fields', 'images']) -> find($applied_template_id);

                $signers = $template -> signers;
                $fields = $template -> fields;
                $images = $template -> images;

                // get signer names from members
                $members = null;
                if ($transaction_type) {

                    if ($transaction_type == 'listing') {

                        $members = Members::where('Listing_ID', $Listing_ID) -> with(['member_type']) -> get();

                    } elseif ($transaction_type == 'contract') {

                        if ($Listing_ID > 0) {
                            $members = Members::where('Listing_ID', $Listing_ID) -> with(['member_type']) -> get();
                        } else {
                            $members = Members::where('Contract_ID', $Contract_ID) -> with(['member_type']) -> get();
                        }

                    }
                }

                if ($members) {
                    $seller_order = 'One';
                    $buyer_order = 'One';

                    foreach ($members as $member) {
                        $member_role = $member -> member_type -> resource_name;

                        if ($member_role == 'Seller') {
                            $member -> member_role = 'Seller '.$seller_order;
                            if ($seller_order == 'One') {
                                $seller_order = 'Two';
                            }
                        } elseif ($member_role == 'Buyer') {
                            $member -> member_role = 'Buyer '.$buyer_order;
                            if ($buyer_order == 'One') {
                                $buyer_order = 'Two';
                            }
                        } else {
                            $member -> member_role = $member_role;
                        }
                    }
                }

                if ($signers) {

                    foreach ($signers as $signer) {

                        if (! in_array($signer -> template_role, $added_signers)) {

                            $added_signers[] = $signer -> signer_role;
                            $role = $signer -> signer_role;

                            $new_signer = new EsignSigners();
                            $new_signer -> envelope_id = $envelope_id;

                            if ($members) {
                                foreach ($members as $member) {
                                    if ($member -> member_role == $role) {
                                        $new_signer -> signer_name = $member -> first_name.' '.$member -> last_name;
                                        $new_signer -> signer_email = $member -> email;
                                        $new_signer -> signer_role = preg_replace('/(One|Two)/', '', $role);
                                        $new_signer -> template_role = $role;
                                        $new_signer -> save();
                                    }
                                }
                            } else {
                                $new_signer -> save();
                            }

                        }

                    }

                }

                if ($docs_added == 'no') {

                    foreach ($fields as $field) {

                        $field_signer = $field -> signer_role;

                        if ($field_signer) {
                            $new_signer = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> where('template_role', $field_signer) -> pluck('id');
                        }

                        if (count($new_signer) == 1) {
                            $new_field = new EsignFields();
                            $new_field -> envelope_id = $envelope_id;
                            $new_field -> document_id = $add_esign_document_id;
                            $new_field -> signer_id = $new_signer[0] ?? 0;
                            $new_field -> field_id = $field -> field_id;
                            $new_field -> field_type = $field -> field_type;
                            $new_field -> field_value = $field -> field_value;
                            $new_field -> required = $field -> required;
                            $new_field -> page = $field -> page;
                            $new_field -> left_perc = $field -> left_perc;
                            $new_field -> top_perc = $field -> top_perc;
                            $new_field -> height_perc = $field -> height_perc;
                            $new_field -> width_perc = $field -> width_perc;
                            $new_field -> save();
                        }
                    }

                }

            }

            if ($docs_added == 'no') {

                // create directories for doc and images
                $document_folder = 'esign/'.$envelope_id.'/'.$add_esign_document_id;

                $doc_to_location = Storage::path($document_folder);
                if (! is_dir($doc_to_location)) {
                    Storage::makeDirectory($document_folder);
                }

                $image_to_location = Storage::path($document_folder.'/images');
                if (! is_dir($image_to_location)) {
                    Storage::makeDirectory($document_folder.'/images');
                }

                if ($file['document_id'] > 0) {

                    // transfer files and images from transactions docs
                    $doc = TransactionDocuments::where('id', $file['document_id']) -> with(['images_converted']) -> first();
                    // copy document
                    $doc_from_location = Storage::path(str_replace('/storage/', '', $doc -> file_location_converted));
                    exec('cp '.$doc_from_location.' '.$doc_to_location);

                    $doc_dimensions = get_width_height($doc_from_location);
                    $doc_width = $doc_dimensions['width'];
                    $doc_height = $doc_dimensions['height'];

                    // get file location
                    $file_name = basename($doc_from_location);
                    $add_esign_doc_file_location = '/storage/'.$document_folder.'/'.$file_name;

                    // page count
                    $pages_total = exec('pdftk '.$doc_from_location.' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

                    // update file location
                    $add_esign_doc -> file_location = $add_esign_doc_file_location;
                    $add_esign_doc -> pages_total = $pages_total;
                    $add_esign_doc -> width = $doc_width;
                    $add_esign_doc -> height = $doc_height;
                    $add_esign_doc -> save();

                    // get images for doc
                    $images = $doc -> images_converted;

                    foreach ($images as $image) {

                        // copy images
                        $image_from_location = Storage::path(str_replace('/storage/', '', $image -> file_location));
                        exec('cp '.$image_from_location.' '.$image_to_location);

                        $doc_dimensions = get_width_height($image_from_location);
                        $doc_width = $doc_dimensions['width'];
                        $doc_height = $doc_dimensions['height'];

                        // get file name
                        $file_name = basename($image_from_location);
                        $add_esign_image_file_location = '/storage/'.$document_folder.'/images/'.$file_name;

                        $add_esign_image = new EsignDocumentsImages();
                        $add_esign_image -> image_location = $add_esign_image_file_location;
                        $add_esign_image -> envelope_id = $envelope_id;
                        $add_esign_image -> document_id = $transaction_document_id;
                        $add_esign_image -> page_number = $image -> page_number;
                        $add_esign_image -> width = $doc_width;
                        $add_esign_image -> height = $doc_height;
                        $add_esign_image -> save();
                    }
                } else {

                    // add files from tmp and create images

                    // copy document
                    $doc_from_location = Storage::path(str_replace('/storage/', '', $file['file_location']));
                    exec('cp '.$doc_from_location.' '.$doc_to_location);

                    $doc_dimensions = get_width_height($doc_from_location);
                    $doc_width = $doc_dimensions['width'];
                    $doc_height = $doc_dimensions['height'];

                    // get file location
                    $file_name = basename($doc_from_location);
                    $add_esign_doc_file_location = '/storage/'.$document_folder.'/'.$file_name;

                    // update location
                    $add_esign_doc -> file_location = $add_esign_doc_file_location;
                    $add_esign_doc -> width = $doc_width;
                    $add_esign_doc -> height = $doc_height;
                    $add_esign_doc -> save();

                    $input_file = $doc_from_location;
                    $output_images = $image_to_location.'/page_%02d.jpg';

                    // add individual images to images directory
                    // $create_images = exec('convert -density 300 -gaussian-blur 0.05 -quality 80% -resize 1200 '.$input_file.' -background white -alpha remove -strip '.$output_images, $output, $return);
                    $create_images = exec('convert -density 300 -quality 100 '.$input_file.' -background white -alpha remove -strip '.$output_images, $output, $return);

                    // get all image files images_storage_path to use as file location
                    $images_public_path = '/storage/'.$document_folder.'/images';
                    $saved_images_directory = Storage::files($document_folder.'/images');

                    foreach ($saved_images_directory as $saved_image) {

                        // get just filename
                        $images_file_name = basename($saved_image);
                        $add_esign_image_file_location = $images_public_path.'/'.$images_file_name;
                        $page_number = preg_match('/page_([0-9]+)\.jpg/', $images_file_name, $matches);
                        $match = $matches[1];
                        if (substr($match, 0, 1 == 0)) {
                            $match = substr($match, 1);
                        }
                        $page_number = count($matches) > 1 ? $match + 1 : 1;

                        $doc_location = Storage::path($document_folder.'/images/'.$images_file_name);
                        $doc_dimensions = get_width_height($doc_location);
                        $doc_width = $doc_dimensions['width'];
                        $doc_height = $doc_dimensions['height'];

                        // add images to database
                        $add_esign_image = new EsignDocumentsImages();
                        $add_esign_image -> image_location = $add_esign_image_file_location;
                        $add_esign_image -> envelope_id = $envelope_id;
                        $add_esign_image -> document_id = $add_esign_document_id;
                        $add_esign_image -> page_number = $page_number;
                        $add_esign_image -> width = $doc_width;
                        $add_esign_image -> height = $doc_height;
                        $add_esign_image -> save();
                    }
                }
            }
        }

        return compact('envelope_id');
    }

    public function esign_add_signers(Request $request) {

        $envelope_id = $request -> envelope_id;

        $envelope = EsignEnvelopes::find($envelope_id);
        $transaction_type = '';
        $Listing_ID = '';
        $Contract_ID = '';
        if ($envelope) {
            $transaction_type = $envelope -> transaction_type;
            $Listing_ID = $envelope -> Listing_ID;
            $Contract_ID = $envelope -> Contract_ID;
        }

        $property = Listings::GetPropertyDetails($transaction_type, [$Listing_ID, $Contract_ID, '0']);
        $address = null;

        if($property != 'not found') {
            $address = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
        }

        if ($transaction_type == 'listing') {
            $members = Members::where('Listing_ID', $Listing_ID) -> with(['member_type:resource_id,resource_name']) -> get();
        } elseif ($transaction_type == 'contract') {
            if ($Listing_ID > 0) {
                $members = Members::where('Listing_ID', $Listing_ID) -> with(['member_type:resource_id,resource_name']) -> get();
            } else {
                $members = Members::where('Contract_ID', $Contract_ID) -> with(['member_type:resource_id,resource_name']) -> get();
            }
        } else {
            $members = null;
        }

        if ($members) {

            $members -> map(function ($member) {

                $member_type = $member -> member_type -> resource_name;
                $member['member_type'] = $member_type;

                $order = [
                    'Seller' => 1,
                    'Buyer' => 2,
                    'Listing Agent' => 3,
                    'Buyer Agent' => 4,
                ][$member_type] ?? 15;

                $member['order'] = $order;

                return $member;

            });

            $members = $members -> sortBy('order');

        }

        $signers = [];
        $recipients = [];
        if ($members) {
            $signers = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> orderBy('signer_order') -> get();
            $recipients = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'yes') -> orderBy('signer_order') -> get();
        }


        return view('/esign/esign_add_signers', compact('envelope_id', 'envelope', 'address', 'members', 'signers', 'recipients'));
    }

    public function esign_add_signers_to_envelope(Request $request) {

        $envelope_id = $request -> envelope_id;
        $signers = json_decode($request -> signers_data);
        $recipients = json_decode($request -> recipients_data);

        $signer_ids = [0];
        foreach ($signers as $signer) {
            $signer_ids[] = $signer -> id ?? 0;
        }
        foreach ($recipients as $recipient) {
            $signer_ids[] = $recipient -> id ?? 0;
        }

        // remove signers and fields from signers not in new list of signers. Keep existing signers
        // if they were all just removed than all fields would need to be recreated again
        // delete signers removed
        $delete_signers = EsignSigners::where('envelope_id', $envelope_id) -> whereNotIn('id', $signer_ids) -> delete();
        // delete fields from signers removed
        $delete_fields = EsignFields::where('envelope_id', $envelope_id) -> whereNotIn('signer_id', $signer_ids) -> delete();

        $seller_template_role = '';
        $buyer_template_role = '';
        $other_template_role = '';
        foreach ($signers as $signer) {
            if ($signer -> role == 'Seller') {
                if ($seller_template_role == '') {
                    $seller_template_role = 'Seller One';
                } elseif ($seller_template_role == 'Seller One') {
                    $seller_template_role = 'Seller Two';
                }
                $template_role = $seller_template_role;
            } elseif ($signer -> role == 'Buyer') {
                if ($buyer_template_role == '') {
                    $buyer_template_role = 'Buyer One';
                } elseif ($buyer_template_role == 'Buyer One') {
                    $buyer_template_role = 'Buyer Two';
                }
                $template_role = $buyer_template_role;
            } elseif ($signer -> role == 'Other') {
                if ($other_template_role == '') {
                    $other_template_role = 'Signer One';
                } elseif ($other_template_role == 'Signer One') {
                    $other_template_role = 'Signer Two';
                } elseif ($other_template_role == 'Signer Two') {
                    $other_template_role = 'Signer Three';
                } elseif ($other_template_role == 'Signer Three') {
                    $other_template_role = 'Signer Four';
                } elseif ($other_template_role == 'Signer Four') {
                    $other_template_role = 'Signer Five';
                }
                $template_role = $other_template_role;
            } else {
                $template_role = $signer -> role;
            }

            // add or update signer

            // check if signer added from template. if so will need to add signer id to fields
            $template_signer = null;
            $template_signer = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> where('template_role', $template_role) -> first();
            if ($template_signer) {
                $add_signer = EsignSigners::find($template_signer -> id);
            } else {
                if ($signer -> id > 0) {
                    $add_signer = EsignSigners::find($signer -> id);
                } else {
                    $add_signer = new EsignSigners();
                }
            }

            $add_signer -> envelope_id = $envelope_id;
            $add_signer -> signer_name = $signer -> name;
            $add_signer -> signer_email = $signer -> email;
            $add_signer -> signer_role = $signer -> role;
            $add_signer -> template_role = $template_role;
            $add_signer -> signer_order = $signer -> order;
            $add_signer -> recipient_only = 'no';
            $add_signer -> save();
        }

        $template_role = '';
        foreach ($recipients as $recipient) {
            if ($recipient -> role == 'Buyer' || $recipient -> role == 'Seller') {
                if ($template_role == '') {
                    $template_role = $recipient -> role.' One';
                } elseif ($template_role == $recipient -> role.' One') {
                    $template_role = $recipient -> role.' Two';
                }
            } elseif ($signer -> role == 'Other') {
                if ($template_role == '') {
                    $template_role = 'Recipient One';
                } elseif ($template_role == 'Recipient One') {
                    $template_role = 'Recipient Two';
                } elseif ($template_role == 'Recipient Two') {
                    $template_role = 'Recipient Three';
                } elseif ($template_role == 'Recipient Three') {
                    $template_role = 'Recipient Four';
                } elseif ($template_role == 'Recipient Four') {
                    $template_role = 'Recipient Five';
                }
            } else {
                $template_role = $recipient -> role;
            }

            // check if signer added from template. if so will need to add signer id to fields
            $template_recipient = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'yes') -> where('template_role', $template_role) -> first();
            if ($template_recipient) {
                $add_recipient = EsignSigners::find($template_recipient -> id);
            } else {
                if ($recipient -> id > 0) {
                    $add_recipient = EsignSigners::find($recipient -> id);
                } else {
                    $add_recipient = new EsignSigners();
                }
            }
            $add_recipient -> envelope_id = $envelope_id;
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

        $envelope_id = $request -> envelope_id;
        $is_draft = $request -> is_draft;

        $draft_name = '';
        $property_address = null;

        $documents = [];
        $signers = [];
        $signers_options = [];

        $error = null;

        $envelope = EsignEnvelopes::find($envelope_id);
        $draft_name = $envelope -> draft_name ?? null;

        if ($envelope -> status != 'not_sent') {
            $error = 'sent';

            return view('/esign/esign_add_fields', compact('error', 'is_draft', 'envelope_id', 'draft_name', 'property_address', 'documents', 'signers', 'signers_options'));
        }

        if ($envelope -> transaction_type != '') {
            $property = Listings::GetPropertyDetails($envelope -> transaction_type, [$envelope -> Listing_ID, $envelope -> Contract_ID, $envelope -> Referral_ID]);
            $property_address = $property -> FullStreetAddress.' '.$property -> City.', '.$property -> StateOrProvince.' '.$property -> PostalCode;
        }

        $documents = EsignDocuments::where('envelope_id', $envelope_id) -> with(['images', 'fields']) -> get();

        $signers = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> orderBy('signer_order') -> get();

        $signers_options = [];

        foreach ($signers as $signer) {
            $signer_name = $signer -> signer_name;
            $signers_options[] = '<option class="signer-select-option" value="'.$signer_name.'" data-role="'.$signer -> signer_role.'" data-name="'.$signer_name.'" data-signer-id="'.$signer -> id.'">'.$signer_name.' - '.$signer -> signer_role.'</option>';

        }

        return view('/esign/esign_add_fields', compact('is_draft', 'envelope_id', 'draft_name', 'property_address', 'documents', 'signers', 'signers_options', 'error'));
    }

    public function esign_send_for_signatures(Request $request) {

		// DB::beginTransaction();
        // try {

            $envelope_id = $request -> envelope_id ?? 0;
            $document_ids = explode(',', $request -> document_ids);

            $subject = $request -> subject;
            $message = $request -> message;
            $user_name = auth() -> user() -> name;
            $user_email = auth() -> user() -> email;

            $fields = json_decode($request -> fields, true);
            $fields = collect($fields) -> map(function ($fields) {
                return (object) $fields;
            });

            $delete_current_fields = EsignFields::where('envelope_id', $envelope_id) -> delete();

            // add fields to db
            foreach ($fields as $field) {
                $add_field = new EsignFields();
                $add_field -> envelope_id = $envelope_id;
                $add_field -> document_id = $field -> document_id;
                $add_field -> signer_id = $field -> signer_id ?? 0;
                $add_field -> field_id = $field -> field_id;
                $add_field -> field_type = $field -> field_type;
                $add_field -> field_value = $field -> field_value ?? null;
                $add_field -> required = $field -> required;
                $add_field -> page = $field -> page;
                $add_field -> left_perc = $field -> left_perc;
                $add_field -> top_perc = $field -> top_perc;
                $add_field -> height_perc = $field -> height_perc;
                $add_field -> width_perc = $field -> width_perc;
                $add_field -> save();
            }

            if ($request -> is_draft == 'yes') {
                return response() -> json(['status' => 'draft saved']);
            }

            //SendForSignatures::dispatch($envelope_id, $document_ids, $subject, $message, $fields, $user_name, $user_email);




            // update esign_envelope table with subject and message
            $envelope = EsignEnvelopes::find($envelope_id);

            $envelope -> subject = $subject;
            $envelope -> message = $message;
            $envelope -> is_draft = 'no';
            $envelope -> draft_name = '';

            $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));

            $file_to_sign = new document();
            if(config('app.env') == 'local') {
                $file_to_sign -> setSandbox(true);
            } else if(config('app.env') == 'production') {
                $file_to_sign -> setSandbox(false);
            }
            $file_to_sign -> setTitle($subject);
            $file_to_sign -> setMessage($message);
            $file_to_sign -> setRequireAllSigners(true);
            $file_to_sign -> setUseSignerOrder(true);
            $file_to_sign -> setCustomRequesterName($user_name);
            $file_to_sign -> setCustomRequesterEmail($user_email);

            $days = config('app.env') == 'local' ? 'PT1200S' : 'P7D';
            $date = new \DateTime();
            $date -> add(new \DateInterval($days));
            $file_to_sign -> setExpires($date);

            // Add Signers
            $signers = EsignSigners::where('envelope_id', $envelope_id) -> where('recipient_only', 'no') -> orderBy('signer_order') -> get();

            foreach ($signers as $signer) {
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

            foreach ($recipients as $recipient) {
                $add_recipient = new Recipient();
                $add_recipient -> setName($recipient -> signer_name);
                $add_recipient -> setEmail($recipient -> signer_email);
                //$add_recipient -> setLanguage('en');
                $file_to_sign -> appendRecipient($add_recipient);
            }

            $file_index = 0;

            foreach ($document_ids as $document_id) {
                $document = EsignDocuments::where('id', $document_id) -> first();

                if (count($fields -> where('document_id', $document_id)) > 0) {

                    //Add a File to the Document
                    $file = new File();
                    $file -> setName($document -> file_name);
                    $file -> setFilePath(Storage::path(str_replace('/storage/', '', $document -> file_location)));
                    $file_to_sign -> appendFile($file);

                    $c = 0;

                    $width = $document -> width;
                    $height = $document -> height;

                    $doc_sizes = get_width_height(Storage::path(str_replace('/storage', '', $document -> file_location)));
                    $actual_width = $doc_sizes['width'];
                    $actual_height = $doc_sizes['height'];

                    foreach ($fields -> where('document_id', $document_id) as $field) {

                        // increase move down and right
                        $x = ($field -> left_perc / 100) * $width;
                        $y = ($field -> top_perc / 100) * $height;
                        $w = ($field -> width_perc / 100) * $width;
                        $h = ($field -> height_perc / 100) * $height;

                        if ($field -> field_type == 'signature') {
                            $document_field = new SignatureField();
                            $document_field -> setSigner($field -> signer_id);
                            $document_field -> setRequired($field -> required);
                            $document_field -> setY($y + 8);
                        } elseif ($field -> field_type == 'initials') {
                            $document_field = new InitialsField();
                            $document_field -> setSigner($field -> signer_id);
                            $document_field -> setRequired($field -> required);
                            $document_field -> setY($y + 3);
                        } elseif ($field -> field_type == 'date') {
                            $document_field = new DateSignedField();
                            $document_field -> setSigner($field -> signer_id);
                            $document_field -> setTextSize(10);
                            //$dateSignedField -> setTextStyle('U');
                            $document_field -> setY($y + 2);
                        } elseif ($field -> field_type == 'name') {
                            $document_field = new TextField();
                            $document_field -> setSigner($field -> signer_id);
                            $document_field -> setValue($field -> signer);
                            $document_field -> setTextSize(9);
                            $document_field -> setY($y + 3);
                            $document_field -> setRequired(0);
                        } elseif ($field -> field_type == 'text') {
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
            $envelope -> document_hash = $hash;
            $envelope -> save();

            $link = null;
            if($envelope -> transaction_type != '') {

                $id = [
                    'listing' => $envelope -> Listing_ID,
                    'contract' => $envelope -> Contract_ID,
                    'referral' => $envelope -> Referral_ID
                ][$envelope -> transaction_type];

                $link = '/agents/doc_management/transactions/transaction_details/'.$id.'/'.$envelope -> transaction_type;

            }

            //DB::commit();

            return response() -> json([
                'status' => 'sent',
                'link' => $link
            ]);

        // } catch (Throwable $e) {

        //     DB::rollBack();

        //     return response() -> json([
        //         'status' => 'error',
        //         'message' => $e -> getMessage()
        //     ]);

        // }


    }


    // public function esign_template_add_documents(Request $request) {

    //     return view('/esign/esign_template_add_documents');

    // }

    public function esign_template_upload(Request $request) {

		$file = $request -> file('esign_file_upload');

        if ($file) {

            $page_width = get_width_height($file)['width'];
            $page_height = get_width_height($file)['height'];

            $file_name_orig = $file -> getClientOriginalName();
            $filename = $file_name_orig;

            $ext = $file -> getClientOriginalExtension();
            $file_name_remove_numbers = preg_replace('/[0-9-_\s\.]+\.'.$ext.'/', '.'.$ext, $filename);
            $file_name_no_ext = str_replace('.'.$ext, '', $file_name_remove_numbers);
            $clean_filename = sanitize($file_name_no_ext);
            $new_filename = $clean_filename.'.'.$ext;


            $storage_path = Storage::path('');
            $storage_dir = 'doc_management/uploads/'.$file_id;

            if (! Storage::put($storage_dir.'/'.$new_filename, file_get_contents($file))) {
                $fail = json_encode(['fail' => 'File Not Uploaded']);
                return $fail;
            }

            $file_in = Storage::path($storage_dir.'/'.$new_filename);
            $file_out = Storage::path($storage_dir.'/temp_'.$new_filename);
            exec('pdftk '.$file_in.' output '.$file_out.' flatten compress');
            exec('rm '.$file_in.' && mv '.$file_out.' '.$file_in);

            $storage_full_path = $storage_path.'/doc_management/uploads/'.$file_id;
            chmod($storage_full_path.'/'.$new_filename, 0775);

            // update directory path in database
            $storage_public_path = '/storage/doc_management/uploads/'.$file_id;
            $upload -> file_location = $storage_public_path.'/'.$new_filename;
            $upload -> save();

            // create directories
            $storage_dir_pages = $storage_dir.'/pages';
            Storage::makeDirectory($storage_dir_pages);
            $storage_dir_images = $storage_dir.'/images';
            Storage::makeDirectory($storage_dir_images);


            // esign template directories
            $template_dir = 'esign_templates/system/'.$file_id;
            Storage::makeDirectory($template_dir);
            Storage::makeDirectory($template_dir.'/images');

            // copy file to esign templates
            Storage::copy($storage_dir.'/'.$new_filename, $template_dir.'/'.$new_filename);
            // add to esign_templates
            $template = new EsignTemplates();
            $template -> template_type = 'system';
            $template -> system_upload_id = $file_id;
            $template -> template_name = $file_name_display;
            $template -> file_name = $new_filename;
            $template -> file_location = '/storage/'.$template_dir.'/'.$new_filename;
            $template -> save();
            $template_id = $template -> id;

            $upload -> template_id = $template_id;
            $upload -> save();


            // split pdf into pages and images
            $input_file = $storage_full_path.'/'.$new_filename;
            $output_files = $storage_path.'/'.$storage_dir_pages.'/page_%02d.pdf';
            $new_image_name = str_replace($ext, 'jpg', $new_filename);
            //$output_images = $storage_path.'/'.$storage_dir_images.'/'.$new_image_name;
            $output_images = $storage_path.'/'.$storage_dir_images.'/page_%02d.jpg';

            // add individual pages to pages directory
            $create_pages = exec('pdftk '.$input_file.' burst output '.$output_files.' flatten compress', $output, $return);

            // remove data file
            exec('rm '.$storage_path.'/'.$storage_dir_pages.'/doc_data.txt');

            // add individual images to images directory
            $create_images = exec('convert -density 200 -quality 80% -resize 1200 '.$input_file.'  -compress JPEG -background white -alpha remove -strip '.$output_images, $output, $return);

            // get all image files images_storage_path to use as file location
            $saved_images_directory = Storage::files($storage_dir.'/images');
            $images_public_path = $storage_public_path.'/images';

            foreach ($saved_images_directory as $saved_image) {
                // get just filename
                $images_file_name = basename($saved_image);
                /* $page_number = preg_match('/([0-9]+)\.jpg/', $images_file_name, $matches);
                $page_number = count($matches) > 1 ? $matches[1] + 1 : 1; */
                $page_number = preg_match('/page_([0-9]+)\.jpg/', $images_file_name, $matches);
                $match = $matches[1];
                if (substr($match, 0, 1 == 0)) {
                    $match = substr($match, 1);
                }
                $page_number = count($matches) > 1 ? $match + 1 : 1;
                // add images to database
                $upload = new UploadImages();
                $upload -> file_id = $file_id;
                $upload -> file_name = $images_file_name;
                $upload -> file_location = $images_public_path.'/'.$images_file_name;
                $upload -> pages_total = $pages_total;
                $upload -> page_number = $page_number;
                $upload -> save();

                $template_image_location = $template_dir.'/images/'.$images_file_name;
                // copy image to templates directory
                Storage::copy($storage_dir.'/images/'.$images_file_name, $template_image_location);

                $page_width = get_width_height(Storage::path($template_image_location))['width'];
                $page_height = get_width_height(Storage::path($template_image_location))['height'];

                // add to template images
                $template_image = new EsignTemplatesDocumentImages();
                $template_image -> template_id = $template_id;
                $template_image -> file_location = '/storage/'.$template_image_location;
                $template_image -> page_number = $page_number;
                $template_image -> width = $page_width;
                $template_image -> height = $page_height;
                $template_image -> save();

            }

            $saved_pages_directory = Storage::files($storage_dir.'/pages');
            $pages_public_path = $storage_public_path.'/pages';

            $page_number = 1;
            foreach ($saved_pages_directory as $saved_page) {
                $pages_file_name = basename($saved_page);
                $upload = new UploadPages();
                $upload -> file_id = $file_id;
                $upload -> file_name = $pages_file_name;
                $upload -> file_location = $pages_public_path.'/'.$pages_file_name;
                $upload -> pages_total = $pages_total;
                $upload -> page_number = $page_number;
                $upload -> save();

                $page_number += 1;
            }
            $success = json_encode(['success' => true]);

            return $success;
        }

    }

    public function esign_template_add_documents_and_signers(Request $request) {

        $template_type = $request -> template_type;
        $template_id = $request -> template_id ? $request -> template_id : null;

        $template = null;
        $signers = null;
        if($template_id) {
            $template = EsignTemplates::with(['signers']) -> find($template_id);
            $signers = $template -> signers -> pluck('signer_role') -> toArray();
        }

        $signer_options = ResourceItems::where('resource_type', 'signer_option') -> orderBy('resource_order') -> get();

        return view('/esign/esign_template_add_documents_and_signers', compact('template_type', 'template', 'template_id', 'signers', 'signer_options'));

    }

    public function esign_template_save_add_signers(Request $request) {

        $template_type = $request -> template_type;
        $template_id =  $request -> template_id ? $request -> template_id : null;
        $file = $request -> file('template_upload');
        $signers = json_decode($request -> signers, true);

        if(!$template_id) {

            $template = new EsignTemplates();
            $template -> template_type = $template_type;
            $template -> save();
            $template_id = $template -> id;

        }

        if($file) {

            $ext = $file -> getClientOriginalExtension();
            $file_name = $file -> getClientOriginalName();

            $file_name_no_ext = str_replace('.'.$ext, '', $file_name);
            $clean_file_name = sanitize($file_name_no_ext);
            $new_file_name = $clean_file_name.'_'.time().'.'.$ext;

            Storage::makeDirectory('esign_templates/'.$template_type.'/'.$template_id);
            Storage::makeDirectory('esign_templates/'.$template_type.'/'.$template_id.'/images');
            $template_dir = 'esign_templates/'.$template_type.'/'.$template_id;
            $template_path = Storage::path($template_dir);

            // convert to pdf if image
            if ($ext != 'pdf') {
                $convert_to_pdf = exec('convert -quality 100 -density 300 -page a4 '.$file.' '.$template_path.'/'.$new_file_name, $output, $return);
            } else {
                move_uploaded_file($file, $template_path.'/'.$new_file_name);
            }

            $output_images = $template_path.'/images/page_%02d.jpg';

            $create_images = exec('convert -density 200 -quality 80% -resize 1200 '.$template_path.'/'.$new_file_name.' -compress JPEG -background white -alpha remove -strip '.$output_images, $output, $return);

            $images = Storage::files($template_dir.'/images');

            foreach($images as $image) {

                $image_location = Storage::path($image);

                $image_name = basename($image);
                $page_number = preg_match('/page_([0-9]+)\.jpg/', $image_name, $matches);
                $match = $matches[1];
                if (substr($match, 0, 1 == 0)) {
                    $match = substr($match, 1);
                }
                $page_number = count($matches) > 1 ? $match + 1 : 1;

                $page_width = get_width_height($image_location)['width'];
                $page_height = get_width_height($image_location)['height'];

                // add to template images
                $template_image = new EsignTemplatesDocumentImages();
                $template_image -> template_id = $template_id;
                $template_image -> file_location = '/storage/'.$image;
                $template_image -> page_number = $page_number;
                $template_image -> width = $page_width;
                $template_image -> height = $page_height;
                $template_image -> save();

            }

        }

        $delete_current_signers = EsignTemplatesSigners::where('template_id', $template_id) -> delete();

        foreach($signers as $signer) {
            $new_signer = new EsignTemplatesSigners();
            $new_signer -> template_id = $template_id;
            $new_signer -> signer_order = $signer['order'];
            $new_signer -> signer_role = $signer['role'];
            $new_signer -> save();
        }

        return response() -> json(['template_id' => $template_id]);

    }

    public function esign_template_add_fields(Request $request) {

        $template_type = $request -> template_type;
        $template_id = $request -> template_id;
        $signer_options_template = [];

        //$envelopes = EsignEnvelopes::where('template_id', $template_id) -> get();
        $template = EsignTemplates::with(['images', 'signers', 'fields']) -> find($template_id);
        $images = $template -> images;
        $signers = $template -> signers;
        $fields = $template -> fields;

        $template_name = $template -> template_name;

        foreach ($signers as $signer) {
            $signer_options_template[] = '<option class="signer-select-option" value="'.$signer -> signer_role.'" data-signer-role="'.$signer -> signer_role.'">'.$signer -> signer_role.'</option>';
        }

        return view('/esign/esign_template_add_fields', compact('template_id','template_name', 'images', 'signers', 'fields', 'signer_options_template'));
    }

    public function save_template(Request $request) {

        $template_id = $request -> template_id;
        $template = EsignTemplates::find($template_id);
        $template_type = $template -> template_type;

        $fields = json_decode($request -> fields, true);
        $fields = collect($fields) -> map(function ($fields) {
            return (object) $fields;
        });

        $delete_current_fields = EsignTemplatesFields::where('template_id', $template_id) -> delete();

        // add fields to db
        foreach ($fields as $field) {
            $add_field = new EsignTemplatesFields();
            $add_field -> template_id = $template_id;
            $add_field -> signer_role = $field -> signer_role;
            $add_field -> field_id = $field -> field_id;
            $add_field -> field_type = $field -> field_type;
            $add_field -> field_value = $field -> field_value ?? null;
            $add_field -> required = $field -> required;
            $add_field -> page = $field -> page;
            $add_field -> left_perc = $field -> left_perc;
            $add_field -> top_perc = $field -> top_perc;
            $add_field -> height_perc = $field -> height_perc;
            $add_field -> width_perc = $field -> width_perc;
            $add_field -> save();
        }

        return response() -> json([
            'status' => 'success',
            'template_type' => $template_type
        ]);

    }


    ////////////////////// Get Docs and Signers //////////////////////////////////

    ////////////////////// Callbacks //////////////////////////////////

    public function get_envelope(Request $request) {

        $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));

        // Get all documents
        // $documents = $client -> getAllDocuments();
        // echo sizeof($documents) . ' documents found';

        // Get a single document
        $document = $client -> getDocumentByHash('576b55b7f84a477a8160a551fe348818');

        // Update envelope
        if($document -> getIsCompleted()) {
            $status = 'completed';
        }


        // update callbacks
        $status = [
            'document_created' => 'Sent',
            'document_viewed' => 'Viewed',
            'document_sent' => 'Sent',
            'document_signed' => 'Signed',
            'document_declined' => 'Declined',
            'document_forwarded' => 'Forwarded',
            'signer_removed' => 'Signer Removed',
            'signer_bounced' => 'Signer Bounced',
            'document_completed' => 'Completed',
            'document_expired' => 'Expired',
            'document_cancelled' => 'Canceled',
        ][$event_type] ?? null;


        /* // download said document
        $client -> downloadFinalDocumentToPath($document, getcwd() . '/final.pdf', true);
        $client -> downloadRawDocumentToPath($document, getcwd() .'/raw.pdf');

        // send a reminder for a signer
        $signers = $document -> getSigners();
        foreach ($signers as $signer) {
            $client -> sendReminderForDocument($document, $signer);
        } */

    }

    public function esign_callback(Request $request) {

		if($request -> event_time) {

            $response_content = $request -> getContent();
            $json = json_decode($request, true);

            $event_time = $request -> event_time;
            $event_type = $request -> event_type;
            $event_hash = $request -> event_hash;
            $related_document_hash = $request -> meta['related_document_hash'];
            $related_user_id = $request -> meta['related_user_id'];

            $status = [
                'document_created' => 'Sent',
                'document_viewed' => 'Viewed',
                'document_sent' => 'Sent',
                'document_signed' => 'Signed',
                'document_declined' => 'Declined',
                'document_forwarded' => 'Forwarded',
                'signer_removed' => 'Signer Removed',
                'signer_bounced' => 'Signer Bounced',
                'document_completed' => 'Completed',
                'document_expired' => 'Expired',
                'document_cancelled' => 'Canceled',
                'email_validation_waived' => 'Email Validation Waived',
            ][$event_type];

            //if(hash_hmac('sha256', $event_time . $event_type, config('esign.key')) == $request -> event_hash) {

            $esign_callback = new EsignCallbacks();
            $esign_callback -> response_content = $response_content;
            $esign_callback -> event_time = $event_time;
            $esign_callback -> event_type = $event_type;
            $esign_callback -> event_hash = $event_hash;
            $esign_callback -> related_document_hash = $related_document_hash;
            $esign_callback -> related_user_id = $related_user_id;

            if ($request -> signer) {

                $signer_id = $request -> signer['id'];
                $signer_name = $request -> signer['name'];
                $signer_email = $request -> signer['email'];
                $signer_role = $request -> signer['role'];
                $signer_order = $request -> signer['order'];

                $esign_callback -> signer_id = $signer_id;
                $esign_callback -> signer_name = $signer_name;
                $esign_callback -> signer_email = $signer_email;
                $esign_callback -> signer_role = $signer_role;
                $esign_callback -> signer_order = $signer_order;

                $signer = EsignSigners::find($signer_id) -> update([
                    'signer_status' => $event_type
                ]);

            }

            $esign_callback -> save();

            // } else {
            //     echo 'error '.hash_hmac('sha256', $event_time . $event_type, config('esign.key')).' = '.$request -> event_hash;
            // }



            if ($status) {

                $envelope = EsignEnvelopes::where('document_hash', $related_document_hash) -> with('documents') -> first();

                $client = new Client(config('esign.eversign.key'), config('esign.eversign.business_id'));
                $document = $client -> getDocumentByHash($related_document_hash);

                $envelope -> status = $status;

                if ($status == 'Completed') {

                    $subject = sanitize($envelope -> subject).'_'.time();
                    $path = Storage::path('esign/'.$envelope -> id);
                    $file_location = $path.'/'.$subject.'.pdf';
                    $public_link = '/storage/esign/'.$envelope -> id.'/'.$subject.'.pdf';

                    $client -> downloadFinalDocumentToPath($document, $file_location, true);

                    $envelope -> file_location = $public_link;

                    // update transaction docs with completed link and set status to signed
                    $documents = $envelope -> documents -> where('transaction_document_id', '>', '0');

                    if ($documents) {

                        $start = 1;
                        foreach ($documents as $document) {

                            $pages = $document -> pages_total;
                            $end = $start + $pages - 1;

                            $file_name = $document -> file_name.'_'.time().'.pdf';

                            $split_doc_location = $path.'/'.$file_name;
                            $split_doc_location_public = '/storage/esign/'.$envelope -> id.'/'.$file_name;

                            exec('pdftk '.$file_location.' cat '.$start.'-'.$end.' output '.$split_doc_location);

                            $update_transaction_docs = TransactionDocuments::where('id', $document -> transaction_document_id) -> update(['signed' => 'yes', 'file_location_converted' => $split_doc_location_public]);

                            $start = $end + 1;
                        }

                    }

                }

                $envelope -> save();

            }

            return true;

        } else {

            return 'who dis new phone';

        }

    }

    public function oauth_callback(Request $request) {
    }
}
