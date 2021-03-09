<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDocsTransactionsUploadsPagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('docs_transactions_uploads_pages', function(Blueprint $table)
		{
			$table->foreign('file_id', 'fk_transaction_pages_file_id')->references('file_id')->on('docs_transactions_uploads')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('docs_transactions_uploads_pages', function(Blueprint $table)
		{
			$table->dropForeign('fk_transaction_pages_file_id');
		});
	}

}
