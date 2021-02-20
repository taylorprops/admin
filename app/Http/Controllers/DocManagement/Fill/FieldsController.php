<?php

namespace App\Http\Controllers\DocManagement\Fill;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocManagement\Create\Fields\CommonFields;
use App\Models\DocManagement\Create\Fields\CommonFieldsGroups;
use App\Models\DocManagement\Create\Fields\Fields;
//use App\Models\DocManagement\Create\Fields\FieldTypes;
use App\Models\DocManagement\Create\Fields\FieldInputs;
//use App\Models\DocManagement\Create\FilledFields\FilledFields;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Create\Upload\UploadImages;
use App\Models\DocManagement\Create\Upload\UploadPages;
use App\Models\Resources\LocationData;
use Illuminate\Support\Facades\Storage;
use mikehaertl\wkhtmlto\Pdf;
use Illuminate\Filesystem\Filesystem;

class FieldsController extends Controller
{

    public function delete_page(Request $request) {
        $file_id = $request -> file_id;
        $page = $request -> page;

        $upload = Upload::where('file_id', $file_id) -> first();
        $images = UploadImages::where('file_id', $file_id) -> where('page_number', $page) -> first();
        $pages = UploadPages::where('file_id', $file_id) -> where('page_number', $page) -> first();

        $files_remove = [$images -> file_location, $pages -> file_location];
        foreach($files_remove as $file_remove) {
            Storage::disk('public') -> delete(str_replace('/storage/', '', $file_remove));
        }

        $images -> delete();
        $pages -> delete();

        $file = Storage::disk('public') -> path(str_replace('/storage/', '', $upload -> file_location));
        $file_location = Storage::disk('public') -> path(str_replace('/storage/', '', $upload -> file_location));
        $temp_location = Storage::disk('public') -> path('tmp/'.$upload -> file_name);

        exec('pdftk '.$file.' cat 1-r2 output '.$temp_location.' && mv '.$temp_location.' '.$file_location);

    }

    public function get_edit_properties_html(Request $request) {

        $file_id = $request -> file_id;
        $field_id = $request -> field_id;
        $field_category = $request -> field_category;
        $group_id = $request -> group_id;

        $common_name = '';
        $custom_name = '';
        $common_field_type = '';
        $common_field_id = '';
        $common_field_sub_group_id = '';
        $number_type = '';

        $field = Fields::where('field_id', $field_id) -> first();

        if($field) {
            if($field -> field_name_type == 'common') {
                $common_name = $field -> field_name_display;
            } else {
                $custom_name = $field -> field_name_display;
            }
            $common_field_type = $field -> field_type;
            $common_field_id = $field -> common_field_id;
            $common_field_sub_group_id = $field -> field_sub_group_id;
            $number_type = $field -> number_type;
        }

        $label = $field_category == 'radio' ? 'Radio Button Group Name' : 'Custom Field Name';

        $common_fields_groups = CommonFieldsGroups::with('sub_groups')
            -> orderBy('group_order')
            -> get();

        $common_fields = CommonFields::orderBy('field_order') -> get();

        $file = Upload::whereFileId($file_id) -> first();
        $published = $file -> published;


        return view('/doc_management/create/fields/edit_properties_html', compact('field_id', 'field_category', 'group_id', 'common_name', 'custom_name', 'common_field_type', 'common_field_sub_group_id', 'common_field_id', 'number_type', 'label', 'common_fields_groups', 'common_fields', 'published'));

        /* $field_id = $request -> field_id;
        $field_type = $request -> field_type;
        $group_id = $request -> group_id;
        $field_number_type = '';
        $field_textline_type = '';
        $field_address_type = '';
        $field_name_type = '';
        $common_name = '';
        $custom_name = '';
        $label = $field_type == 'radio' ? 'Radio Button Group Name' : 'Custom Name';

        $file = Upload::whereFileId($request -> file_id) -> first();
        $published = $file -> published;
        $common_fields = CommonFields::getCommonFields();
        $field_inputs = FieldInputs::where('file_id', $request -> file_id) -> orderBy('id') -> get();


        return view('/doc_management/create/fields/edit_properties_html', compact('field_id', 'field_type', 'group_id', 'field_number_type', 'field_textline_type', 'field_address_type', 'field_name_type', 'common_name', 'custom_name', 'label', 'common_fields', 'field_inputs', 'published')); */
    }

    public function get_custom_names(Request $request) {
        $val = $request -> val;
        $custom_names = Fields::select('field_name_display') -> where('field_name_display', 'like', '%'.$val.'%') -> where('field_name_type', 'custom') -> groupBy('field_name_display') -> orderBy('field_name_display') -> get();

        return compact('custom_names');
    }

    /* public function get_common_fields(Request $request) {
        return CommonFields::getCommonFields();
    } */

    public function add_fields(Request $request) {

        $file = Upload::whereFileId($request -> file_id) -> first();
        $file_name = $file -> file_name_display;
        $published = $file -> published;
        $images = UploadImages::where('file_id', $request -> file_id) -> orderBy('page_number') -> get();
        $fields = Fields::where('file_id', $request -> file_id) -> orderBy('id') -> get();
        //$common_fields = CommonFields::getCommonFields();
        //$field_types = FieldTypes::select('field_type') -> get();
        $field_inputs = FieldInputs::where('file_id', $request -> file_id) -> orderBy('id') -> get();
        return view('/doc_management/create/fields/add_fields', compact('file', 'file_name', 'published', 'images', 'fields', /* 'common_fields', *//*  'field_types', */ 'field_inputs'));

    }

