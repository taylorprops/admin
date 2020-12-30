<?php

namespace App\Models\DocManagement\Earnest;

use Illuminate\Database\Eloquent\Model;

class EarnestChecks extends Model
{
    public $table = 'earnest_checks';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
