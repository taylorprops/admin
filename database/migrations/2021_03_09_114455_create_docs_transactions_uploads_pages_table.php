<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsUploadsPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_uploads_pages', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('Agent_ID');
			$table->bigInteger('Listing_ID');
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->integer('file_id')->nullable()->index('fk_transaction_pages_file_id');
			$table->text('file_name')->nullable();
			$table->text('file_location')->nullable();
			$table->integer('pages_total')->nullable();
			$table->integer('page_number')->nullable();
			$table->timestamps(10);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_transactions_uploads_pages');
	}

}
