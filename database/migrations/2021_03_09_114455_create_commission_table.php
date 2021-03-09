<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('commission_type', 45)->nullable()->default('property')->comment('property, other');
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->decimal('close_price', 15)->nullable();
			$table->date('close_date')->nullable();
			$table->string('both_sides', 4)->nullable();
			$table->string('using_heritage', 4)->nullable();
			$table->string('title_company', 65)->nullable();
			$table->decimal('checks_in_total', 10)->nullable();
			$table->decimal('checks_out_total', 10)->nullable();
			$table->decimal('earnest_deposit_amount', 10)->nullable();
			$table->decimal('income_deductions_total', 10)->nullable();
			$table->decimal('admin_fee_from_client', 10)->nullable();
			$table->decimal('total_income', 10)->nullable();
			$table->integer('agent_commission_percent')->nullable();
			$table->decimal('agent_commission_amount', 10)->nullable();
			$table->decimal('admin_fee_from_agent', 10)->nullable();
			$table->decimal('commission_deductions_total', 10)->nullable();
			$table->decimal('total_commission_to_agent', 10)->nullable();
			$table->decimal('total_left', 10)->nullable();
			$table->string('other_street', 145)->nullable();
			$table->string('other_city', 45)->nullable();
			$table->string('other_state', 5)->nullable();
			$table->string('other_zip', 15)->nullable();
			$table->string('other_client_name', 145)->nullable();
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
		Schema::drop('commission');
	}

}
