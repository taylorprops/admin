<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignDocumentsImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esign_documents_images', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('envelope_id')->nullable();
			$table->integer('document_id')->nullable();
			$table->text('image_location')->nullable();
			$table->integer('page_number')->nullable();
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
		Schema::drop('esign_documents_images');
	}

}
