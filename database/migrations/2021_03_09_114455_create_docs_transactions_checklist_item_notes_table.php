<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsChecklistItemNotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_checklist_item_notes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('checklist_id')->nullable();
			$table->integer('checklist_item_id')->nullable()->index('dk_checklist_item_notes_idx');
			$table->integer('checklist_item_doc_id')->nullable();
			$table->bigInteger('Listing_ID')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->integer('Agent_ID');
			$table->integer('note_user_id')->nullable();
			$table->string('note_status', 15)->nullable()->default('unread')->comment('read, unread');
			$table->text('notes')->nullable();
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
		Schema::drop('docs_transactions_checklist_item_notes');
	}

}
