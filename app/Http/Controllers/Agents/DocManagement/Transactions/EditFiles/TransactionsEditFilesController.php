<?php

namespace App\Http\Controllers\Agents\DocManagement\Transactions\EditFiles;

use App\Http\Controllers\Controller;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;
use App\Models\DocManagement\Transactions\Documents\InProcess;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\EditFiles\UserFields;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use mikehaertl\wkhtmlto\Pdf;

class TransactionsEditFilesController extends Controller {

    public function convert_to_pdf(Request $request) {

        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $file_id = $request -> file_id;
        $file_type = $request -> file_type;


        // add to in_process table
        $in_process = new InProcess();
        $in_process -> document_id = $file_id;
        $in_process -> save();

        $path = [
            'listing' => 'listings/'.$Listing_ID,
            'contract' => 'contracts/'.$Contract_ID,
            'referral' => 'referrals/'.$Referral_ID,
        ][$transaction_type];

        $upload_dir = 'doc_management/transactions/'.$path.'/'.$file_id.'_'.$file_type;

        Storage::disk('public') -> makeDirectory($upload_dir.'/combined/');
        Storage::disk('public') -> makeDirectory($upload_dir.'/layers/');
        $full_path_dir = Storage::disk('public') -> path($upload_dir);
        $pdf_output_dir = Storage::disk('public') -> path($upload_dir.'/combined/');

        // get file name to use for the final converted file
        $file = glob($full_path_dir.'/converted/*pdf');

        $filename = basename($file[0]);

        // create or clear out directories if they already exist
        $clean_dir = new Filesystem;
        $clean_dir -> cleanDirectory('storage/'.$upload_dir.'/layers');
        $clean_dir -> cleanDirectory('storage/'.$upload_dir.'/combined');
        //$clean_dir -> cleanDirectory('storage/'.$upload_dir.'/converted');
        exec('cp -p '.$file[0].' '.$full_path_dir.'/converted/backup.pdf');
        //exec('rm '.$file[0]);

        // pdf options - more added below depending on page size
        $options = array(
            //'binary' => '/usr/bin/xvfb-run -- /usr/bin/wkhtmltopdf',
            'no-outline',
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
            'encoding' => 'UTF-8',
            'dpi' => 96,
            'disable-smart-shrinking',
            'tmpDir' => '/var/www/tmp'
        );

        // loop through all pages
        for ($c = 1; $c <= $request['page_count']; $c++) {

            $page_number = $c;

            if (strlen($c) == 1) {
                $page_number = '0'.$c;
            }

            // set layer and combined directories
            $layer_pdf = $full_path_dir.'/pages/page_'.$page_number.'.pdf';
            $layer_top = $full_path_dir.'/layers/layer_top_'.$page_number.'.pdf';
            $layer_top_temp = $full_path_dir.'/layers/temp_layer_top_'.$page_number.'.pdf';
            $layer_bottom = $full_path_dir.'/layers/layer_bottom_'.$page_number.'.pdf';

            $combined_top = $pdf_output_dir.'/combined_top_'.$page_number.'.pdf';
            $combined = $pdf_output_dir.'/combined_'.$page_number.'.pdf';

            $page_width = get_width_height($layer_pdf)['width'];
            $page_height = get_width_height($layer_pdf)['height'];

            // if not standard 612 by 792 get width and height and convert to mm
            if($page_width != 612 || $page_height != 792) {

                $page_width = $page_width * 0.2745833333;
                $page_height = $page_height * 0.2745833333;

                $options['page-width'] = $page_width.'mm';
                $options['page-height'] = $page_height.'mm';

            } else {

                $options['page-size'] = 'Letter';

            }

            $html = "
            <style>
            @import 'https://fonts.googleapis.com/css2?family=Roboto+Condensed&display=swap';
            </style>
            ";

            $html_top = '';
            $html_bottom = '';

            if($request['page_html_top_'.$c]) {

                $html_top = $html.$request['page_html_top_'.$c];

                $pdf = new Pdf($options);
                $pdf -> addPage($html_top);

                if (!$pdf -> saveAs($layer_top_temp)) {
                    $error = $pdf -> getError();
                    dd($error);
                }

            }

            if($request['page_html_bottom_'.$c]) {

                $html_bottom = $html.$request['page_html_bottom_'.$c];

                $pdf = new Pdf($options);
                $pdf -> addPage($html_bottom);

                if (!$pdf -> saveAs($layer_bottom)) {
                    $error = $pdf -> getError();
                    dd($error);
                }

            }

            if($html_top != '') {
                // remove background and resize top layer
                exec('convert -quality 100 -density 300 '.$layer_top_temp.' -size '.$page_width.'x'.$page_height.' -transparent white -compress Zip '.$layer_top);
                // merge top pdf layer with top layer
                exec('pdftk '.$layer_top.' background '.$layer_pdf.' output '.$combined_top.' compress');
                // if not bottom move combined_top to combined
                if($html_bottom == '') {
                    exec('mv '.$combined_top.' '.$combined);
                }
                // remove top layer file
                exec('rm '.$layer_top);
            }

            if($html_bottom != '') {
                // if html_top add it to pdf-top layer
                if($html_top != '') {
                    exec('pdftk '.$combined_top.' background '.$layer_bottom.' output '.$combined.' compress');
                    exec('rm '.$combined_top);
                    exec('rm '.$layer_bottom);
                } else {
                    exec('pdftk '.$layer_pdf.' background '.$layer_bottom.' output '.$combined.' compress');
                    exec('rm '.$layer_pdf);
                    exec('rm '.$layer_bottom);
                }
            }

            // if no fields to add to page
            if($html_top == '' && $html_bottom == '') {
                exec('cp -p '.$layer_pdf.' '.$combined);
            }

        }

        // merge all from combined and add final to converted - named $filename
        exec('pdftk '.$full_path_dir.'/combined/*pdf cat output '.$full_path_dir.'/converted/'.$filename.' compress');

        if (file_exists($full_path_dir.'/converted/'.$filename)) {
            exec('rm '.$full_path_dir.'/converted/backup.pdf');
        }

        $checklist_item_docs_model = new TransactionChecklistItemsDocs();
        $image_filename = str_replace('.pdf', '.jpg', $filename);
        $source = $full_path_dir.'/converted/'.$filename;
        $destination = $full_path_dir.'/converted_images';
        $checklist_item_docs_model -> convert_doc_to_images($source, $destination, $image_filename, $file_id);

        // remove from in_process
        $remove_in_process = InProcess::where('document_id', $file_id) -> delete();

    }

