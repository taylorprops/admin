<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsCreateUploadsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_create_uploads', function(Blueprint $table)
		{
			$table->integer('file_id', true);
			$table->integer('template_id')->nullable();
			$table->text('file_location')->nullable();
			$table->text('file_name')->nullable();
			$table->text('file_name_orig')->nullable();
			$table->text('file_name_display')->nullable();
			$table->string('form_tags', 45)->nullable();
			$table->integer('pages_total')->nullable();
			$table->string('form_categories', 45)->nullable();
			$table->string('state', 3)->nullable();
			$table->integer('checklist_group_id')->nullable();
			$table->integer('form_group_id')->nullable();
			$table->text('helper_text')->nullable();
			$table->string('published', 3)->nullable()->default('no');
			$table->string('active', 5)->nullable()->default('yes');
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
		Schema::drop('docs_create_uploads');
	}

}
