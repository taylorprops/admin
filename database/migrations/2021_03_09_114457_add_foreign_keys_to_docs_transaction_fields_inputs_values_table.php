<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToDocsTransactionFieldsInputsValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('docs_transaction_fields_inputs_values', function(Blueprint $table)
		{
			$table->foreign('input_id', 'fk_docs_transaction_fields_inputs_values_input_id')->references('id')->on('docs_transaction_fields_inputs')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('docs_transaction_fields_inputs_values', function(Blueprint $table)
		{
			$table->dropForeign('fk_docs_transaction_fields_inputs_values_input_id');
		});
	}

}