    public function file_view(Request $request) {

        $document_id = $request -> document_id;
        $document = TransactionDocuments::whereId($document_id) -> first();
        $file_type = $document -> file_type;
        $file_id = $document -> file_id;
        $Listing_ID = $document -> Listing_ID ?? 0;
        $Contract_ID = $document -> Contract_ID ?? 0;
        $Referral_ID = $document -> Referral_ID ?? 0;
        $transaction_type = $document -> transaction_type;
        $Agent_ID = $document -> Agent_ID;

        $file = TransactionUpload::where('file_id', $file_id) -> first();
        $file_name = $file -> file_name_display;

        return view('/agents/doc_management/transactions/edit_files/file', compact('Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type', 'Agent_ID', 'file', 'file_name', 'file_id', 'document_id', 'file_type'));

    }

    public function get_edit_file_docs(Request $request) {

        $document_id = $request -> document_id;
        $document = TransactionDocuments::whereId($document_id) -> first();
        $file_type = $document -> file_type;
        $file_id = $document -> file_id;
        $Listing_ID = $document -> Listing_ID ?? 0;
        $Contract_ID = $document -> Contract_ID ?? 0;
        $Referral_ID = $document -> Referral_ID ?? 0;
        $transaction_type = $document -> transaction_type;
        $Agent_ID = $document -> Agent_ID;

        $file = TransactionUpload::where('file_id', $file_id) -> first();
        $file_name = $file -> file_name_display;
        $images = TransactionUploadImages::where('file_id', $file_id) -> orderBy('page_number') -> get();

        $user_fields = UserFields::where('file_id', $file_id) -> with('user_field_inputs') -> with('common_field') -> orderBy('id') -> get();

        return view('/agents/doc_management/transactions/edit_files/get_edit_file_docs_html', compact('Listing_ID', 'Contract_ID', 'Referral_ID', 'transaction_type', 'Agent_ID', 'file', 'file_name', 'images', 'user_fields', 'file_id', 'document_id', 'file_type'));

    }

