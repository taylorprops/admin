<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsZipsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_zips', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('city', 45)->nullable();
			$table->string('county', 45)->nullable();
			$table->string('state_name', 45)->nullable();
			$table->string('state', 5)->nullable();
			$table->string('zip', 15)->nullable();
			$table->string('lat', 45)->nullable();
			$table->string('lon', 45)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_zips');
	}

}
