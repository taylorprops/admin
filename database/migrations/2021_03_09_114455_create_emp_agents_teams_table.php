<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpAgentsTeamsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emp_agents_teams', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('team_name', 145)->nullable()->default('yes');
			$table->integer('team_leader_id')->nullable();
			$table->string('active', 5)->nullable()->default('yes');
			$table->timestamps(10);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('emp_agents_teams');
	}

}
