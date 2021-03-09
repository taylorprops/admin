<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsCreateFieldsInputsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_create_fields_inputs', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('file_id')->nullable()->index('fk_fields_inputs_file_id');
			$table->string('field_id', 100)->nullable();
			$table->bigInteger('input_id')->nullable();
			$table->string('input_name', 85)->nullable();
			$table->text('input_helper_text')->nullable();
			$table->string('field_type', 8)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_create_fields_inputs');
	}

}
