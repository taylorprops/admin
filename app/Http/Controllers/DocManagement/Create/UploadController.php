<?php

namespace App\Http\Controllers\DocManagement\Create;

use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Esign\EsignTemplates;
use App\Models\Resources\LocationData;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\DocManagement\Create\Fields\Fields;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\Esign\EsignTemplatesDocumentImages;
use App\Models\DocManagement\Checklists\Checklists;
use App\Models\DocManagement\Resources\ResourceItems;
use App\Models\DocManagement\Create\Fields\FieldInputs;
use App\Models\DocManagement\Create\Upload\UploadPages;
use App\Models\DocManagement\Checklists\ChecklistsItems;
use App\Models\DocManagement\Create\Upload\UploadImages;
use App\Models\DocManagement\Create\FilledFields\FilledFields;

class UploadController extends Controller
{
    public function add_form_get_checklist_items(Request $request) {

		$file_id = $request -> file_id;
        // get form details just to display
        $uploaded_file = Upload::where('file_id', $file_id) -> first();
        $checklist_id = $request -> checklist_id;
        $checklist = Checklists::where('id', $checklist_id) -> first();
        $checklist_type = $checklist -> checklist_type;
        $checklist_items = new ChecklistsItems();
        $items = ChecklistsItems::where('checklist_id', $checklist_id) -> get();
        $upload = new Upload();

        $checklist_types = ['listing', 'both'];
        if ($checklist_type == 'contract') {
            $checklist_types = ['contract', 'both'];
        } elseif ($checklist_type == 'referral') {
            $checklist_types = ['referral'];
        }
        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', $checklist_types) -> orderBy('resource_order') -> get();

        return view('/doc_management/create/upload/get_checklist_items_html', compact('file_id', 'uploaded_file', 'checklist_id', 'checklist_items', 'items', 'upload', 'checklist_type', 'checklist_groups'));
    }

    public function activate_upload(Request $request) {

		$upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> first();
        $upload -> active = $request -> active;
        $upload -> save();
    }

    public function delete_upload(Request $request) {

		$upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> delete();
        $upload = Fields::where('file_id', $upload_id) -> delete();
        $upload = FieldInputs::where('file_id', $upload_id) -> delete();
        $upload = FilledFields::where('file_id', $upload_id) -> delete();
        $upload_dir = base_path().'/storage/app/public/doc_management/uploads/'.$upload_id;
        exec('rm -r '.$upload_dir);
    }

    public function duplicate_upload(Request $request) {

		$upload_id = $request -> upload_id;

        // insert copy to get new id to use to create folder. update file location after
        $upload = Upload::find($upload_id);
        $upload_copy = $upload -> replicate();
        $upload_copy -> save();

        if ($upload -> file_location != '') {
            $file_id = $upload_copy -> file_id;
            $uploads_path = 'doc_management/uploads';

            File::copyDirectory(Storage::path($uploads_path.'/'.$upload_id), Storage::path($uploads_path.'/'.$file_id));
            //$uploads_path = base_path().'/storage/app/public/doc_management/uploads';
            //exec('cp -r '.$uploads_path.'/'.$upload_id.' '.$uploads_path.'/'.$file_id);

            $copy_path = str_replace('/'.$upload_id.'/', '/'.$file_id.'/', $upload -> file_location);
            // update file location
            $upload_copy -> file_location = $copy_path;
            $upload_copy -> published = 'no';
            $upload_copy -> save();

            // copy db data for admin.docs_fields, admin.docs_transaction_fields_inputs_values, admin.docs_fields_inputs
            $data_sets = [UploadImages::where('file_id', $upload_id) -> get(), UploadPages::where('file_id', $upload_id) -> get()];

            foreach ($data_sets as $data_set) {
                foreach ($data_set as $row) {
                    $copy = $row -> replicate();
                    $copy -> file_id = $file_id;
                    $path = str_replace('/'.$upload_id.'/', '/'.$file_id.'/', $row -> file_location);
                    $copy -> file_location = $path;
                    $copy -> save();
                }
            }

            $data_sets = [Fields::where('file_id', $upload_id) -> get(), FieldInputs::where('file_id', $upload_id) -> get()];

            foreach ($data_sets as $data_set) {
                foreach ($data_set as $row) {
                    $copy = $row -> replicate();
                    $copy -> file_id = $file_id;
                    $copy -> save();
                }
            }
        } else {
            $upload_copy -> published = 'no';
            $upload_copy -> save();
        }
    }

