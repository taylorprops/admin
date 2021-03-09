<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignCallbacksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esign_callbacks', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('event_time');
			$table->string('event_type', 30)->nullable();
			$table->text('event_hash')->nullable();
			$table->text('related_document_hash')->nullable();
			$table->integer('related_user_id');
			$table->integer('signer_id');
			$table->string('signer_name', 50)->nullable();
			$table->string('signer_email', 50)->nullable();
			$table->string('signer_role', 50)->nullable();
			$table->integer('signer_order');
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
		Schema::drop('esign_callbacks');
	}

}
