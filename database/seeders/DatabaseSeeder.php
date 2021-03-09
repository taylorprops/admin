<?php

namespace Database\Seeders;

use Database\Seeders\AgentContacts;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AgentContacts::class);
    }
}