    public function get_add_to_checklists_details(Request $request) {

		$file_id = $request -> form_id;
        // get form details just to display
        $uploaded_file = Upload::where('file_id', $file_id) -> first();
        // to run functions from ResourceItems
        $resource_items = new ResourceItems();
        // checklists to add to
        $checklists = Checklists::where('active', 'yes') -> where('checklist_type', $request -> checklist_type) -> orderBy('checklist_state', 'ASC')
            -> orderBy('checklist_location_id', 'ASC')
            -> orderBy('checklist_represent', 'DESC')
            -> orderBy('checklist_sale_rent', 'DESC')
            -> orderBy('checklist_property_type_id', 'ASC')
            -> get();
        // checklist items to view in checklists
        $checklists_items = new ChecklistsItems();
        // options for required and form_group
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> orderBy('resource_order') -> get();
        $checklist_groups = $resource_items -> where('resource_type', 'checklist_groups') -> whereIn('resource_form_group_type', [$request -> checklist_type, 'both']) -> orderBy('resource_order') -> get();
        $checklist_locations = $resource_items -> where('resource_type', 'checklist_locations') -> orderBy('resource_order') -> get();
        $property_types = $resource_items -> where('resource_type', 'checklist_property_types') -> orderBy('resource_order') -> get();
        $property_sub_types = $resource_items -> where('resource_type', 'checklist_property_sub_types') -> orderBy('resource_order') -> get();
        // for upload functions
        $upload = new Upload();

        // get required and form group id for add to checklists
        $checklist_item_details = $checklists_items -> where('checklist_form_id', $file_id) -> first();
        $checklist_item_group_id = $checklist_item_details -> checklist_item_group_id ?? null;
        $checklist_item_required = $checklist_item_details -> checklist_item_required ?? null;

        $states = LocationData::ActiveStates();

        return view('/doc_management/create/upload/get_add_to_checklists_details_html', compact('file_id', 'uploaded_file', 'resource_items', 'checklists', 'checklists_items', 'form_groups', 'checklist_groups', 'checklist_locations', 'property_types', 'property_sub_types', 'upload', 'checklist_item_required', 'checklist_item_group_id', 'states'));
    }

    public function get_form_group_files(Request $request) {

		$form_group_id = $request -> form_group_id;
        $state = $request -> state;
        $order = $request -> order ?? null;

        $order_by = 'file_name_display';
        $dir = 'ASC';
        if ($order == 'added') {
            $order_by = 'created_at';
            $dir = 'DESC';
        }
        $resource_items = new ResourceItems();

        $files = Upload::where('form_group_id', $form_group_id) -> withCount('fields') -> orderBy($order_by, $dir) -> get();

        $files_count = count($files);

        $checklists = new ChecklistsItems();

        return view('/doc_management/create/upload/get_form_group_files_html', compact('files', 'files_count', 'form_group_id', 'state', 'resource_items', 'checklists'));
    }

    public function get_manage_upload_details(Request $request) {

		$file_id = $request -> form_id;
        // get form details just to display
        $uploaded_file = Upload::where('file_id', $file_id) -> first();
        // all forms to select replacement from
        $uploads = Upload::where('form_group_id', $request -> form_group_id) -> where('published', 'yes') -> where('active', 'yes')
        -> whereNotIn('file_id', function ($query) {
            $query -> select('checklist_form_id')
                -> from('docs_checklists_items')
                -> groupBy('checklist_form_id');
        })
        -> orderBy('file_name_display', 'ASC') -> get();
        // checklists that form is located in to display
        $checklists = Checklists::whereIn('id', function ($query) use ($file_id) {
            $query -> select('checklist_id')
                -> from('docs_checklists_items')
                -> where('checklist_form_id', $file_id);
        })
            -> orderBy('checklist_state', 'ASC')
            -> orderBy('checklist_location_id', 'ASC')
            -> orderBy('checklist_type', 'DESC')
            -> orderBy('checklist_represent', 'DESC')
            -> orderBy('checklist_sale_rent', 'DESC')
            -> orderBy('checklist_property_type_id', 'ASC')
            -> get();

        // to run functions from ResourceItems
        $resource_items = new ResourceItems();

        return view('/doc_management/create/upload/get_manage_upload_details_html', compact('file_id', 'uploaded_file', 'uploads', 'checklists', 'resource_items'));
    }

    public function get_upload_details(Request $request) {

		$upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> first();

        return $upload;
    }

    public function get_uploaded_files(Request $request) {

		$form_group_id = $request -> form_group_id ?? null;
        //$files = Upload::groupBy('file_id', 'file_name_orig') -> get();
        $upload = new Upload();
        $files = $upload -> orderBy('file_name_display') -> get();
        $states = LocationData::ActiveStates();
        //dd(ResourceItems::getResourceName('11'));
        $resource_items = new ResourceItems();
        $resources = ResourceItems::orderBy('resource_order') -> get();
        $form_groups = $resource_items -> where('resource_type', 'form_groups') -> orderBy('resource_order') -> get();

        $checklist_types = ['listing', 'both'];

        $checklist_groups = ResourceItems::where('resource_type', 'checklist_groups') -> orderBy('resource_order') -> get();

        return view('/doc_management/create/upload/files', compact('upload', 'files', 'states', 'resources', 'form_group_id', 'resource_items', 'form_groups', 'checklist_groups'));
    }

