<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;

class EsignEnvelopes extends Model
{
    public $table = 'esign_envelopes';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function documents() {
        return $this -> hasMany('App\Models\Esign\EsignDocuments', 'envelope_id', 'id');
    }

    public function signers() {
        return $this -> hasMany('App\Models\Esign\EsignSigners', 'envelope_id', 'id');
    }

    public function fields() {
        return $this -> hasMany('App\Models\Esign\EsignFields', 'envelope_id', 'id');
    }
}
