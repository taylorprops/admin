<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsCreateCommonFieldsSubGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_create_common_fields_sub_groups', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('group_id')->index('fk_docs_create_common_fields_sub_groups_group_idx');
			$table->string('sub_group_name', 85)->nullable();
			$table->integer('sub_group_order')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_create_common_fields_sub_groups');
	}

}
