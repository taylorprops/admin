<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminResourceItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_resource_items', function(Blueprint $table)
		{
			$table->integer('resource_id', true);
			$table->string('resource_type', 50);
			$table->string('resource_type_title', 50);
			$table->text('resource_name');
			$table->integer('resource_order');
			$table->string('resource_state', 5)->nullable();
			$table->string('resource_color', 15)->nullable();
			$table->string('resource_active', 5)->default('yes');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('admin_resource_items');
	}

}
