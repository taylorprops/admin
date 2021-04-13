<?php

namespace App\Jobs\OldDB\Agents;

use Illuminate\Bus\Queueable;
use App\Models\OldDB\OldAgentsNotes;
use App\Models\OldDB\OldAgentsTeams;
use App\Models\Employees\AgentsNotes;
use App\Models\Employees\AgentsTeams;
use Illuminate\Queue\SerializesModels;
use App\Models\OldDB\OldAgentsLicenses;
use App\Models\Employees\AgentsLicenses;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdateAgentsTablesOtherJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $delete_agents_licenses = AgentsLicenses::truncate();
        $delete_agents_teams = AgentsTeams::truncate();
        $delete_agents_notes = AgentsNotes::truncate();

        $licenses = OldAgentsLicenses::where('active', 'yes') -> get();
        $teams = OldAgentsTeams::get();
        $notes = OldAgentsNotes::where('deleted', 'no') -> get();

        foreach ($licenses as $license) {
            $add_license = new AgentsLicenses();
            $add_license -> Agent_ID = $license -> agent_id;
            $add_license -> state = $license -> lic_state;
            $add_license -> number = $license -> lic_number;
            $add_license -> expiration = $license -> lic_expire;
            $add_license -> company = $license -> lic_comp;
            $add_license -> file_location = $license -> lic_location;
            $add_license -> save();
        }

        foreach ($teams as $team) {
            $add_team = new AgentsTeams();
            $add_team -> team_name = $team -> team_name;
            $add_team -> team_leader_id = $team -> team_leader;
            $add_team -> active = $team -> active;
            $add_team -> save();
        }

        foreach ($notes as $note) {
            $add_note = new AgentsNotes();
            $add_note -> Agent_ID = $note -> agent_id;
            $add_note -> agent_name = $note -> agent_name;
            $add_note -> notes = $note -> notes;
            $add_note -> created_by = $note -> creator;
            $add_note -> created_at = $note -> date_added;
            $add_note -> save();
        }

    }
}
