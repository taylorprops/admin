<?php

namespace App\Models\DocManagement\Earnest;

use Illuminate\Database\Eloquent\Model;

class EarnestNotes extends Model
{
    public $table = 'earnest_notes';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];
}