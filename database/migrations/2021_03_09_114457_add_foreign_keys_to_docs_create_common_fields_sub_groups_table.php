<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDocsCreateCommonFieldsSubGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('docs_create_common_fields_sub_groups', function(Blueprint $table)
		{
			$table->foreign('group_id', 'fk_docs_create_common_fields_sub_groups_group')->references('id')->on('docs_create_common_fields_groups')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('docs_create_common_fields_sub_groups', function(Blueprint $table)
		{
			$table->dropForeign('fk_docs_create_common_fields_sub_groups_group');
		});
	}

}
