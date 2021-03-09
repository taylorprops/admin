<?php

namespace Database\Seeders;

use App\Models\CRM\CRMContacts;
use Illuminate\Database\Seeder;

class AgentContacts extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = 100;
        CRMContacts::factory()->count($count)->create();
    }
}
