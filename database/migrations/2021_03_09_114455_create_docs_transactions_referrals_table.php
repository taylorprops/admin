<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsReferralsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_referrals', function(Blueprint $table)
		{
			$table->decimal('AgentCommission', 10)->nullable();
			$table->string('City', 50)->nullable()->comment('City Name');
			$table->string('ClientFirstName', 45)->nullable();
			$table->string('ClientLastName', 45)->nullable();
			$table->string('ClientPhone', 45)->nullable();
			$table->string('ClientStreet', 45)->nullable();
			$table->string('ClientCity', 45)->nullable();
			$table->string('ClientState', 45)->nullable();
			$table->string('ClientZip', 45)->nullable();
			$table->date('CloseDate')->nullable()->comment('Close Date');
			$table->decimal('CommissionAmount', 10)->nullable();
			$table->integer('ContractPrice')->nullable();
			$table->string('County', 50)->nullable()->comment('County');
			$table->string('FullStreetAddress', 80)->nullable()->comment('Full Street Address');
			$table->string('ListingId')->nullable()->comment('MLS Number');
			$table->decimal('OtherAgentCommission', 10)->nullable();
			$table->string('PostalCode', 10)->nullable()->comment('Zip Code');
			$table->string('PropertyEmail', 145)->nullable();
			$table->string('ReceivingAgentEmail', 80)->nullable()->comment('Receiving Agent Email');
			$table->string('ReceivingAgentFirstName', 50)->nullable()->comment('Receiving Agent First Name');
			$table->string('ReceivingAgentLastName', 50)->nullable()->comment('Receiving Agent Last Name');
			$table->string('ReceivingAgentPreferredPhone', 16)->nullable()->comment('Receiving Agent Preferred Phone');
			$table->string('ReceivingAgentOfficeName', 50)->nullable()->comment('Receiving Office Name');
			$table->string('ReceivingAgentOfficeStreet', 65)->nullable();
			$table->string('ReceivingAgentOfficeCity', 50)->nullable()->comment('Receiving Office City');
			$table->string('ReceivingAgentOfficeState', 50)->nullable()->comment('Receiving Office State');
			$table->string('ReceivingAgentOfficeZip', 50)->nullable()->comment('Receiving Office Zip');
			$table->string('ReceivingAgentOfficePhone', 45)->nullable();
			$table->integer('ReferralPercentage')->nullable();
			$table->string('SaleRent', 8)->nullable();
			$table->string('StateOrProvince', 50)->nullable()->comment('State Or Province');
			$table->string('StreetDirPrefix', 15)->nullable();
			$table->integer('Status')->nullable();
			$table->string('StreetName', 65)->nullable();
			$table->string('StreetNumber', 15)->nullable();
			$table->string('StreetSuffix', 45)->nullable();
			$table->string('StreetDirSuffix', 45)->nullable();
			$table->string('UnitNumber', 15)->nullable();
			$table->bigInteger('Referral_ID', true);
			$table->integer('Agent_ID');
			$table->integer('TransactionCoordinator_ID')->nullable();
			$table->integer('Location_ID')->nullable();
			$table->integer('Commission_ID')->nullable();
			$table->integer('DocsMissingCount')->nullable();
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
		Schema::drop('docs_transactions_referrals');
	}

}
