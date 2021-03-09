<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmpAgentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('emp_agents', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('active', 5)->nullable()->default('yes');
			$table->date('start_date')->nullable();
			$table->string('first_name', 45)->nullable()->comment('	');
			$table->string('middle_name', 45)->nullable();
			$table->string('last_name', 45)->nullable();
			$table->string('suffix', 45)->nullable();
			$table->string('full_name', 145)->nullable();
			$table->integer('dob_day')->nullable();
			$table->integer('dob_month')->nullable();
			$table->string('social_security', 15)->nullable();
			$table->string('email', 45)->nullable();
			$table->string('cell_phone', 15)->nullable();
			$table->string('home_phone', 15)->nullable();
			$table->string('address_street', 65)->nullable();
			$table->string('address_unit', 15)->nullable();
			$table->string('address_city', 45)->nullable();
			$table->string('address_state', 5)->nullable();
			$table->string('address_zip', 15)->nullable();
			$table->string('address_county', 45)->nullable();
			$table->string('company', 45)->nullable();
			$table->integer('team_id')->nullable()->default(0);
			$table->integer('commission_percent')->nullable();
			$table->string('commission_plan', 5)->nullable();
			$table->text('photo_location')->nullable();
			$table->string('bright_mls_id_md_dc_tp', 45)->nullable();
			$table->string('bright_mls_id_va_tp', 45)->nullable();
			$table->string('bright_mls_id_md_aap', 45)->nullable();
			$table->string('llc_name', 45)->nullable();
			$table->string('owe_other', 5)->nullable();
			$table->text('owe_other_notes')->nullable();
			$table->text('bill_cycle')->nullable();
			$table->decimal('bill_amount', 10)->nullable();
			$table->decimal('admin_fee', 10)->nullable();
			$table->decimal('admin_fee_rentals', 10)->nullable();
			$table->decimal('balance', 10)->nullable();
			$table->decimal('balance_eno', 10)->nullable();
			$table->decimal('balance_rent', 10)->nullable();
			$table->string('auto_bill', 5)->nullable();
			$table->string('ein', 45)->nullable();
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
		Schema::drop('emp_agents');
	}

}
