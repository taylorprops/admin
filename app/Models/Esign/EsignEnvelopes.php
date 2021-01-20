<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EsignEnvelopes extends Model
{

    use SoftDeletes;

    public $table = 'esign_envelopes';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(function ($query) {
            if(auth() -> user()) {
                //if(auth() -> user() -> group == 'agent') {
                    $query -> where('User_ID', auth() -> user() -> id);
                //}
            }
        });
    }

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
