<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;

class TransactionChecklistItemsDocs extends Model
{

    protected $connection = 'mysql';
    public $table = 'docs_transactions_checklist_item_docs';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function ScopeGetDocs($query, $checklist_item_id) {

        $docs = $this -> where('checklist_item_id', $checklist_item_id) -> orderBy('created_at', 'DESC') -> get();

        return $docs;

    }

    public function scopeGetDocsToReviewCount($query, $id, $type) {

        if($type == 'listing') {
            $docs = $this -> where('Listing_ID', $id);
        } else if($type == 'contract') {
            $docs = $this -> where('Contract_ID', $id);
        } else if($type == 'referral') {
            $docs = $this -> where('Referral_ID', $id);
        }

        $docs = $docs -> where('doc_status', 'pending') -> get();

        return $docs;

    }

    public function convert_doc_to_images($source, $destination, $filename, $document_id) {

        // clear directory
        if(!is_dir_empty($destination)) {
            exec('rm -r '.$destination.'/*');
        }
        // delete current images in db
        $remove = TransactionDocumentsImages::where('document_id', $document_id) -> delete();
        // create images from converted file and put in converted_images directory
        $create_images = exec('convert -density 200 -quality 85 '.$source.' -background white -alpha remove -strip '.$destination.'/'.$filename, $output, $return);


        // add the new images to db
        $c = 0;
        foreach (glob($destination.'/*') as $file) {

            $order = 0;
            if(preg_match('/-([0-9]+)\.jpg/', $file, $match)) {
                $order = $match[1];
            }
            $page_number = $order + 1;
            $file_location = str_replace(base_path(), '', $file);
            $file_location = str_replace('/storage/app/public', '/storage', $file_location);
            $add_image = new TransactionDocumentsImages();
            $add_image -> file_name = basename($file);
            $add_image -> document_id = $document_id;
            $add_image -> file_location = $file_location;
            $add_image -> page_number = $page_number;
            $add_image -> order = $order;
            $add_image -> save();
            $c += 1;
        }

        $add_total_pages = TransactionDocumentsImages::where('document_id', $document_id) -> update(['pages_total' => $c]);

    }
}
