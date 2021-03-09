<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommissionCommissionDeductionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commission_commission_deductions', function(Blueprint $table)
		{
			$table->foreign('commission_id', 'fk_commission_commission_deductions')->references('id')->on('commission')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('commission_commission_deductions', function(Blueprint $table)
		{
			$table->dropForeign('fk_commission_commission_deductions');
		});
	}

}
