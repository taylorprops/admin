<?php

namespace App\Models\BugReports;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugReports extends Model
{
    use HasFactory;

    protected $table = 'bug_reports';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function user() {
        return $this -> hasOne(\App\User::class, 'id', 'user_id');
    }


}
