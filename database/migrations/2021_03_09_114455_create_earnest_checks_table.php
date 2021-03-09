<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEarnestChecksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('earnest_checks', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('active', 5)->nullable()->default('yes');
			$table->integer('Earnest_ID');
			$table->bigInteger('Contract_ID');
			$table->integer('Agent_ID');
			$table->string('check_type', 5)->nullable()->comment('in, out');
			$table->string('check_name', 65)->nullable();
			$table->string('payable_to', 45)->nullable();
			$table->date('check_date')->nullable();
			$table->string('check_number', 45)->nullable();
			$table->decimal('check_amount', 10)->nullable();
			$table->string('check_status', 15)->nullable()->default('pending')->comment('pending, cleared, bounced');
			$table->text('file_location')->nullable();
			$table->text('image_location')->nullable();
			$table->date('date_cleared')->nullable();
			$table->date('date_deposited')->nullable();
			$table->text('mail_to_address')->nullable();
			$table->date('date_sent')->nullable();
			$table->integer('transferred_from_check_id')->nullable();
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
		Schema::drop('earnest_checks');
	}

}
