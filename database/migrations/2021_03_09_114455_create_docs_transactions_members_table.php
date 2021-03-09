<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_members', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('member_type_id')->nullable();
			$table->string('active', 5)->nullable()->default('yes');
			$table->string('first_name', 45)->nullable();
			$table->string('last_name', 45)->nullable();
			$table->string('entity_name', 145)->nullable();
			$table->string('email', 45)->nullable();
			$table->string('cell_phone', 15)->nullable();
			$table->string('company', 85)->nullable();
			$table->string('address_home_street', 65)->nullable();
			$table->string('address_home_unit', 15)->nullable();
			$table->string('address_home_city', 45)->nullable();
			$table->string('address_home_state', 5)->nullable();
			$table->string('address_home_zip', 15)->nullable();
			$table->string('address_office_street', 65)->nullable();
			$table->string('address_office_city', 45)->nullable();
			$table->string('address_office_state', 4)->nullable();
			$table->string('address_office_zip', 15)->nullable();
			$table->boolean('disabled')->nullable()->default(0);
			$table->integer('bright_mls_id')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->string('transaction_type', 45)->nullable();
			$table->bigInteger('Listing_ID')->nullable();
			$table->bigInteger('Contract_ID')->nullable();
			$table->integer('CRMContact_ID')->nullable();
			$table->timestamps(10);
			$table->bigInteger('Referral_ID')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_transactions_members');
	}

}
