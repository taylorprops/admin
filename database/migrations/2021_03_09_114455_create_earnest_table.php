<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarnestTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('earnest', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('status', 15)->nullable()->default('pending')->comment('pending, active, released, transferred');
			$table->string('held_by', 45)->nullable()->comment('us, other_company, title, heritage_title, builder');
			$table->integer('earnest_account_id')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->decimal('amount_total', 10)->nullable()->default(0.00);
			$table->decimal('amount_received', 10)->nullable()->default(0.00);
			$table->decimal('amount_released', 10)->nullable()->default(0.00);
			$table->integer('transferred_from_Contract_ID')->nullable();
			$table->integer('transferred_to_Contract_ID')->nullable();
			$table->string('release_to_street', 85)->nullable();
			$table->string('release_to_city', 45)->nullable();
			$table->string('release_to_state', 4)->nullable();
			$table->string('release_to_zip', 15)->nullable();
			$table->date('last_emailed_date')->nullable();
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
		Schema::drop('earnest');
	}

}
