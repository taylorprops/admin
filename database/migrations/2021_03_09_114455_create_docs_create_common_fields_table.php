<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsCreateCommonFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_create_common_fields', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('field_name', 85)->nullable();
			$table->string('field_type', 45)->nullable();
			$table->integer('field_order')->nullable();
			$table->string('db_column_name', 85)->nullable();
			$table->integer('group_id')->nullable()->index('fk_docs_create_common_fields_group_idx');
			$table->integer('sub_group_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_create_common_fields');
	}

}
