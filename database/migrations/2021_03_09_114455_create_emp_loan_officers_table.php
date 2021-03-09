<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpLoanOfficersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emp_loan_officers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('active', 5)->nullable()->default('yes');
			$table->string('first_name', 45)->nullable()->comment('	');
			$table->string('last_name', 45)->nullable();
			$table->string('email', 45)->nullable();
			$table->string('cell_phone', 15)->nullable();
			$table->string('home_phone', 15)->nullable();
			$table->string('address_street', 65)->nullable();
			$table->string('address_unit', 15)->nullable();
			$table->string('address_city', 45)->nullable();
			$table->string('address_state', 5)->nullable();
			$table->string('address_zip', 15)->nullable();
			$table->text('signature')->nullable();
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
		Schema::drop('emp_loan_officers');
	}

}
