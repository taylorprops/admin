<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentsNotes extends Model {

    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'emp_agents_notes';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