    public function publish_upload(Request $request) {

		$upload_id = $request -> upload_id;
        $upload = Upload::where('file_id', $upload_id) -> first();
        $upload -> published = 'yes';
        $upload -> save();
    }

    public function remove_upload(Request $request) {

		$form_id = $request -> form_id;
        $checklist_items_model = new ChecklistsItems();

        $checklist_items = $checklist_items_model -> where('checklist_form_id', $form_id) -> get();

        $checklist_items -> each -> delete();
        // set checklist count column
        $update_count = $checklist_items -> map(function ($checklist_item) {
            $this -> updateChecklistCount($checklist_item -> checklist_id);
        });
    }

    public function replace_upload(Request $request) {

		$old_id = $request -> old_form_id;
        $new_id = $request -> new_form_id;

        ChecklistsItems::where('checklist_form_id', $old_id)
            -> update(['checklist_form_id' => $new_id]);
    }

    public function save_add_non_form(Request $request) {

		$file_name_display = $request -> no_form_file_name_display;
        $state = $request -> no_form_state;
        $helper_text = $request -> no_form_helper_text;
        $form_group_id = $request -> no_form_form_group_id;
        $form_categories = implode(',', $request -> no_form_form_categories);
        $form_tags = $request -> no_form_form_tags;
        $checklist_group_id = $request -> no_form_checklist_group_id;

        $upload = new Upload();
        $upload -> file_name_orig = $file_name_display;
        $upload -> file_name_display = $file_name_display;
        $upload -> state = $state;
        $upload -> pages_total = 0;
        $upload -> helper_text = $helper_text;
        $upload -> form_categories = $form_categories;
        $upload -> form_tags = $form_tags;
        $upload -> checklist_group_id = $checklist_group_id;
        $upload -> form_group_id = $form_group_id;
        $upload -> save();
    }

    public function save_add_to_checklists(Request $request) {

		$file_id = $request -> file_id;
        $checklists = json_decode($request -> checklists);
        $checklists = $checklists -> checklist;
        $required = $request -> required;

        $checklist_ids_keep = explode(',', $request -> checklist_ids_keep);

        // delete file from all checklists except those not changed
        $delete_from_checklists = ChecklistsItems::where('checklist_form_id', $file_id) -> whereNotIn('checklist_id', $checklist_ids_keep) -> delete();

        foreach ($checklists as $checklist) {
            if ($checklist -> checklist_id != '') {
                $checklist_order = $checklist -> checklist_order;
                $checklist_items = new ChecklistsItems();
                $checklist_items -> checklist_id = $checklist -> checklist_id;
                $checklist_items -> checklist_form_id = $file_id;
                $checklist_items -> checklist_item_required = $required;
                $checklist_items -> checklist_item_order = $checklist_order;
                $checklist_items -> checklist_item_group_id = $checklist -> checklist_group_id;
                $checklist_items -> save();

                $current_items = new ChecklistsItems();

                $set_order = $current_items -> where('checklist_id', $checklist -> checklist_id) -> where('checklist_item_order', '>=', $checklist_order) -> where('checklist_form_id', '!=', $file_id) -> update(['checklist_item_order' => DB::raw('checklist_item_order + 1')]);
                // update checklist order
                $current_items -> updateChecklistItemsOrder($checklist -> checklist_id);
                // set checklist count column
                $this -> updateChecklistCount($checklist -> checklist_id);
            }
        }
    }

    private function updateChecklistCount($checklist_id) {
        $checklist_item_count = ChecklistsItems::where('checklist_id', $checklist_id) -> count();
        $update_count = Checklists::where('id', $checklist_id) -> first();
        $update_count -> checklist_count = $checklist_item_count;
        $update_count -> save();
    }

    public function save_file_edit(Request $request) {

		$file_id = $request -> edit_file_id;
        $form_group_id = $request -> edit_form_group_id;
        $checklist_group_id = $request -> edit_checklist_group_id;
        $file_name_display = $request -> edit_file_name_display;
        $helper_text = $request -> edit_helper_text;
        $form_categories = implode(',', $request -> edit_form_categories);
        $form_tags = $request -> edit_form_tags;

        $upload = Upload::where('file_id', $file_id) -> first();

        if ($upload -> published == 'no') {
            $upload -> form_group_id = $form_group_id;
            $upload -> checklist_group_id = $checklist_group_id;
        }

        $upload -> file_name_display = $file_name_display;
        $upload -> helper_text = $helper_text;
        $upload -> form_categories = $form_categories;
        $upload -> form_tags = $form_tags;
        $upload -> save();
    }

