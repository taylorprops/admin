<?php

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
        factory(CRMContacts::class, $count)->create();
    }
}
