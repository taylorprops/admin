<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionFieldsInputsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transaction_fields_inputs', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('file_id')->nullable();
			$table->bigInteger('group_id')->nullable();
			$table->string('file_type', 15)->nullable();
			$table->string('field_type', 45)->nullable();
			$table->string('number_type', 45)->nullable();
			$table->bigInteger('transaction_field_id')->nullable();
			$table->string('input_name_display', 85)->nullable();
			$table->text('input_value')->nullable();
			$table->string('input_db_column', 45)->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->bigInteger('Listing_ID')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->string('transaction_type', 45)->nullable();
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
		Schema::drop('docs_transaction_fields_inputs');
	}

}
