<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;

class EsignFields extends Model
{
    public $table = 'esign_fields';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
