<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InHouse extends Model {

    use HasFactory;

    protected $connection = 'mysql';
    public $table = 'emp_in_house';
    protected $guarded = [];

    public function docs() {
        return $this -> hasMany(\App\Models\Employees\InHouseDocs::class, 'emp_in_house_id', 'id') -> orderBy('created_at', 'desc');
    }


}
