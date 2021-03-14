<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;

class EsignDocuments extends Model
{
    public $table = 'esign_documents';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function envelope() {
        return $this -> hasOne(\App\Models\Esign\EsignEnvelopes::class, 'id', 'envelope_id');
    }

    public function images() {
        //return $this -> hasMany('App\Models\Esign\EsignDocumentsImages', 'document_id', 'transaction_document_id');
        return $this -> hasMany(\App\Models\Esign\EsignDocumentsImages::class, 'envelope_id', 'envelope_id');
    }

    public function fields() {
        return $this -> hasMany(\App\Models\Esign\EsignFields::class, 'document_id', 'id') -> with('signer');
    }

    public function signers() {
        return $this -> hasMany(\App\Models\Esign\EsignSigners::class, 'document_id', 'id');
    }
}
