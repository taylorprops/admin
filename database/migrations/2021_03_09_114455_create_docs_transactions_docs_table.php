<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsDocsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_docs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('Agent_ID');
			$table->bigInteger('Listing_ID');
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->integer('template_id')->nullable();
			$table->integer('queue_id')->nullable();
			$table->string('assigned', 5)->nullable()->default('no');
			$table->string('signed', 5)->nullable()->default('no');
			$table->integer('checklist_item_id')->nullable();
			$table->string('folder', 45)->nullable();
			$table->string('file_type', 45)->nullable()->comment('options - system, user');
			$table->bigInteger('file_id')->nullable()->comment('file_id from docs_create_uploads');
			$table->integer('orig_file_id')->nullable();
			$table->text('file_name');
			$table->text('file_name_display');
			$table->integer('pages_total');
			$table->text('file_location');
			$table->text('file_location_converted')->nullable();
			$table->integer('doc_order')->nullable();
			$table->string('transaction_type', 45)->nullable();
			$table->integer('page_width')->nullable();
			$table->integer('page_height')->nullable();
			$table->string('page_size', 15)->nullable();
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
		Schema::drop('docs_transactions_docs');
	}

}
