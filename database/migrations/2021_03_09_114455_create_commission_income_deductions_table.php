<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionIncomeDeductionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission_income_deductions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('commission_id')->index('fk_commission_income_deductions');
			$table->decimal('amount', 10);
			$table->text('description');
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
		Schema::drop('commission_income_deductions');
	}

}
