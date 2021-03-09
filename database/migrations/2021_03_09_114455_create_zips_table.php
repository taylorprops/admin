<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZipsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zips', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('zip')->nullable();
			$table->string('lat', 45)->nullable();
			$table->string('lon', 45)->nullable();
			$table->string('city', 45)->nullable();
			$table->string('state', 45)->nullable();
			$table->string('county', 45)->nullable();
			$table->string('zip_type', 45)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('zips');
	}

}
