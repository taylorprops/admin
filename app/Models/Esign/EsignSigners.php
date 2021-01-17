<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;

class EsignSigners extends Model
{
    public $table = 'esign_signers';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];


}
