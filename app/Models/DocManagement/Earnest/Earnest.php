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

    public function agent() {
        return $this -> hasOne('App\Models\Employees\Agents', 'id', 'Agent_ID');
    }

    public function property() {
        return $this -> hasOne('App\Models\DocManagement\Transactions\Contracts\Contracts', 'Contract_ID', 'Contract_ID');
    }

}
