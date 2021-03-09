<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsChecklistItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_checklist_items', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('checklist_id')->nullable();
			$table->bigInteger('Listing_ID')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->integer('checklist_form_id')->nullable();
			$table->string('checklist_item_added_name', 145)->nullable();
			$table->string('checklist_item_required', 4)->nullable();
			$table->string('checklist_item_status', 45)->nullable()->default('not_reviewed')->comment('not_reviewed, accepted, rejected');
			$table->integer('checklist_item_group_id')->nullable();
			$table->integer('checklist_item_order')->nullable();
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
		Schema::drop('docs_transactions_checklist_items');
	}

}
