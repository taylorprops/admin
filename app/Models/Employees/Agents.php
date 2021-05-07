<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agents extends Model {

    use HasFactory;

    use \Awobaz\Compoships\Compoships;

    protected $connection = 'mysql';
    protected $table = 'emp_agents';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $hidden = [
        'social_security',
    ];

    public function scopeAgentDetails($query, $id) {
        return $query -> find($id);
    }

    public function contracts() {
        return $this -> hasMany('App\Models\DocManagement\Transactions\Contracts\Contracts', 'Agent_ID', 'id');
    }

    public function user_account() {
        return $this -> hasOne('App\User', 'user_id', 'id') -> where('group', 'like', 'agent%');
    }

    /* public function earnest_deposits() {
        return $this -> hasMany('App\Models\DocManagement\Earnest\Earnest', 'Agent_ID', 'id');
    }

    public function earnest_deposit_checks() {
        return $this -> hasMany('App\Models\DocManagement\Earnest\EarnestChecks', 'Agent_ID', 'id');
    } */
}
