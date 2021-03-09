<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommissionIncomeDeductionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commission_income_deductions', function(Blueprint $table)
		{
			$table->foreign('commission_id', 'fk_commission_income_deductions')->references('id')->on('commission')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('commission_income_deductions', function(Blueprint $table)
		{
			$table->dropForeign('fk_commission_income_deductions');
		});
	}

}
