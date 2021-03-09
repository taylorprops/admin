<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDocsTransactionsChecklistItemNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('docs_transactions_checklist_item_notes', function(Blueprint $table)
		{
			$table->foreign('checklist_item_id', 'dk_checklist_item_notes')->references('id')->on('docs_transactions_checklist_items')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('docs_transactions_checklist_item_notes', function(Blueprint $table)
		{
			$table->dropForeign('dk_checklist_item_notes');
		});
	}

}
