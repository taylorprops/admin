<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrmContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_contacts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('Agent_ID')->nullable();
			$table->integer('contact_type_id')->nullable();
			$table->string('contact_active', 5)->nullable()->default('yes');
			$table->string('contact_first', 25)->nullable();
			$table->string('contact_last', 25)->nullable();
			$table->string('contact_phone_cell', 15)->nullable();
			$table->string('contact_phone_home', 15)->nullable();
			$table->string('contact_email', 45)->nullable();
			$table->string('contact_street', 100)->nullable();
			$table->string('contact_city', 45)->nullable();
			$table->string('contact_state', 4)->nullable();
			$table->string('contact_unit', 45)->nullable();
			$table->string('contact_company', 45)->nullable();
			$table->string('contact_zip', 15)->nullable();
			$table->timestamps(10);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_contacts');
	}

}
