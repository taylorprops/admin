<?php

namespace App\Models\Esign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EsignTemplatesSigners extends Model
{
    use HasFactory;

    protected $table = 'esign_templates_signers';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

}