    public function get_upload_text(Request $request) {

		$upload = $request -> file('file_upload');

        $new_file_name = str_replace('.pdf', '', $upload -> getClientOriginalName());
        $new_file_name_pdf = date('YmdHis').'_'.sanitize($new_file_name).'.pdf';
        $new_file_name_image = date('YmdHis').'_'.sanitize($new_file_name).'.png';

        // put original file
        Storage::put('tmp/'.$new_file_name_pdf, file_get_contents($upload));
        // remove images from pdf so easier to scan text
        exec('gs -o '.Storage::path('tmp/tmp_'.$new_file_name_pdf).' -sDEVICE=pdfwrite -dFILTERIMAGE '.$upload);
        // convert to image
        exec('convert '.Storage::path('tmp/tmp_'.$new_file_name_pdf).'[0] -density 200 -flatten -trim -quality 80% -background white '.Storage::path('tmp/'.$new_file_name_image));
        // scan text
        $text = (new TesseractOCR(Storage::path('tmp/'.$new_file_name_image)))
            -> allowlist(range('a', 'z'), range('A', 'Z'), '-_/\'/')
            -> run();
        // store results to text file so we can loop through the lines
        $temp_text_file = 'tmp/'.date('YmdHis').'.txt';
        Storage::put($temp_text_file, $text);
        // open saved text file with titles and get lines
        $fn = fopen(Storage::path($temp_text_file), 'r');
        $lines = [];
        while (! feof($fn)) {
            $lines[] = fgets($fn);
        }
        fclose($fn);

        $titles = [$upload -> getClientOriginalName()];
        foreach ($lines as $line) {
            // clean lines
            $line = trim(urldecode($line));
            $line = iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $line);

            // get words
            if (preg_match('/^[a-zA-Z-_\/\s]+/', $line, $matches)) {
                // remove non form names
                if (! preg_match('/(realtor|association|commission)/i', $matches[0])) {
                    // if more than one word in name
                    preg_match_all('/\S+/', $matches[0], $words);
                    //if(count($words[0]) > 1) {
                    $titles[] = ucwords(strtolower($matches[0]));
                    //}
                }
            }
        }

        $titles = array_slice($titles, 0, 10);

        $upload_location = '/storage/tmp/'.$new_file_name_pdf;

        return response() -> json([
            'upload_location' => $upload_location,
            'titles' => $titles,
        ]);
    }

    public function upload_file(Request $request) {

		$file = $request -> file('file_upload');

        if ($file) {
            $page_width = get_width_height($file)['width'];
            $page_height = get_width_height($file)['height'];

            // if not standard 612 by 792 get width and height and convert to mm
            if ($page_width == 612 && $page_height == 792) {
                $page_size = 'letter';
            } elseif ($page_width == 595 && $page_height == 842) {
                $page_size = 'a4';
            }

            $file_name_orig = $file -> getClientOriginalName();
            $filename = $file_name_orig;

            $ext = $file -> getClientOriginalExtension();
            $file_name_remove_numbers = preg_replace('/[0-9-_\s\.]+\.'.$ext.'/', '.'.$ext, $filename);
            $file_name_no_ext = str_replace('.'.$ext, '', $file_name_remove_numbers);
            $clean_filename = sanitize($file_name_no_ext);
            $new_filename = $clean_filename.'.'.$ext;

            $state = $request['state'];
            $helper_text = $request['helper_text'];
            $form_categories = implode(',', $request['form_categories']);
            $form_tags = $request['form_tags'];
            $checklist_group_id = $request['checklist_group_id'];
            $form_group_id = $request['form_group_id'];
            $file_name_display = $request['file_name_display'];

            $pages_total = exec('pdftk '.$file.' dump_data | sed -n \'s/^NumberOfPages:\s//p\'');

            // add original file to database
            $upload = new Upload();
            $upload -> file_name = $new_filename;
            $upload -> file_name_orig = $file_name_orig;
            $upload -> file_name_display = $file_name_display;
            $upload -> pages_total = $pages_total;
            $upload -> state = $state;
            $upload -> helper_text = $helper_text;
            $upload -> form_categories = $form_categories;
            $upload -> form_tags = $form_tags;
            $upload -> checklist_group_id = $checklist_group_id;
            $upload -> form_group_id = $form_group_id;
            $upload -> pages_total = $pages_total;
            $upload -> page_width = $page_width;
            $upload -> page_height = $page_height;
            $upload -> page_size = $page_size;
            $upload -> save();
            $file_id = $upload -> file_id;

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
}
