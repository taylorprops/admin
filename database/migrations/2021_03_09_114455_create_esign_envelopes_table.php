<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsignEnvelopesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('esign_envelopes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('document_hash', 45)->nullable();
			$table->string('status', 15)->nullable()->default('not_sent');
			$table->integer('User_ID')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->string('transaction_type', 15)->nullable();
			$table->bigInteger('Listing_ID')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->text('file_location')->nullable();
			$table->text('subject')->nullable();
			$table->text('message')->nullable();
			$table->string('is_draft', 5)->nullable()->default('no');
			$table->text('draft_name')->nullable();
			$table->string('is_template', 5)->nullable()->default('no');
			$table->string('is_system_template', 4)->nullable()->default('no');
			$table->string('is_admin_template', 4)->nullable()->default('no');
			$table->integer('template_id')->nullable();
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
		Schema::drop('esign_envelopes');
	}

}
