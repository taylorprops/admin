<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esign_documents', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('template_id')->nullable();
			$table->integer('template_applied_id')->nullable();
			$table->integer('envelope_id')->nullable();
			$table->integer('transaction_document_id')->nullable();
			$table->text('file_name')->nullable();
			$table->text('file_location')->nullable();
			$table->integer('pages_total')->nullable();
			$table->integer('width')->nullable();
			$table->integer('height')->nullable();
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
		Schema::drop('esign_documents');
	}

}
