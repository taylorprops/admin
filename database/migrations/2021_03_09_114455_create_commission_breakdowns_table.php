<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionBreakdownsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission_breakdowns', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->bigInteger('Contract_ID')->nullable();
			$table->bigInteger('Referral_ID')->nullable();
			$table->string('transaction_type', 15)->nullable();
			$table->integer('Agent_ID')->nullable();
			$table->integer('Commission_ID');
			$table->decimal('checks_in_total', 10)->nullable();
			$table->decimal('admin_fee_in_total', 10)->nullable();
			$table->decimal('earnest_deposit_amount', 10)->nullable();
			$table->decimal('total_income', 10)->nullable();
			$table->decimal('admin_fee_from_client', 10)->nullable();
			$table->decimal('sub_total', 10)->nullable();
			$table->decimal('agent_commission_deduction', 10)->nullable();
			$table->decimal('admin_fee_from_agent', 10)->nullable();
			$table->string('add_fedex', 5)->nullable();
			$table->decimal('referral_company_deduction', 10)->nullable();
			$table->decimal('commission_deductions_total', 10)->nullable();
			$table->decimal('total_commission_to_agent', 10)->nullable();
			$table->string('check_payable_to', 45)->nullable();
			$table->string('delivery_method', 45)->nullable();
			$table->string('check_mail_to_street', 65)->nullable();
			$table->string('check_mail_to_city', 45)->nullable();
			$table->string('check_mail_to_state', 5)->nullable();
			$table->string('check_mail_to_zip', 15)->nullable();
			$table->text('notes')->nullable();
			$table->string('submitted', 5)->nullable()->default('no');
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
		Schema::drop('commission_breakdowns');
	}

}
