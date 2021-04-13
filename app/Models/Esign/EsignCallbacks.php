<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Model;

class EsignCallbacks extends Model
{
    protected $table = 'esign_callbacks';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
