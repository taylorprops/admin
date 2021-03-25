<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class CommissionBreakdowns extends Model
{
    public $table = 'commission_breakdowns';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function deductions() {
        return $this -> hasMany(\App\Models\Commission\CommissionBreakdownsDeductions::class, 'commission_breakdown_id');
    }

    public function agent() {
        return $this -> belongsTo(\App\Models\Employees\Agents::class, 'Agent_ID');
    }

    public function property_contract() {
        return $this -> belongsTo(\App\Models\DocManagement\Transactions\Contracts\Contracts::class, 'Contract_ID', 'Contract_ID');
    }

    public function property_referral() {
        return $this -> belongsTo(\App\Models\DocManagement\Transactions\Referrals\Referrals::class, 'Referral_ID', 'Referral_ID');
    }

}
