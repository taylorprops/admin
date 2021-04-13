<?php

namespace App\Models\DocManagement\Transactions\Checklists;

use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TransactionChecklistItemsDocs extends Model
{
    protected $connection = 'mysql';
    protected $table = 'docs_transactions_checklist_item_docs';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function original_doc() {
        return $this -> hasOne(\App\Models\DocManagement\Transactions\Documents\TransactionDocuments::class, 'id', 'document_id');
    }

    public function images() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages::class, 'document_id', 'document_id');
    }

    public function ScopeGetDocs($query, $checklist_item_id) {
        $docs = $this -> where('checklist_item_id', $checklist_item_id) -> orderBy('created_at', 'DESC') -> get();

        return $docs;
    }

    public function scopeGetDocsToReviewCount($query, $id, $type) {
        if ($type == 'listing') {
            $docs = $this -> where('Listing_ID', $id);
        } elseif ($type == 'contract') {
            $docs = $this -> where('Contract_ID', $id);
        } elseif ($type == 'referral') {
            $docs = $this -> where('Referral_ID', $id);
        }

        $docs = $docs -> where('doc_status', 'pending') -> get();

        return $docs;
    }

    public function convert_doc_to_images($source, $destination, $filename, $document_id) {

        // clear directory
        if (! is_dir_empty($destination)) {
            exec('rm -r '.$destination.'/*');
        }
        // delete current images in db
        $remove = TransactionDocumentsImages::where('document_id', $document_id) -> delete();
        // create images from converted file and put in converted_images directory
        $create_images = exec('convert -density 300 -quality 80 '.$source.' -background white -alpha remove -strip -compress JPEG '.$destination.'/page_%02d.jpg');

        // add the new images to db
        $c = 0;
        $order = 0;
        foreach (glob($destination.'/*') as $file) {

            /* if(preg_match('/page_([0-9]+)\.jpg/', $file, $match)) {
                $order = $match[1];
            }
            $page_number = $order + 1; */

            $page_number = preg_match('/page_([0-9]+)\.jpg/', $file, $matches);
            $match = $matches[1];
            if (substr($match, 0, 1 == 0)) {
                $match = substr($match, 1);
            }
            $page_number = count($matches) > 1 ? $match + 1 : 1;

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
