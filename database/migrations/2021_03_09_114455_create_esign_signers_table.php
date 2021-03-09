<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignSignersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esign_signers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('template_id')->nullable();
			$table->integer('envelope_id')->nullable();
			$table->string('signer_name', 45)->nullable();
			$table->string('signer_email', 65)->nullable();
			$table->integer('signer_order')->nullable();
			$table->string('signer_role', 25)->nullable();
			$table->string('template_role', 45)->nullable();
			$table->string('recipient_only', 5)->nullable();
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
		Schema::drop('esign_signers');
	}

}
