<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agents extends Model {

    use HasFactory;

    protected $connection = 'mysql';
    public $table = 'emp_agents';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $hidden = [
        'social_security',
    ];

    public function scopeAgentDetails($query, $id) {
        $agent_details = $query -> find($id);

        return $agent_details;
    }

    public function contracts() {
        return $this -> hasMany('App\Models\DocManagement\Transactions\Contracts\Contracts', 'Agent_ID', 'id');
    }

    /* public function earnest_deposits() {
        return $this -> hasMany('App\Models\DocManagement\Earnest\Earnest', 'Agent_ID', 'id');
    }

    public function earnest_deposit_checks() {
        return $this -> hasMany('App\Models\DocManagement\Earnest\EarnestChecks', 'Agent_ID', 'id');
    } */
}
