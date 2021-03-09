<?php

namespace App\Models\DocManagement\Transactions\Upload;

use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;
use App\Models\DocManagement\Transactions\Documents\TransactionDocumentsFolders;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionUpload extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_transactions_uploads';
    protected $primaryKey = 'file_id';
    protected $guarded = [];

    public function images()
    {
        return $this->hasMany(\App\Models\DocManagement\Transactions\Upload\TransactionUploadImages::class, 'file_id', 'file_id')->orderBy('page_number');
    }

    public function pages()
    {
        return $this->hasMany(\App\Models\DocManagement\Transactions\Upload\TransactionUploadPages::class, 'file_id', 'file_id')->orderBy('page_number');
    }

    /* public function scopeFormGroupFiles($query, $location_id, $Listing_ID, $Contract_ID, $type) {

        $forms_available = $this -> where('form_group_id', $location_id)
            -> where('published', 'yes')
            -> orderBy('file_name_display', 'ASC') -> get();

        //$forms_in_use = null;

        if($type != '') {
            if($Contract_ID > 0 || $Listing_ID > 0) {
                $trash_folder = TransactionDocumentsFolders::where(function($query) use ($Contract_ID, $Listing_ID) {
                    $query -> where(function($q) use ($Listing_ID) {
                        $q -> where('Listing_ID', $Listing_ID) -> where('Listing_ID', '>', '0');
                    })
                    -> orWhere(function($q) use ($Contract_ID) {
                        $q -> where('Contract_ID', $Contract_ID) -> where('Contract_ID', '>', '0');
                    });
                })
                -> where('folder_name', 'Trash') -> first();

            }
        }

        return compact('forms_available');

    } */

    public function scopeGetFormName($query, $form_id)
    {
        if ($form_id) {
            $form_name = $query->where('file_id', $form_id)->first();

            return $form_name->file_name_display;
        }

        return  true;
    }

    public function scopeGetFormLocation($query, $form_id)
    {
        $form_name = $query->where('file_id', $form_id)->first();
        if ($form_name->file_location) {
            return $form_name->file_location;
        }

        return '';
    }
}
