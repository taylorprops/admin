<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionChecksOutTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission_checks_out', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('Commission_ID')->index('fk_commission_checks_out');
			$table->date('check_date');
			$table->integer('check_number');
			$table->decimal('check_amount', 10);
			$table->string('check_recipient', 100);
			$table->integer('check_recipient_agent_id')->nullable();
			$table->string('check_delivery_method', 45)->nullable();
			$table->date('check_date_ready')->nullable();
			$table->string('check_mail_to_street', 145)->nullable();
			$table->string('check_mail_to_city', 45)->nullable();
			$table->string('check_mail_to_state', 4)->nullable();
			$table->string('check_mail_to_zip', 15)->nullable();
			$table->text('file_location');
			$table->text('image_location');
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
		Schema::drop('commission_checks_out');
	}

}
