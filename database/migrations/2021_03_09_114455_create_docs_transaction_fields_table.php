<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionFieldsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transaction_fields', function(Blueprint $table)
		{
			$table->bigInteger('id', true);
			$table->integer('file_id')->nullable();
			$table->integer('common_field_id')->nullable();
			$table->bigInteger('create_field_id')->nullable();
			$table->bigInteger('group_id')->nullable();
			$table->integer('page')->nullable();
			$table->string('field_category', 45)->nullable();
			$table->string('field_type', 10)->nullable();
			$table->string('field_created_by', 10)->nullable()->default('system');
			$table->string('field_name', 65)->nullable();
			$table->string('field_name_display', 85)->nullable();
			$table->string('field_name_type', 45)->nullable()->default('custom');
			$table->string('number_type', 15)->nullable();
			$table->integer('field_sub_group_id')->nullable();
			$table->decimal('top_perc', 15, 10)->nullable();
			$table->decimal('left_perc', 15, 10)->nullable();
			$table->decimal('width_perc', 15, 10)->nullable();
			$table->decimal('height_perc', 15, 10)->nullable();
			$table->string('field_inputs', 45)->nullable()->default('no');
			$table->string('file_type', 45)->nullable();
			$table->integer('Agent_ID');
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
		Schema::drop('docs_transaction_fields');
	}

}
