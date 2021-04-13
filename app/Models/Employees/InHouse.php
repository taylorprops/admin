<?php

namespace App\Models\Employees;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InHouse extends Model {

    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'emp_in_house';
    protected $guarded = [];

    public function docs() {
        return $this -> hasMany(\App\Models\Employees\InHouseDocs::class, 'emp_in_house_id', 'id') -> orderBy('created_at', 'desc');
    }

    public function user_account() {
        return $this -> hasOne(\App\User::class, 'user_id', 'id');
    }


}
