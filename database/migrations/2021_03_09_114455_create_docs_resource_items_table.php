<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsResourceItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_resource_items', function(Blueprint $table)
		{
			$table->integer('resource_id', true);
			$table->string('resource_type', 50)->nullable();
			$table->string('resource_type_title', 50)->nullable();
			$table->text('resource_name')->nullable();
			$table->integer('resource_order')->nullable();
			$table->string('resource_state', 5)->nullable();
			$table->string('resource_color', 15)->nullable();
			$table->string('resource_association', 5)->nullable();
			$table->string('resource_county_abbr', 45)->nullable();
			$table->string('resource_addendums', 4)->nullable();
			$table->string('resource_active', 5)->nullable()->default('yes');
			$table->string('resource_form_group_type', 15)->nullable();
			$table->string('resource_account_number', 45)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_resource_items');
	}

}