    public function save_add_fields(Request $request) {

        $fields = json_decode($request['data'], true);

        $file_id = $fields[0]['file_id'];

        $published = Upload::where('file_id', $file_id) -> first();

        if($published -> published == 'no') {

            if(isset($file_id)) {

                // delete all fields for this document
                $delete_docs = Fields::where('file_id', $file_id) -> delete();

                foreach($fields as $field) {

                    $custom_name = $field['custom_field_name'] ?? null;
                    $common_name = $field['common_field_name'] ?? null;

                    $field_name = $field['common_field_id'] > 0 ? $common_name : $custom_name;
                    $field_name_type = $field['common_field_id'] > 0 ? 'common' : 'custom';

                    $new_field = new Fields;

                    $new_field -> file_id = $field['file_id'];
                    $new_field -> common_field_id = $field['common_field_id'];
                    $new_field -> field_id = $field['field_id'];
                    $new_field -> group_id = $field['group_id'];
                    $new_field -> page = $field['page'];
                    $new_field -> field_category = $field['field_category']; // textline, date, number, checkbox, radio
                    $new_field -> field_type = $field['common_field_type']; // address, date, name, number, phone, text
                    $new_field -> field_name = trim(preg_replace('/\s/', '', $field_name));
                    $new_field -> field_name_display = $field_name;
                    $new_field -> field_name_type = $field_name_type; // common or custom
                    $new_field -> field_sub_group_id = $field['common_field_sub_group_id'];
                    $new_field -> number_type = $field['number_type']; // numeric or written
                    $new_field -> top_perc = $field['top_perc'];
                    $new_field -> left_perc = $field['left_perc'];
                    $new_field -> width_perc = $field['width_perc'];
                    $new_field -> height_perc = $field['height_perc'];

                    $new_field -> save();

                }

            }


            return true;

        } else {

            return response() -> json([
                'error' => 'published',
            ]);

        }

    }

    /* public function fillable_files(Request $request) {

        $files = Upload::select('file_name_orig', 'file_id') -> groupBy('file_id', 'file_name_orig') -> get();
        return view('/doc_management/fill/fillable_files', compact('files'));

    } */

    /* public function fill_fields(Request $request) {

        $file_id = $request -> file_id;
        $file = Upload::whereFileId($file_id) -> get();
        $images = UploadImages::whereFileId($request -> file_id) -> orderBy('page_number') -> get() -> toArray();
        $fields = Fields::where('file_id', $file_id) -> orderBy('id') -> get() -> toArray();
        $field_inputs = FieldInputs::where('file_id', $file_id) -> orderBy('id') -> get() -> toArray();
        $field_values = FilledFields::where('file_id', $file_id) -> get() -> toArray();
        // $states = LocationData::ActiveStates();
        //$file_id = $file_id;
        return view('/doc_management/fill/fill_fields', compact('file', 'images', 'fields', 'field_inputs', 'file_id', 'field_values'));

    } */

    /* public function save_fill_fields(Request $request) {

        // delete all field input values for this file
        $file_id = $request[0]['file_id'];
        $delete_filled_fields = FilledFields::where('file_id', $file_id) -> delete();

        $fields = json_decode($request -> getContent(), true);
        foreach($fields as $field) {
            $filled_fields = new FilledFields();
            $filled_fields -> file_id = $file_id;
            $filled_fields -> input_id = $field['input_id'];
            $filled_fields -> input_value = $field['input_value'];
            $filled_fields -> save();
        }


    } */

    public function save_pdf_client_side(Request $request) {
        if($request) {

            $file_id = $request['file_id'];

            $upload_dir = 'doc_management/uploads/' . $file_id;
            // create or clear out directories if they already exist
            $clean_dir = new Filesystem;
            if (!Storage::disk('public') -> exists($upload_dir . '/layers')) {
                Storage::disk('public') -> makeDirectory($upload_dir . '/layers');
            } else {
                $clean_dir -> cleanDirectory('storage/'.$upload_dir . '/layers');
            }
            if (!Storage::disk('public') -> exists($upload_dir . '/combined')) {
                Storage::disk('public') -> makeDirectory($upload_dir . '/combined');
            } else {
                $clean_dir -> cleanDirectory('storage/'.$upload_dir . '/combined');
            }

            $doc_root = base_path().'/public/';
            $full_path_dir = $doc_root . 'storage/'.$upload_dir;

            $pdf_output_dir = $doc_root . 'storage/'.$upload_dir.'/combined/';

            for($c = 1; $c <= $request['page_count']; $c++) {
                $options = array(
                    'binary' => '/usr/bin/xvfb-run -- /usr/bin/wkhtmltopdf',
                    'no-outline',
                    'margin-top'    => 0,
                    'margin-right'  => 0,
                    'margin-bottom' => 0,
                    'margin-left'   => 0,
                    //'disable-smart-shrinking',
                    'page-size' => 'Letter',
                    'encoding' => 'UTF-8',
                    'dpi' => 96,
                );

                $pdf = new Pdf($options);
                $pdf -> addPage($request['page_'.$c]);
                if (!$pdf -> saveAs($full_path_dir.'/layers/layer_'.$c.'.pdf')) {
                    $error = $pdf -> getError();
                }

                // merge layers from pages folder and layers folder and dump in combined folder
                $page_number = $c;
                if(strlen($c) == 1) {
                    $page_number = '0'.$c;
                }
                $layer1 = $full_path_dir . '/pages/page_'.$page_number.'.pdf';
                $layer2 = $full_path_dir . '/layers/layer_'.$c.'.pdf';
                exec('convert -quality 100 -density 300 '.$layer2.' -transparent '.$layer2);
                exec('pdftk '.$layer2.' background '.$layer1.' output '.$pdf_output_dir.'/'.date('YmdHis').'_combined_'.$c.'.pdf');

            }

        }
    }




}

