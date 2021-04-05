<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    public $table = 'calendar';
    protected $_connection = 'mysql';
    protected $_primaryKey = 'id';
    protected $guarded = [];


}
