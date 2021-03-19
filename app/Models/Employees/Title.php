<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    public $table = 'emp_title';
    protected $guarded = [];

}
