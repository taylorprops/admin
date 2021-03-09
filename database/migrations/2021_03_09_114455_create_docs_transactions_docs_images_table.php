<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsDocsImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_docs_images', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('document_id');
			$table->string('file_name', 200);
			$table->integer('pages_total');
			$table->text('file_location');
			$table->integer('page_number')->nullable();
			$table->integer('order')->nullable();
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
		Schema::drop('docs_transactions_docs_images');
	}

}
