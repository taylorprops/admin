<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpAgentsLicensesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emp_agents_licenses', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('active', 3)->default('yes');
			$table->integer('Agent_ID');
			$table->string('state', 2);
			$table->string('number', 45);
			$table->date('expiration')->nullable();
			$table->string('company', 45);
			$table->text('file_location')->nullable();
			$table->string('received', 3)->default('no');
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
		Schema::drop('emp_agents_licenses');
	}

}
