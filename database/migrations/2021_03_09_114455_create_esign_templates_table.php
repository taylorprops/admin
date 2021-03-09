<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignTemplatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esign_templates', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('User_ID')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->string('is_system_template', 5)->nullable()->default('no');
			$table->string('is_admin_template', 4)->nullable()->default('no');
			$table->integer('upload_file_id')->nullable();
			$table->text('template_name')->nullable();
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
		Schema::drop('esign_templates');
	}

}
