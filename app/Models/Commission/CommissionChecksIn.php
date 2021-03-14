<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class CommissionChecksIn extends Model
{
    public $table = 'commission_checks_in';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function agent() {
        return $this -> belongsTo(\App\Models\Employees\Agents::class, 'Agent_ID');
    }
}