    public function rotate_document(Request $request) {

        $file_id = $request -> file_id;
        $file_type = $request -> file_type;
        $Listing_ID = $request -> Listing_ID ?? 0;
        $Contract_ID = $request -> Contract_ID ?? 0;
        $Referral_ID = $request -> Referral_ID ?? 0;
        $transaction_type = $request -> transaction_type;
        $degrees = $request -> degrees;

        $path = [
            'listing' => 'listings/'.$Listing_ID,
            'contract' => 'contracts/'.$Contract_ID,
            'referral' => 'referrals/'.$Referral_ID,
        ][$transaction_type];

        $files = Storage::disk('public') -> allFiles('doc_management/transactions/'.$path.'/'.$file_id.'_'.$file_type);

        $doc_root = Storage::disk('public') -> path('');

        foreach ($files as $file) {
            $file = $doc_root . $file;
            exec('mogrify -density 300 -quality 100 -rotate "'.$degrees.'" /'.$file.' 2>&1', $output);
        }

        return response() -> json(['status' => 'success']);

    }

    public function save_edit_system_inputs(Request $request) {

        // update system input values
        $inputs = $request -> inputs;
        $inputs = json_decode($inputs, true);

        if (count($inputs) > 0) {

            foreach ($inputs as $input) {

                $updated_input = UserFieldsInputs::find($input['id']);

                if($updated_input) {

                    $updated_input -> update(['input_value' => $input['value']]);

                    //update all common fields on other docs
                    if ($updated_input -> input_db_column != '') {
                        // update all with same transaction_type, Listing_ID, Contract_ID, Referral_ID and input_db_column
                        $common_inputs = UserFieldsInputs::where([
                            'transaction_type' => $updated_input -> transaction_type,
                            'Listing_ID' => $updated_input -> Listing_ID ?? 0,
                            'Contract_ID' => $updated_input -> Contract_ID ?? 0,
                            'Referral_ID' => $updated_input -> Referral_ID ?? 0,
                            'input_db_column' => $updated_input -> input_db_column,
                        ])
                        -> update([
                            'input_value' => $updated_input -> input_value,
                        ]);

                    }

                }

            }

        }

        return response() -> json(['status' => 'success']);

/*
$inputs = UserFieldsInputs::where('file_type', 'user')
-> where('input_db_column', '!=', '')
-> where('Agent_ID', $Agent_ID)
-> where('file_id', $file_id)
-> get();

foreach($inputs as $input) {
dump($input);
} */

        /* // delete all field input values for this file
    $file_id = $request[0]['file_id'];
    $file_type = $request[0]['file_type'];
    $Listing_ID = $request[0]['Listing_ID'] ?? 0;
    $Contract_ID = $request[0]['Contract_ID'] ?? 0;
    $Referral_ID = $request[0]['Referral_ID'] ?? 0;
    $transaction_type = $request[0]['transaction_type'];
    $Agent_ID = $request[0]['Agent_ID'];

    if($transaction_type == 'listing') {
    $column = 'Listing_ID';
    $id = $Listing_ID;
    } else if($transaction_type == 'contract') {
    $column = 'Contract_ID';
    $id = $Contract_ID;
    } else if($transaction_type == 'referral') {
    $column = 'Referral_ID';
    $id = $Referral_ID;
    }

    // ****************** UserFieldsValues is no more
    $delete_filled_fields = UserFieldsValues::where('Agent_ID', $Agent_ID)
    -> where('file_id', $file_id)
    -> where($column, $id)
    -> delete();

    $fields = json_decode($request -> getContent(), true);

    // ****************** UserFieldsValues is no more
    foreach($fields as $field) {
    $filled_fields = new UserFieldsValues();
    $filled_fields -> file_id = $file_id;
    $filled_fields -> file_type = $file_type;
    $filled_fields -> common_name = $field['common_name'];
    $filled_fields -> Agent_ID = $Agent_ID;
    $filled_fields -> Listing_ID = $Listing_ID;
    $filled_fields -> Contract_ID = $Contract_ID;
    $filled_fields -> Referral_ID = $Referral_ID;
    $filled_fields -> transaction_type = $transaction_type;
    $filled_fields -> input_id = $field['input_id'];
    $filled_fields -> input_value = $field['input_value'];
    $filled_fields -> save();

    if($field['common_name'] != '') {
    // update all common fields
    // ****************** UserFieldsValues is no more
    $common_fields = UserFieldsValues::where('Agent_ID', $Agent_ID)
    -> where($column, $id)
    -> where('common_name', $field['common_name']) -> first();

    $common_fields -> input_value = $field['input_value'];
    $common_fields -> save();
    }
    } */

    }

