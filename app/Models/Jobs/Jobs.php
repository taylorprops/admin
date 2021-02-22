<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    public $table = 'jobs';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];
}