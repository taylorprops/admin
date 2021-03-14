<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;

class EsignDocumentsImages extends Model
{
    public $table = 'esign_documents_images';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function images() {
        return $this -> hasMany(self::class, 'document_id', 'id');
    }
}
