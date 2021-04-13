<?php

namespace App\Models\DocManagement\Transactions\Documents;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDocuments extends Model
{
    use SoftDeletes;

    protected $table = 'docs_transactions_docs';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function ScopeGetDocInfo($query, $document_id) {
        $document = $this -> where('id', $document_id) -> first();
        $file_name = $document -> file_name_display;
        $file_location_converted = $document -> file_location_converted;

        return compact('file_name', 'file_location_converted');
    }

    public function upload() {
        return $this -> hasOne(\App\Models\DocManagement\Create\Upload\Upload::class, 'file_id', 'orig_file_id');
    }

    public function images_converted() {
        return $this -> hasMany(\App\Models\DocManagement\Transactions\Documents\TransactionDocumentsImages::class, 'document_id', 'id') -> orderBy('page_number');
    }

    public function esign_document() {
        return $this -> hasMany(\App\Models\Esign\EsignDocuments::class, 'transaction_document_id', 'id') -> with('envelope');
    }
}
