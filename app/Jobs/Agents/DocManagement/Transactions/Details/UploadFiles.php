<?php

namespace App\Jobs\Agents\DocManagement\Transactions\Details;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadPages;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Upload\TransactionUploadImages;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;
use App\Models\DocManagement\Transactions\Checklists\TransactionChecklistItemsDocs;

class UploadFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $file;
    protected $file_id;
    protected $file_name;
    protected $file_name_display;
    protected $new_file_name;
    protected $ext;
    protected $Agent_ID;
    protected $Listing_ID;
    protected $Contract_ID;
    protected $Referral_ID;
    protected $transaction_type;
    protected $folder;
    protected $storage_dir;
    protected $Transaction_Docs_ID;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file, $file_id, $file_name, $file_name_display, $new_file_name, $ext, $Agent_ID, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, $folder, $storage_dir, $Transaction_Docs_ID)
    {

        $this -> file = $file;
        $this -> file_id = $file_id;
        $this -> file_name = $file_name;
        $this -> file_name_display = $file_name_display;
        $this -> new_file_name = $new_file_name;
        $this -> ext = $ext;
        $this -> Agent_ID = $Agent_ID;
        $this -> Listing_ID = $Listing_ID;
        $this -> Contract_ID = $Contract_ID;
        $this -> Referral_ID = $Referral_ID;
        $this -> transaction_type = $transaction_type;
        $this -> folder = $folder;
        $this -> storage_dir = $storage_dir;
        $this -> Transaction_Docs_ID = $Transaction_Docs_ID;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $file = $this -> file;
        $file_id = $this -> file_id;
        $file_name = $this -> file_name;
        $file_name_display = $this -> file_name_display;
        $new_file_name = $this -> new_file_name;
        $ext = $this -> ext;
        $Agent_ID = $this -> Agent_ID;
        $Listing_ID = $this -> Listing_ID;
        $Contract_ID = $this -> Contract_ID;
        $Referral_ID = $this -> Referral_ID;
        $transaction_type = $this -> transaction_type;
        $folder = $this -> folder;
        $storage_dir = $this -> storage_dir;
        $Transaction_Docs_ID = $this -> Transaction_Docs_ID;

        /************************************************/

        $storage_link = '/storage/'.$storage_dir;
        $storage_full_path = Storage::path($storage_dir);

        // create directories
        $storage_dir_pages = $storage_dir.'/pages';
        $storage_dir_images = $storage_dir.'/images';

        // split pdf into pages and images
        $input_file = $storage_full_path.'/'.$new_file_name;
        $output_files = Storage::path($storage_dir_pages.'/page_%02d.pdf');
        $new_image_name = str_replace($ext, 'jpg', $new_file_name);
        $output_images = Storage::path($storage_dir_images.'/page_%02d.jpg');

        // add individual pages to pages directory
        $create_pages = exec('pdftk '.$input_file.' burst output '.$output_files.' flatten', $output, $return);
        // remove data file
        exec('rm '.Storage::path($storage_dir_pages.'/doc_data.txt'));

        // add individual images to images directory
        $create_images = exec('convert -density 200 -quality 80 '.$input_file.' -background white -alpha remove -strip '.$output_images, $output, $return);

        // get all image files images_storage_path to use as file location
        $saved_images_directory = Storage::files($storage_dir.'/images');
        $images_public_path = $storage_link.'/images';

        $pages_total = count($saved_images_directory);

        foreach ($saved_images_directory as $saved_image) {
            // get just file_name
            $images_file_name = basename($saved_image);

            $page_number = preg_match('/page_([0-9]+)\.jpg/', $images_file_name, $matches);
            $match = $matches[1];
            if (substr($match, 0, 1 == 0)) {
                $match = substr($match, 1);
            }
            $page_number = count($matches) > 1 ? $match + 1 : 1;

            $upload_images = new TransactionUploadImages();
            $upload_images -> file_id = $file_id;
            $upload_images -> Agent_ID = $Agent_ID;
            $upload_images -> Listing_ID = $Listing_ID;
            $upload_images -> Contract_ID = $Contract_ID;
            $upload_images -> Referral_ID = $Referral_ID;
            $upload_images -> file_name = $images_file_name;
            $upload_images -> file_location = $images_public_path.'/'.$images_file_name;
            $upload_images -> pages_total = $pages_total;
            $upload_images -> page_number = $page_number;
            $upload_images -> save();


            $from = Storage::path($saved_image);
            $to = Storage::path($storage_dir.'/converted_images');

            exec('cp '.$from.' '.$to);

            $file_location = '/storage/'.$storage_dir.'/converted_images/'.$images_file_name;

            $add_image = new TransactionDocumentsImages();
            $add_image -> file_name = $images_file_name;
            $add_image -> document_id = $Transaction_Docs_ID;
            $add_image -> file_location = $file_location;
            $add_image -> page_number = $page_number;
            $add_image -> order = $page_number;
            $add_image -> save();

        }

        $saved_pages_directory = Storage::files($storage_dir.'/pages');
        $pages_public_path = $storage_link.'/pages';

        $page_number = 1;

        foreach ($saved_pages_directory as $saved_page) {
            $pages_file_name = basename($saved_page);
            $upload_pages = new TransactionUploadPages();
            $upload_pages -> Agent_ID = $Agent_ID;
            $upload_pages -> Listing_ID = $Listing_ID;
            $upload_pages -> Contract_ID = $Contract_ID;
            $upload_pages -> Referral_ID = $Referral_ID;
            $upload_pages -> file_id = $file_id;
            $upload_pages -> file_name = $pages_file_name;
            $upload_pages -> file_location = $pages_public_path.'/'.$pages_file_name;
            $upload_pages -> pages_total = $pages_total;
            $upload_pages -> page_number = $page_number;
            $upload_pages -> save();

            $page_number += 1;
        }



        /************************************************/

    }



}
