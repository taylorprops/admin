<?php

namespace App\Models\OldDB;

use Illuminate\Database\Eloquent\Model;

class OldAgentsNotes extends Model
{
    protected $connection = 'mysql_company';
    protected $table = 'tbl_agents_notes';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
