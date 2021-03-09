<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsCreateFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_create_fields', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('file_id')->nullable()->index('fk_fields_file_id');
			$table->integer('common_field_id')->nullable();
			$table->bigInteger('field_id')->nullable();
			$table->bigInteger('group_id')->nullable();
			$table->integer('page')->nullable();
			$table->string('field_category', 45)->nullable();
			$table->string('field_type', 10)->nullable();
			$table->string('field_name', 65)->nullable();
			$table->string('field_name_display', 85)->nullable();
			$table->string('field_name_type', 45)->nullable();
			$table->string('number_type', 15)->nullable();
			$table->integer('field_sub_group_id')->nullable();
			$table->decimal('top_perc', 15, 10)->nullable();
			$table->decimal('left_perc', 15, 10)->nullable();
			$table->decimal('width_perc', 15, 10)->nullable();
			$table->decimal('height_perc', 15, 10)->nullable();
			$table->softDeletes();
			$table->integer('Listing_ID')->nullable();
			$table->integer('Contract_ID')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_create_fields');
	}

}
