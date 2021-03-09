<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsCreateUploadsImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_create_uploads_images', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('file_id')->nullable()->index('fk_images_file_id');
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
		Schema::drop('docs_create_uploads_images');
	}

}
