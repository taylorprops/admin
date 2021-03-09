<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esign_fields', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('template_id')->nullable();
			$table->integer('envelope_id')->nullable();
			$table->integer('document_id')->nullable();
			$table->integer('signer_id')->nullable();
			$table->bigInteger('field_id')->nullable();
			$table->string('field_type', 15)->nullable();
			$table->text('field_value')->nullable();
			$table->string('required', 5)->nullable();
			$table->integer('page')->nullable();
			$table->decimal('left_perc', 15)->nullable();
			$table->decimal('top_perc', 15)->nullable();
			$table->decimal('height_perc', 15)->nullable();
			$table->decimal('width_perc', 15)->nullable();
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
		Schema::drop('esign_fields');
	}

}
