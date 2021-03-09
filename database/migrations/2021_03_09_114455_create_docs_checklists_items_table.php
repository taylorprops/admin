<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsChecklistsItemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_checklists_items', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('checklist_id')->nullable()->index('fk_checklists_items_checklist_id');
			$table->integer('checklist_form_id')->nullable();
			$table->string('checklist_item_required', 3)->nullable();
			$table->integer('checklist_item_group_id')->nullable();
			$table->integer('checklist_item_order')->nullable();
			$table->string('checklist_item_active', 5)->nullable()->default('yes');
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
		Schema::drop('docs_checklists_items');
	}

}
