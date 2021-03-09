<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    public $table = 'commission';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(\App\Models\Employees\Agents::class, 'Agent_ID');
    }

    public function property_contract()
    {
        return $this->belongsTo(\App\Models\DocManagement\Transactions\Contracts\Contracts::class, 'Contract_ID', 'Contract_ID');
    }

    public function property_referral()
    {
        return $this->belongsTo(\App\Models\DocManagement\Transactions\Referrals\Referrals::class, 'Referral_ID', 'Referral_ID');
    }

    public function other_checks()
    {
        return $this->hasMany(\App\Models\Commission\CommissionChecksIn::class, 'Commission_ID')/*  -> where('commission_checks_in.active', 'yes') */;
    }
}