    public function save_edit_user_fields(Request $request) {

        // add and update user input values
        $user_fields = $request -> user_fields;
        $user_fields = json_decode($user_fields, true);

        $file_id = $request -> file_id;
        $Agent_ID = $request -> Agent_ID;
        $Listing_ID = $request -> Listing_ID;
        $Contract_ID = $request -> Contract_ID;
        $transaction_type = $request -> transaction_type;

        // delete all current user fields for this file
        $delete_user_fields = UserFields::where('field_created_by', 'user') -> where('Agent_ID', $Agent_ID) -> where('file_id', $file_id) -> delete();
        $delete_user_inputs = UserFieldsInputs::where('file_type', 'user') -> where('Agent_ID', $Agent_ID) -> where('file_id', $file_id) -> delete();

        if (count($user_fields) > 0) {

            foreach ($user_fields as $field) {

                $new_field = new UserFields();

                $new_field -> file_id = $file_id;
                $new_field -> create_field_id = $field['create_field_id'];
                $new_field -> group_id = $field['create_field_id'];
                $new_field -> page = $field['page'];
                $new_field -> field_category = $field['field_type'];
                $new_field -> field_type = $field['field_type'];
                $new_field -> field_created_by = 'user'; // system, user
                $new_field -> top_perc = $field['yp'];
                $new_field -> left_perc = $field['xp'];
                $new_field -> width_perc = $field['wp'];
                $new_field -> height_perc = $field['hp'];
                $new_field -> Agent_ID = $Agent_ID;
                $new_field -> Listing_ID = $Listing_ID;
                $new_field -> Contract_ID = $Contract_ID;
                $new_field -> transaction_type = $transaction_type;

                $new_field -> save();

                $new_field_id = $new_field -> id;

                // add inputs if user_text
                if ($field['field_type'] == 'user_text') {

                    $new_field_input = new UserFieldsInputs();

                    $new_field_input -> file_id = $field['file_id'];
                    $new_field_input -> group_id = $field['create_field_id'];
                    $new_field_input -> file_type = 'user';
                    $new_field_input -> field_type = $field['field_type'];
                    $new_field_input -> input_value = $field['input_data']['value'];
                    $new_field_input -> transaction_field_id = $new_field_id;
                    $new_field_input -> Agent_ID = $new_field -> Agent_ID;
                    $new_field_input -> Listing_ID = $new_field -> Listing_ID;
                    $new_field_input -> Contract_ID = $new_field -> Contract_ID;
                    $new_field_input -> transaction_type = $new_field -> transaction_type;

                    $new_field_input -> save();

                }

            }

        }

        return response() -> json(['status' => 'success']);

    }

}
