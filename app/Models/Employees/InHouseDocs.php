<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InHouseDocs extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    public $table = 'emp_in_house_docs';
    protected $guarded = [];

}
