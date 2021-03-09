<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsListingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_listings', function(Blueprint $table)
		{
			$table->text('Appliances')->nullable()->comment('Appliances');
			$table->decimal('AssociationFee', 14)->nullable()->comment('HOA Fee');
			$table->string('AssociationFeeFrequency', 50)->nullable()->comment('HOA Fee Frequency');
			$table->char('AssociationYN', 1)->nullable()->comment('HOA Y/N');
			$table->char('AttachedGarageYN', 1)->nullable()->comment('Attached Garage Y/N');
			$table->decimal('BasementFinishedPercent', 32)->nullable()->comment('Basement Finished Percent');
			$table->char('BasementYN', 1)->nullable()->comment('Basement Y/N');
			$table->bigInteger('BathroomsTotalInteger')->nullable()->comment('Bathrooms Total Count');
			$table->integer('BedroomsTotal')->nullable()->comment('Bedrooms Count');
			$table->string('BuyerAgentEmail', 80)->nullable()->comment('Buyer Agent Email');
			$table->string('BuyerAgentFirstName', 50)->nullable()->comment('Buyer Agent First Name');
			$table->string('BuyerAgentLastName', 50)->nullable()->comment('Buyer Agent Last Name');
			$table->string('BuyerAgentFullName', 45)->nullable();
			$table->string('BuyerAgentMlsId', 25)->nullable()->comment('Selling Agent MLS ID');
			$table->string('BuyerAgentPreferredPhone', 16)->nullable()->comment('Buyer Agent Preferred Phone');
			$table->string('BuyerOfficeMlsId', 25)->nullable()->comment('Buyer Office MLS ID');
			$table->string('BuyerOfficeName', 50)->nullable()->comment('Buyer Office Name');
			$table->string('BuyerOfficeFullAddress', 145)->nullable();
			$table->string('BuyerOfficeFullStreetAddress', 145)->nullable();
			$table->string('BuyerOfficeCity', 45)->nullable();
			$table->string('BuyerOfficeStateOrProvince', 4)->nullable();
			$table->string('BuyerOfficePostalCode', 15)->nullable();
			$table->string('BuyerOfficePhone', 15)->nullable();
			$table->string('BuyerBothFullName', 45)->nullable();
			$table->string('BuyerOneFullName', 45)->nullable();
			$table->string('BuyerOneFirstName', 45)->nullable();
			$table->string('BuyerOneLastName', 45)->nullable();
			$table->string('BuyerOneEmail', 65)->nullable();
			$table->string('BuyerOneCellPhone', 45)->nullable();
			$table->string('BuyerOneFullAddress', 145)->nullable();
			$table->string('BuyerOneFullStreetAddress', 145)->nullable();
			$table->string('BuyerOneCity', 45)->nullable();
			$table->string('BuyerOneStateOrProvince', 4)->nullable();
			$table->string('BuyerOnePostalCode', 15)->nullable();
			$table->string('BuyerOneCounty', 65)->nullable();
			$table->string('BuyerTwoFullName', 45)->nullable();
			$table->string('BuyerTwoFirstName', 45)->nullable();
			$table->string('BuyerTwoLastName', 45)->nullable();
			$table->string('BuyerTwoEmail', 65)->nullable();
			$table->string('BuyerTwoCellPhone', 45)->nullable();
			$table->string('BuyerTwoFullAddress', 145)->nullable();
			$table->string('BuyerTwoFullStreetAddress', 145)->nullable();
			$table->string('BuyerTwoCity', 45)->nullable();
			$table->string('BuyerTwoStateOrProvince', 4)->nullable();
			$table->string('BuyerTwoPostalCode', 15)->nullable();
			$table->string('BuyerTwoCounty', 65)->nullable();
			$table->string('City', 50)->nullable()->comment('City Name');
			$table->date('CloseDate')->nullable()->comment('Close Date');
			$table->decimal('ClosePrice', 10)->nullable();
			$table->char('CondoYN', 1)->nullable()->comment('Condo/Coop Association Y/N');
			$table->date('ContractDate')->nullable();
			$table->integer('ContractPrice')->nullable();
			$table->text('Cooling')->nullable()->comment('Cooling Type');
			$table->string('County', 45)->nullable()->comment('County');
			$table->string('Deed Reference2', 10)->nullable();
			$table->string('DeedReference1', 10)->nullable();
			$table->string('District', 10)->nullable();
			$table->string('ElementarySchool', 80)->nullable()->comment('Elementary School');
			$table->date('ExpirationDate')->nullable();
			$table->char('FireplaceYN', 1)->nullable()->comment('Fireplace Y/N');
			$table->string('FullStreetAddress', 150)->nullable()->comment('Full Street Address');
			$table->string('FullAddress', 145)->nullable();
			$table->char('GarageYN', 1)->nullable()->comment('Garage Y/N');
			$table->string('Grid', 10)->nullable();
			$table->text('Heating')->nullable()->comment('Heating Type');
			$table->string('HighSchool', 80)->nullable()->comment('High School');
			$table->string('HoaCondoFees', 5)->nullable();
			$table->decimal('Latitude', 12, 8)->nullable()->comment('Latitude');
			$table->decimal('LeaseAmount', 10)->nullable();
			$table->string('LegalDescription1', 150)->nullable();
			$table->string('LegalDescription2', 100)->nullable();
			$table->string('LegalDescription3', 50)->nullable();
			$table->string('ListAgentEmail', 80)->nullable()->comment('List Agent Email');
			$table->string('ListAgentFirstName', 50)->nullable()->comment('List Agent First Name');
			$table->string('ListAgentLastName', 50)->nullable()->comment('List Agent Last Name');
			$table->string('ListAgentFullName', 45)->nullable();
			$table->string('ListAgentMlsId', 25)->nullable()->comment('List Agent MLS ID');
			$table->string('ListAgentPreferredPhone', 16)->nullable()->comment('List Agent Preferred Phone');
			$table->string('ListingId')->nullable()->comment('MLS Number');
			$table->string('ListingSourceRecordKey', 30)->comment('Listing Source Record Key');
			$table->string('ListingTaxID', 50)->nullable()->comment('Tax ID Number');
			$table->string('ListOfficeMlsId', 25)->nullable()->comment('List Office MLS ID');
			$table->string('ListOfficeName', 50)->nullable()->comment('List Office Name');
			$table->string('ListOfficeFullAddress', 145)->nullable();
			$table->string('ListOfficeFullStreetAddress', 145)->nullable();
			$table->string('ListOfficeCity', 45)->nullable();
			$table->string('ListOfficeStateOrProvince', 4)->nullable();
			$table->string('ListOfficePostalCode', 15)->nullable();
			$table->string('ListOfficePhone', 15)->nullable();
			$table->text('ListPictureURL')->nullable()->comment('Ext Main Mid-res');
			$table->integer('ListPrice')->nullable()->comment('List Price');
			$table->integer('LivingArea')->nullable()->comment('Total Finished SQFT');
			$table->decimal('Longitude', 12, 8)->nullable()->comment('Longitude');
			$table->decimal('LotSizeAcres', 14)->nullable()->comment('Lot Size Acres');
			$table->decimal('LotSizeSquareFeet', 14)->nullable()->comment('Lot SQFT');
			$table->dateTime('MajorChangeTimestamp')->nullable()->comment('Major Change Timestamp');
			$table->string('Map', 10)->nullable();
			$table->string('MiddleOrJuniorSchool', 80)->nullable()->comment('Middle Or Junior School');
			$table->date('MLSListDate')->nullable()->comment('Listing Entry Date');
			$table->string('MlsStatus', 50)->nullable()->comment('Status');
			$table->string('MLS_Verified', 45)->nullable();
			$table->char('NewConstructionYN', 1)->nullable()->comment('New Construction Y/N');
			$table->bigInteger('NumAttachedGarageSpaces')->nullable()->comment('# of Attached Garage Spaces');
			$table->bigInteger('NumDetachedGarageSpaces')->nullable()->comment('# of Detached Garage Spaces');
			$table->string('Owner1', 50)->nullable();
			$table->string('Owner2', 50)->nullable();
			$table->string('Parcel', 10)->nullable();
			$table->text('Pool')->nullable()->comment('Pool');
			$table->string('PostalCode', 10)->nullable()->comment('Zip Code');
			$table->string('PropertyEmail', 145)->nullable();
			$table->integer('PropertySubType')->nullable()->comment('Property Sub Type');
			$table->integer('PropertyType')->nullable()->comment('Property Type');
			$table->text('PublicRemarks')->nullable()->comment('Remarks - Public');
			$table->string('ResidenceType', 50)->nullable();
			$table->string('SaleRent', 8)->nullable();
			$table->text('SaleType')->nullable()->comment('Sale Type');
			$table->string('SellerBothFullName', 45)->nullable();
			$table->string('SellerOneFullName', 45)->nullable();
			$table->string('SellerOneFirstName', 45)->nullable();
			$table->string('SellerOneLastName', 45)->nullable();
			$table->string('SellerOneEmail', 65)->nullable();
			$table->string('SellerOneCellPhone', 45)->nullable();
			$table->string('SellerOneFullAddress', 145)->nullable();
			$table->string('SellerOneFullStreetAddress', 145)->nullable();
			$table->string('SellerOneCity', 45)->nullable();
			$table->string('SellerOneStateOrProvince', 4)->nullable();
			$table->string('SellerOnePostalCode', 15)->nullable();
			$table->string('SellerOneCounty', 65)->nullable();
			$table->string('SellerTwoFullName', 45)->nullable();
			$table->string('SellerTwoFirstName', 45)->nullable();
			$table->string('SellerTwoLastName', 45)->nullable();
			$table->string('SellerTwoEmail', 65)->nullable();
			$table->string('SellerTwoCellPhone', 45)->nullable();
			$table->string('SellerTwoFullAddress', 145)->nullable();
			$table->string('SellerTwoFullStreetAddress', 145)->nullable();
			$table->string('SellerTwoCity', 45)->nullable();
			$table->string('SellerTwoStateOrProvince', 4)->nullable();
			$table->string('SellerTwoPostalCode', 15)->nullable();
			$table->string('SellerTwoCounty', 65)->nullable();
			$table->string('Source', 145)->nullable();
			$table->string('StateOrProvince', 50)->nullable()->comment('State Or Province');
			$table->integer('Status')->nullable();
			$table->string('StreetDirPrefix', 50)->nullable()->comment('Street Direction Prefix');
			$table->string('StreetDirSuffix', 50)->nullable()->comment('Street Direction Suffix');
			$table->string('StreetName', 50)->nullable()->comment('Street Name');
			$table->string('StreetNumber', 25)->nullable()->comment('Street Number');
			$table->string('StreetSuffix', 50)->nullable()->comment('Street Suffix');
			$table->string('StreetSuffixModifier', 25)->nullable()->comment('Street Suffix Modifier');
			$table->string('StructureDesignType', 50)->nullable()->comment('Structure Type');
			$table->string('Subdivision Code', 50)->nullable();
			$table->string('SubdivisionName', 50)->nullable()->comment('Subdivision Name');
			$table->string('TaxPropertyType', 45)->nullable();
			$table->text('TaxRecordLink')->nullable();
			$table->integer('TotalPhotos')->nullable()->comment('Photos Count');
			$table->string('TownCode', 50)->nullable();
			$table->string('UnitBuildingType', 50)->nullable()->comment('Unit Building Type');
			$table->string('UnitNumber', 25)->nullable()->comment('Unit Number');
			$table->string('UtilitiesSewage', 50)->nullable();
			$table->string('UtilitiesWater', 50)->nullable();
			$table->integer('YearBuilt')->nullable()->comment('Year Built');
			$table->string('ZoningCode', 50)->nullable();
			$table->bigInteger('Listing_ID', true)->comment('our internal id for the listing');
			$table->integer('Location_ID')->nullable();
			$table->bigInteger('Contract_ID');
			$table->integer('Agent_ID')->comment('Internal Agent id from tbl_agents');
			$table->integer('CoAgent_ID')->nullable()->comment('Internal Agent id from tbl_agents');
			$table->integer('TransactionCoordinator_ID')->nullable();
			$table->integer('Team_ID')->nullable();
			$table->integer('HeritageFinancialUser_ID')->nullable()->comment('Internal LO id from tbl_los');
			$table->integer('HeritageTItleUser_ID')->nullable()->comment('Internal Title Rep id from employees table');
			$table->integer('Commission_ID')->nullable();
			$table->integer('Earnest_ID')->nullable();
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
		Schema::drop('docs_transactions_listings');
	}

}
