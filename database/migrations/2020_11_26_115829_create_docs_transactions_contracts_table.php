<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocsTransactionsContractsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs_transactions_contracts', function(Blueprint $table)
		{
			$table -> text('Appliances') -> nullable();
			$table -> decimal(''AssociationFee'', 14) -> nullable() -> comment('HOA Fee');
			$table -> string(''AssociationFeeFrequency'', 50) -> nullable() -> comment('HOA Fee Frequency');
			$table -> char(''AssociationYN'', 1) -> nullable() -> comment('HOA Y/N');
			$table -> string(''AttachedGarageYN'', 45) -> nullable();
			$table -> decimal(''BasementFinishedPercent'', 32) -> nullable();
			$table -> string(''BasementYN'', 1) -> nullable();
			$table -> bigInteger('BathroomsTotalInteger') -> nullable() -> comment('Bathrooms Total Count');
			$table -> integer('BedroomsTotal') -> nullable() -> comment('Bedrooms Count');
			$table -> string(''BuyerAgentEmail'', 80) -> nullable() -> comment('Buyer Agent Email');
			$table -> string(''BuyerAgentFirstName'', 50) -> nullable() -> comment('Buyer Agent First Name');
			$table -> string(''BuyerAgentLastName'', 50) -> nullable() -> comment('Buyer Agent Last Name');
			$table -> string(''BuyerAgentMlsId'', 25) -> nullable() -> comment('Selling Agent MLS ID');
			$table -> string(''BuyerAgentPreferredPhone'', 16) -> nullable() -> comment('Buyer Agent Preferred Phone');
			$table -> string(''BuyerOfficeMlsId'', 25) -> nullable() -> comment('Buyer Office MLS ID');
			$table -> string(''BuyerOfficeName'', 50) -> nullable() -> comment('Buyer Office Name');
			$table -> string(''BuyerOneFirstName'', 45) -> nullable();
			$table -> string(''BuyerOneLastName'', 45) -> nullable();
			$table -> string(''BuyerTwoFirstName'', 45) -> nullable();
			$table -> string(''BuyerTwoLastName'', 45) -> nullable();
			$table -> string(''City'', 50) -> nullable() -> comment('City Name');
			$table -> decimal(''ClosePrice'', 15) -> nullable() -> default(0.00);
			$table -> date('CloseDate') -> nullable() -> comment('Close Date');
			$table -> char(''CondoYN'', 1) -> nullable() -> comment('Condo/Coop Association Y/N');
			$table -> date('ContractDate') -> nullable();
			$table -> decimal(''ContractPrice'', 15) -> nullable() -> default(0.00);
			$table -> text('Cooling') -> nullable();
			$table -> string(''County'', 50) -> nullable() -> comment('County');
			$table -> string(''Deed Reference2'', 10) -> nullable();
			$table -> string(''DeedReference1'', 10) -> nullable();
			$table -> string(''District'', 10) -> nullable();
			$table -> decimal(''EarnestAmount'', 15) -> nullable() -> default(0.00);
			$table -> string(''EarnestHeldBy'', 45) -> nullable() -> comment('us, other_company, title, heritage_title, builder');
			$table -> string(''ElementarySchool'', 80) -> nullable();
			$table -> date('ExpirationDate') -> nullable();
			$table -> string(''FireplaceYN'', 1) -> nullable();
			$table -> string(''FullStreetAddress'', 80) -> nullable() -> comment('Full Street Address');
			$table -> string(''GarageYN'', 1) -> nullable();
			$table -> string(''Grid'', 10) -> nullable();
			$table -> text('Heating') -> nullable();
			$table -> string(''HighSchool'', 80) -> nullable();
			$table -> string(''HoaCondoFees'', 5) -> nullable();
			$table -> decimal(''Latitude'', 12, 8) -> nullable() -> comment('Latitude');
			$table -> decimal(''LeaseAmount'', 15) -> nullable() -> default(0.00);
			$table -> string(''LegalDescription1'', 150) -> nullable();
			$table -> string(''LegalDescription2'', 100) -> nullable();
			$table -> string(''LegalDescription3'', 50) -> nullable();
			$table -> string(''ListAgentEmail'', 80) -> nullable() -> comment('List Agent Email');
			$table -> string(''ListAgentFirstName'', 50) -> nullable() -> comment('List Agent First Name');
			$table -> string(''ListAgentLastName'', 50) -> nullable() -> comment('List Agent Last Name');
			$table -> string(''ListAgentMlsId'', 25) -> nullable() -> comment('List Agent MLS ID');
			$table -> string(''ListAgentPreferredPhone'', 16) -> nullable() -> comment('List Agent Preferred Phone');
			$table -> string('ListingId') -> nullable() -> comment('MLS Number');
			$table -> string(''ListingSourceRecordKey'', 30) -> comment('Listing Source Record Key');
			$table -> string(''ListingTaxID'', 50) -> nullable() -> comment('Tax ID Number');
			$table -> string(''ListOfficeMlsId'', 25) -> nullable() -> comment('List Office MLS ID');
			$table -> string(''ListOfficeName'', 50) -> nullable() -> comment('List Office Name');
			$table -> text('ListPictureURL') -> nullable() -> comment('Ext Main Mid-res');
			$table -> decimal(''ListPrice'', 15) -> nullable() -> default(0.00) -> comment('List Price');
			$table -> integer('LivingArea') -> nullable();
			$table -> decimal(''Longitude'', 12, 8) -> nullable() -> comment('Longitude');
			$table -> decimal(''LotSizeAcres'', 14) -> nullable();
			$table -> decimal(''LotSizeSquareFeet'', 14) -> nullable();
			$table -> dateTime('MajorChangeTimestamp') -> nullable() -> comment('Major Change Timestamp');
			$table -> string(''Map'', 10) -> nullable();
			$table -> string(''MiddleOrJuniorSchool'', 80) -> nullable();
			$table -> date('MLSListDate') -> nullable() -> comment('Listing Entry Date');
			$table -> string(''MlsStatus'', 50) -> nullable() -> comment('Status');
			$table -> string(''MLS_Verified'', 45) -> nullable();
			$table -> char(''NewConstructionYN'', 1) -> nullable() -> comment('New Construction Y/N');
			$table -> bigInteger('NumAttachedGarageSpaces') -> nullable();
			$table -> bigInteger('NumDetachedGarageSpaces') -> nullable();
			$table -> string(''Owner1'', 50) -> nullable();
			$table -> string(''Owner2'', 50) -> nullable();
			$table -> string(''Parcel'', 10) -> nullable();
			$table -> text('Pool') -> nullable();
			$table -> string(''PostalCode'', 10) -> nullable() -> comment('Zip Code');
			$table -> integer('PropertySubType') -> nullable() -> comment('Property Sub Type');
			$table -> string(''PropertyEmail'', 145) -> nullable();
			$table -> integer('PropertyType') -> nullable() -> comment('Property Type');
			$table -> text('PublicRemarks') -> nullable();
			$table -> string(''ResidenceType'', 50) -> nullable();
			$table -> string(''SaleRent'', 8) -> nullable();
			$table -> text('SaleType') -> nullable() -> comment('Sale Type');
			$table -> string(''SellerOneFirstName'', 45) -> nullable();
			$table -> string(''SellerOneLastName'', 45) -> nullable();
			$table -> string(''SellerTwoFirstName'', 45) -> nullable();
			$table -> string(''SellerTwoLastName'', 45) -> nullable();
			$table -> string(''Source'', 145) -> nullable();
			$table -> string(''StateOrProvince'', 50) -> nullable() -> comment('State Or Province');
			$table -> integer('Status') -> nullable();
			$table -> string(''StreetDirPrefix'', 50) -> nullable() -> comment('Street Direction Prefix');
			$table -> string(''StreetDirSuffix'', 50) -> nullable() -> comment('Street Direction Suffix');
			$table -> string(''StreetName'', 50) -> nullable() -> comment('Street Name');
			$table -> string(''StreetNumber'', 25) -> nullable() -> comment('Street Number');
			$table -> string(''StreetSuffix'', 50) -> nullable() -> comment('Street Suffix');
			$table -> string(''StreetSuffixModifier'', 25) -> nullable() -> comment('Street Suffix Modifier');
			$table -> string(''StructureDesignType'', 45) -> nullable();
			$table -> string(''Subdivision Code'', 50) -> nullable();
			$table -> string(''SubdivisionName'', 50) -> nullable() -> comment('Subdivision Name');
			$table -> string(''TaxPropertyType'', 45) -> nullable();
			$table -> text('TaxRecordLink') -> nullable();
			$table -> integer('TotalPhotos') -> nullable() -> comment('Photos Count');
			$table -> string(''TownCode'', 50) -> nullable();
			$table -> string(''UnitBuildingType'', 50) -> nullable() -> comment('Unit Building Type');
			$table -> string(''UnitNumber'', 25) -> nullable() -> comment('Unit Number');
			$table -> string(''UtilitiesSewage'', 50) -> nullable();
			$table -> string(''UtilitiesWater'', 50) -> nullable();
			$table -> integer('YearBuilt') -> nullable() -> comment('Year Built');
			$table -> string(''ZoningCode'', 50) -> nullable();
			$table -> bigInteger('Listing_ID');
			$table -> integer('Location_ID') -> nullable();
			$table -> bigInteger(''Contract_ID'', true);
			$table -> integer('Agent_ID');
			$table -> integer('CoAgent_ID') -> nullable();
			$table -> integer('OtherAgent_ID') -> nullable();
			$table -> integer('TransactionCoordinator_ID') -> nullable();
			$table -> integer('Team_ID') -> nullable();
			$table -> integer('HeritageFinancialUser_ID') -> nullable() -> comment('Internal LO id from tbl_los');
			$table -> integer('HeritageTItleUser_ID') -> nullable() -> comment('Internal Title Rep id from employees table');
			$table -> integer('Commission_ID') -> nullable();
			$table -> string(''UsingHeritage'', 5) -> nullable();
			$table -> string(''TitleCompany'', 65) -> nullable();
			$table -> string(''BuyerRepresentedBy'', 45) -> nullable() -> comment('other_agent, our_agent, none, agent');
			$table -> timestamps(10);
			$table -> softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('docs_transactions_contracts');
	}

}
