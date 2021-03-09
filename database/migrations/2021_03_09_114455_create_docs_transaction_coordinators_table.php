<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionCoordinatorsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transaction_coordinators', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('active', 5)->nullable()->default('yes');
			$table->string('first_name', 45)->nullable()->comment('	');
			$table->string('last_name', 45)->nullable();
			$table->string('email', 45)->nullable();
			$table->string('cell_phone', 15)->nullable();
			$table->string('address_street', 65)->nullable();
			$table->string('address_unit', 15)->nullable();
			$table->string('address_city', 45)->nullable();
			$table->string('address_state', 5)->nullable();
			$table->string('address_zip', 15)->nullable();
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
		Schema::drop('docs_transaction_coordinators');
	}

}
