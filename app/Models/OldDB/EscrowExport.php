<?php

namespace App\Models\OldDB;

use Illuminate\Database\Eloquent\Model;

class EscrowExport extends Model
{
    protected $connection = 'mysql_company';
    protected $table = 'escrow_import';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
