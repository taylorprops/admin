<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;

class EsignFields extends Model
{
    public $table = 'esign_fields';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function signer() {
        return $this -> hasOne('App\Models\Esign\EsignSigners', 'id', 'signer_id');
    }
}
