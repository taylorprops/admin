<?php

namespace App\Models\DocManagement\Earnest;

use Illuminate\Database\Eloquent\Model;

class EarnestChecks extends Model
{
    public $table = 'earnest_checks';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function agent() {
        return $this -> hasOne('App\Models\Employees\Agents', 'id', 'Agent_ID');
    }

    public function property() {
        return $this -> hasOne('App\Models\DocManagement\Transactions\Contracts\Contracts', 'Contract_ID', 'Contract_ID');
    }

    public function earnest() {
        return $this -> hasOne('App\Models\DocManagement\Earnest\Earnest', 'id', 'Earnest_ID');
    }

}
