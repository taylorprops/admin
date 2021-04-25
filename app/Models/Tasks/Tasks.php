<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tasks extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tasks';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function task_action() {
        return $this -> hasOne(\App\Models\DocManagement\Resources\ResourceItems::class, 'resource_id', 'task_action');
    }

    public function members() {
        return $this -> hasMany(\App\Models\Tasks\TasksMembers::class, 'task_id', 'id');
    }



}
