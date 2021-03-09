<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDocsTransactionsUploadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('docs_transactions_uploads', function(Blueprint $table)
		{
			$table->foreign('Transaction_Docs_ID', 'fk_transactions_uploads_file_id')->references('id')->on('docs_transactions_docs')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('docs_transactions_uploads', function(Blueprint $table)
		{
			$table->dropForeign('fk_transactions_uploads_file_id');
		});
	}

}
