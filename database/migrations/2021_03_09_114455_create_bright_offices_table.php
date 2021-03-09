<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrightOfficesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bright_offices', function(Blueprint $table)
		{
			$table->string('FranchiseAffiliation', 50)->nullable()->comment('FranchiseAffiliation');
			$table->char('IDXOfficeParticipationYN', 1)->nullable()->comment('IDXOfficeParticipationYN');
			$table->bigInteger('MainOfficeKey')->nullable()->comment('MainOfficeKey');
			$table->string('MainOfficeMlsId', 25)->nullable()->comment('MainOfficeMlsId');
			$table->dateTime('ModificationTimestamp')->default('0000-00-00 00:00:00')->comment('ModificationTimestamp');
			$table->string('OfficeAssociationPrimary', 50)->nullable()->comment('OfficeAssociationPrimary');
			$table->string('OfficeAddress1', 50)->nullable()->comment('OfficeAddress1');
			$table->string('OfficeAddress2', 50)->nullable()->comment('OfficeAddress2');
			$table->string('OfficeBoxNumber', 10)->nullable()->comment('OfficeBoxNumber');
			$table->string('OfficeBranchType', 50)->nullable()->comment('OfficeBranchType');
			$table->char('OfficeBrokerAcceptedPortalTermsOfUseYN', 1)->nullable()->comment('OfficeBrokerAcceptedPortalTermsOfUseYN');
			$table->string('OfficeBrokerAcceptedPortalTermsOfUseVersion', 10)->nullable()->comment('OfficeBrokerAcceptedPortalTermsOfUseVersion');
			$table->bigInteger('OfficeBrokerKey')->nullable()->comment('OfficeBrokerKey');
			$table->string('OfficeBrokerLeadEmail', 128)->nullable()->comment('OfficeBrokerLeadEmail');
			$table->string('OfficeBrokerLeadPhoneNumber', 128)->nullable()->comment('OfficeBrokerLeadPhoneNumber');
			$table->string('OfficeBrokerMlsId', 25)->nullable()->comment('OfficeBrokerMlsId');
			$table->string('OfficeCity', 50)->nullable()->comment('OfficeCity');
			$table->string('OfficeCountry', 50)->nullable()->comment('OfficeCountry');
			$table->string('OfficeCounty', 50)->nullable()->comment('OfficeCountyOrParish');
			$table->dateTime('OfficeDateAdded')->default('0000-00-00 00:00:00')->comment('OfficeDateAdded');
			$table->dateTime('OfficeDateTerminated')->default('0000-00-00 00:00:00')->comment('OfficeDateTerminated');
			$table->string('OfficeEmail', 80)->nullable()->comment('OfficeEmail');
			$table->string('OfficeFax', 16)->nullable()->comment('OfficeFax');
			$table->bigInteger('OfficeKey')->primary()->comment('OfficeKey');
			$table->bigInteger('OfficeManagerKey')->nullable()->comment('OfficeManagerKey');
			$table->decimal('OfficeLatitude', 17, 12)->nullable()->comment('OfficeLatitude');
			$table->char('OfficeLeadToListingAgentYN', 1)->nullable()->comment('OfficeLeadToListingAgentYN');
			$table->decimal('OfficeLongitude', 17, 12)->nullable()->comment('OfficeLongitude');
			$table->text('OfficeManagerEmail')->nullable()->comment('OfficeManagerEmail');
			$table->string('OfficeManagerMlsId', 25)->nullable()->comment('OfficeManagerMlsId');
			$table->string('OfficeManagerName', 80)->nullable()->comment('OfficeManagerName');
			$table->string('OfficeMlsId', 25)->nullable()->comment('OfficeMlsId');
			$table->string('OfficeName', 80)->nullable()->comment('OfficeName');
			$table->string('OfficeNationalAssociationId', 25)->nullable()->comment('OfficeNationalAssociationId');
			$table->integer('OfficeNumViolations')->nullable()->comment('OfficeNumViolations');
			$table->string('OfficePhone', 16)->nullable()->comment('OfficePhone');
			$table->integer('OfficePhoneExt')->nullable()->comment('OfficePhoneExt');
			$table->string('OfficePhoneOther', 10)->nullable()->comment('OfficePhoneOther');
			$table->string('OfficePostalCode', 10)->nullable()->comment('OfficePostalCode');
			$table->string('OfficePostalCodePlus4', 4)->nullable()->comment('OfficePostalCodePlus4');
			$table->text('OfficeRoleList')->nullable()->comment('OfficeRoleList');
			$table->string('OfficeStateOrProvince', 50)->nullable()->comment('OfficeStateOrProvince');
			$table->string('OfficeStatus', 50)->nullable()->comment('OfficeStatus');
			$table->string('OfficeStreetDirSuffix', 50)->nullable()->comment('OfficeStreetDirSuffix');
			$table->string('OfficeStreetException', 10)->nullable()->comment('OfficeStreetException');
			$table->string('OfficeStreetName', 50)->nullable()->comment('OfficeStreetName');
			$table->integer('OfficeStreetNumber')->nullable()->comment('OfficeStreetNumber');
			$table->string('OfficeStreetSuffix', 50)->nullable()->comment('OfficeStreetSuffix');
			$table->string('OfficeTradingAs', 50)->nullable()->comment('OfficeTradingAs');
			$table->string('OfficeType', 50)->nullable()->comment('OfficeType');
			$table->string('OfficeUnitDesignation', 50)->nullable()->comment('OfficeUnitDesignation');
			$table->string('OfficeUnitNumber', 20)->nullable()->comment('OfficeUnitNumber');
			$table->string('OfficeUserName', 30)->nullable()->comment('OfficeUserName');
			$table->string('OfficeSubSystemLocale', 50)->nullable()->comment('OfficeSubSystemLocale');
			$table->string('OfficeSystemLocale', 50)->nullable()->comment('OfficeSystemLocale');
			$table->text('SocialMediaBlogUrlOrId')->nullable()->comment('SocialMediaBlogUrlOrId');
			$table->text('SocialMediaFacebookUrlOrId')->nullable()->comment('SocialMediaFacebookUrlOrId');
			$table->text('SocialMediaLinkedInUrlOrId')->nullable()->comment('SocialMediaLinkedInUrlOrId');
			$table->text('SocialMediaTwitterUrlOrId')->nullable()->comment('SocialMediaTwitterUrlOrId');
			$table->text('SocialMediaWebsiteUrlOrId')->nullable()->comment('SocialMediaWebsiteUrlOrId');
			$table->text('SocialMediaYouTubeUrlOrId')->nullable()->comment('SocialMediaYouTubeUrlOrId');
			$table->string('OfficeSourceBusinessPartner', 50)->nullable()->comment('OfficeSourceBusinessPartner');
			$table->string('OfficeSourceRecordKey')->nullable()->comment('OfficeSourceRecordKey');
			$table->string('SyndicateAgentOption', 50)->nullable()->comment('SyndicateAgentOption');
			$table->text('SyndicateTo')->nullable()->comment('SyndicateTo');
			$table->string('OfficeSourceRecordID', 128)->nullable()->comment('OfficeSourceRecordID');
			$table->char('OfficeBrightConvertedYN', 1)->nullable()->comment('OfficeBrightConvertedYN');
			$table->string('OfficeSourceInput', 50)->nullable()->comment('OfficeSourceInput');
			$table->string('OfficeSourceTransport', 50)->nullable()->comment('OfficeSourceTransport');
			$table->string('MainOfficeName', 50)->nullable()->comment('MainOfficeName');
			$table->text('OfficeAssociationsFullList')->nullable()->comment('OfficeAssociationsFullList');
			$table->string('OfficeValidationStatus', 50)->nullable()->comment('Office Validation Status');
			$table->string('OfficeCorporateLicense', 50)->nullable()->comment('Brokerage Office License');
			$table->dateTime('SourceModificationTimestamp')->default('0000-00-00 00:00:00')->comment('SourceModificationTimestamp');
			$table->string('OfficeStreetDirPrefix', 50)->nullable()->comment('OfficeStreetDirPrefix');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bright_offices');
	}

}
