<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToCommissionNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('commission_notes', function(Blueprint $table)
		{
			$table->foreign('Commission_ID', 'fk_commission_notes')->references('id')->on('commission')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('commission_notes', function(Blueprint $table)
		{
			$table->dropForeign('fk_commission_notes');
		});
	}

}
