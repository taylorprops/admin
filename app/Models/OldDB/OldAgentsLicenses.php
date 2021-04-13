<?php

namespace App\Models\OldDB;

use Illuminate\Database\Eloquent\Model;

class OldAgentsLicenses extends Model
{
    protected $connection = 'mysql_company';
    protected $table = 'tbl_agents_licenses';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = [];
}
