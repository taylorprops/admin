<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionFieldsInputsValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transaction_fields_inputs_values', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->bigInteger('input_id')->nullable()->index('fk_docs_transaction_fields_inputs_values_input_id_idx');
			$table->string('common_name', 45)->nullable();
			$table->string('field_type', 45)->nullable();
			$table->integer('file_id')->nullable();
			$table->string('file_type', 45)->nullable()->comment('system or user - file_id can be same for docs_create_fields_inputs and docs_transactions_fields_inputs');
			$table->integer('transaction_field_id')->nullable();
			$table->text('input_value')->nullable();
			$table->string('input_name', 85)->nullable();
			$table->bigInteger('Listing_ID')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->string('transaction_type', 45)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_transaction_fields_inputs_values');
	}

}
