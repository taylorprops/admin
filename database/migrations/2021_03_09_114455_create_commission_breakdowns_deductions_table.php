<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionBreakdownsDeductionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commission_breakdowns_deductions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('commission_breakdown_id');
			$table->text('description')->nullable();
			$table->decimal('amount', 10)->nullable();
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
		Schema::drop('commission_breakdowns_deductions');
	}

}
