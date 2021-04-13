<?php

namespace App\Models\BrightMLS;

use Illuminate\Database\Eloquent\Model;

class Offices extends Model
{
    protected $connection = 'mysql';
    protected $table = 'bright_offices';
    protected $primaryKey = 'OfficeKey';
    public $timestamps = false;
    protected $guarded = [];
}
