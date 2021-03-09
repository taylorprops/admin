<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommissionChecksOutTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commission_checks_out', function(Blueprint $table)
		{
			$table->foreign('Commission_ID', 'fk_commission_checks_out')->references('id')->on('commission')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('commission_checks_out', function(Blueprint $table)
		{
			$table->dropForeign('fk_commission_checks_out');
		});
	}

}
