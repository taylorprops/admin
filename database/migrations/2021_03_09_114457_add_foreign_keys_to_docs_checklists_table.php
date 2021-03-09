<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDocsChecklistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('docs_checklists', function(Blueprint $table)
		{
			$table->foreign('checklist_location_id', 'fk_checklists_resource_id')->references('resource_id')->on('docs_resource_items')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('docs_checklists', function(Blueprint $table)
		{
			$table->dropForeign('fk_checklists_resource_id');
		});
	}

}
