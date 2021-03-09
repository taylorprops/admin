<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpAgentsNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emp_agents_notes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('Agent_ID');
			$table->string('agent_name', 45)->nullable();
			$table->text('notes');
			$table->string('created_by', 45)->nullable();
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
		Schema::drop('emp_agents_notes');
	}

}
