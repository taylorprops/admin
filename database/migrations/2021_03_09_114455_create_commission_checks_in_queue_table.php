<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionChecksInQueueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission_checks_in_queue', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('Commission_ID')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->string('agent_name', 45)->nullable();
			$table->date('date_received');
			$table->date('date_deposited')->nullable();
			$table->date('check_date');
			$table->integer('check_number');
			$table->decimal('check_amount', 10);
			$table->text('file_location');
			$table->text('image_location');
			$table->string('street', 145)->nullable();
			$table->string('city', 45)->nullable();
			$table->string('state', 4)->nullable();
			$table->string('zip', 15)->nullable();
			$table->string('client_name', 45)->nullable()->comment('will always be blank - needed to select equal columns getting deleted checks');
			$table->string('active', 5)->default('yes');
			$table->string('exported', 5)->nullable()->default('no');
			$table->timestamps(10);
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('commission_checks_in_queue');
	}

}
