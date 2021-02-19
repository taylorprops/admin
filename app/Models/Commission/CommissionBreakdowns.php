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
        return $this -> hasMany('App\Models\Commission\CommissionBreakdownsDeductions', 'commission_breakdown_id');
    }
}
