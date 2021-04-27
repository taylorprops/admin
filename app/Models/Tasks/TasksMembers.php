<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TasksMembers extends Model
{
    use HasFactory;

    protected $table = 'tasks_members';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function task() {
        return $this -> hasOne(\App\Models\Tasks\Tasks::class, 'id', 'task_id');
    }

    public function member_details() {
        return $this -> hasOne(\App\Models\DocManagement\Transactions\Members\Members::class, 'id', 'member_id');
    }

}
