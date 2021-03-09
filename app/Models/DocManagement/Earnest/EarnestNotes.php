<?php

namespace App\Models\DocManagement\Earnest;

use Illuminate\Database\Eloquent\Model;

class EarnestNotes extends Model
{
    public $table = 'earnest_notes';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'user_id');
    }
}
