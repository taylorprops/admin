<?php

namespace App\Models\OldDB;

use Illuminate\Database\Eloquent\Model;

class OldAgents extends Model
{
    protected $connection = 'mysql_company';
    protected $table = 'tbl_agents';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
