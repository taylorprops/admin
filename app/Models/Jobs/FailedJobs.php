<?php

namespace App\Models\Jobs;

use Illuminate\Database\Eloquent\Model;

class FailedJobs extends Model
{
    protected $table = 'failed_jobs';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
