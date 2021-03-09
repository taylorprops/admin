<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDocsChecklistsItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('docs_checklists_items', function(Blueprint $table)
		{
			$table->foreign('checklist_id', 'fk_checklists_items_checklist_id')->references('id')->on('docs_checklists')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('docs_checklists_items', function(Blueprint $table)
		{
			$table->dropForeign('fk_checklists_items_checklist_id');
		});
	}

}
