<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsChecklistItemDocsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_checklist_item_docs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('document_id')->nullable();
			$table->integer('checklist_id')->nullable();
			$table->integer('checklist_item_id')->nullable();
			$table->bigInteger('Listing_ID')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->string('doc_status', 45)->nullable()->default('pending')->comment('pending, viewed');
			$table->string('file_name', 150)->nullable();
			$table->text('file_location')->nullable();
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
		Schema::drop('docs_transactions_checklist_item_docs');
	}

}
