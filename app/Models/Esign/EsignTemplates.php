<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EsignTemplates extends Model
{
    use SoftDeletes;

    protected $table = 'esign_templates';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];



    public function fields() {
        return $this -> hasMany(\App\Models\Esign\EsignTemplatesFields::class, 'template_id', 'id');
    }

    public function signers() {
        return $this -> hasMany(\App\Models\Esign\EsignTemplatesSigners::class, 'template_id', 'id');
    }

    public function images() {
        return $this -> hasMany(\App\Models\Esign\EsignTemplatesDocumentImages::class, 'template_id', 'id');
    }

    public function upload() {
        return $this -> hasOne(\App\Models\DocManagement\Create\Upload\Upload::class, 'template_id', 'id');
    }

}
