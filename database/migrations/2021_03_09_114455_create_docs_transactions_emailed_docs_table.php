<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsEmailedDocsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_emailed_docs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('Agent_ID');
			$table->bigInteger('Listing_ID');
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->text('file_name_display');
			$table->text('file_location');
			$table->string('transaction_type', 45)->nullable();
			$table->string('active', 5)->nullable()->default('yes');
			$table->string('email_status', 15)->nullable()->default('success');
			$table->text('fail_reason')->nullable();
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
		Schema::drop('docs_transactions_emailed_docs');
	}

}
