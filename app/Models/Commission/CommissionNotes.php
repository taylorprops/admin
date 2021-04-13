<?php

namespace App\Models\Commission;

use Illuminate\Database\Eloquent\Model;

class CommissionNotes extends Model
{
    protected $table = 'commission_notes';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function user() {
        return $this -> hasOne(\App\User::class, 'id', 'user_id');
    }
}
