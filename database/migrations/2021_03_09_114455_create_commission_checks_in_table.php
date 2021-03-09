<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionChecksInTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission_checks_in', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('check_type', 45)->nullable();
			$table->integer('Commission_ID')->nullable();
			$table->integer('Agent_ID')->nullable()->comment('only used for CBs with no property such as BPOs');
			$table->string('agent_name', 45)->nullable();
			$table->integer('queue_id')->nullable();
			$table->date('date_received');
			$table->date('date_deposited')->nullable();
			$table->date('check_date');
			$table->string('check_number', 30);
			$table->decimal('check_amount', 10);
			$table->text('file_location');
			$table->text('image_location');
			$table->string('client_name', 45)->nullable();
			$table->string('street', 145)->nullable()->comment('only used for CBs with no property such as BPOs');
			$table->string('city', 45)->nullable()->comment('only used for CBs with no property such as BPOs');
			$table->string('state', 4)->nullable()->comment('only used for CBs with no property such as BPOs');
			$table->string('zip', 45)->nullable()->comment('only used for CBs with no property such as BPOs');
			$table->string('active', 5)->default('yes');
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
		Schema::drop('commission_checks_in');
	}

}
