<?php

namespace App\Models\Employees;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentsTeams extends Model {

    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'emp_agents_teams';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function ScopeGetTeamName($request, $id) {
        $team = $request -> find($id);

        return $team -> team_name;
    }
}
