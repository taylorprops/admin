<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsChecklistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_checklists', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('checklist_represent', 45)->nullable();
			$table->string('checklist_type', 15)->nullable();
			$table->integer('checklist_property_type_id');
			$table->integer('checklist_property_sub_type_id')->default(0);
			$table->string('checklist_sale_rent', 15)->nullable();
			$table->string('checklist_state', 85)->nullable();
			$table->integer('checklist_location_id')->nullable()->index('fk_checklists_resource_id');
			$table->integer('checklist_order')->nullable();
			$table->integer('checklist_count')->nullable();
			$table->string('checklist_active', 5)->nullable()->default('yes');
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
		Schema::drop('docs_checklists');
	}

}
