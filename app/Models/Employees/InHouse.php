<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Model;

class InHouse extends Model
{
    protected $connection = 'mysql';
    public $table = 'emp_in_house';
    protected $guarded = [];
}
