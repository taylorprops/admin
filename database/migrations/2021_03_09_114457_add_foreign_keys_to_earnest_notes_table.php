<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToEarnestNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('earnest_notes', function(Blueprint $table)
		{
			$table->foreign('Earnest_ID', 'fk_earnest_notes')->references('id')->on('earnest')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('earnest_notes', function(Blueprint $table)
		{
			$table->dropForeign('fk_earnest_notes');
		});
	}

}
