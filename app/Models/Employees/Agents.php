<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    protected $connection = 'mysql';
    public $table = 'emp_agents';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function scopeAgentDetails($query, $id)
    {
        $agent_details = $query->find($id);

        return $agent_details;
    }

    /* public function earnest_deposits() {
        return $this -> hasMany('App\Models\DocManagement\Earnest\Earnest', 'Agent_ID', 'id');
    }

    public function earnest_deposit_checks() {
        return $this -> hasMany('App\Models\DocManagement\Earnest\EarnestChecks', 'Agent_ID', 'id');
    } */
}
