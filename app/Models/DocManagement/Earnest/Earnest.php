<?php

namespace App\Models\DocManagement\Earnest;

use Illuminate\Database\Eloquent\Model;

class Earnest extends Model
{
    public $table = 'earnest';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function checks() {
        return $this -> hasMany('App\Models\DocManagement\Earnest\EarnestChecks', 'Earnest_ID', 'id');
    }

    public function notes() {
        return $this -> hasMany('App\Models\DocManagement\Earnest\EarnestNotes', 'Earnest_ID', 'id');
    }
}
