<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrightAgentRosterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bright_agent_roster', function(Blueprint $table)
		{
			$table->string('JobTitle', 50)->nullable()->comment('JobTitle');
			$table->string('MemberAddress1', 50)->nullable()->comment('MemberAddress1');
			$table->string('MemberAddress2', 50)->nullable()->comment('MemberAddress2');
			$table->string('MemberBoxNumber', 10)->nullable()->comment('MemberBoxNumber');
			$table->string('MemberCity', 50)->nullable()->comment('MemberCity');
			$table->string('MemberCountry', 50)->nullable()->comment('MemberCountry');
			$table->string('MemberCounty', 50)->nullable()->comment('MemberCounty');
			$table->text('MemberDesignation')->nullable()->comment('MemberDesignation');
			$table->string('MemberDirectPhone', 16)->nullable()->comment('MemberDirectPhone');
			$table->string('MemberEmail', 80)->nullable()->comment('MemberEmail');
			$table->string('MemberFax', 16)->nullable()->comment('MemberFax');
			$table->string('MemberFirstName', 50)->nullable()->comment('MemberFirstName');
			$table->string('MemberFullName', 150)->nullable()->comment('MemberFullName');
			$table->text('MemberFullRoleList')->nullable()->comment('MemberFullRoleList');
			$table->date('MemberJoinDate')->default('0000-00-00')->comment('MemberJoinDate');
			$table->bigInteger('MemberKey')->primary()->comment('MemberKey');
			$table->string('MemberLastName', 50)->nullable()->comment('MemberLastName');
			$table->date('MemberLicenseExpirationDate')->default('0000-00-00')->comment('MemberLicenseExpirationDate');
			$table->string('MemberLoginId', 25)->nullable()->comment('MemberLoginId');
			$table->string('MemberMiddleInitial', 5)->nullable()->comment('MemberMiddleInitial');
			$table->string('MemberMiddleName', 50)->nullable()->comment('MemberMiddleName');
			$table->string('MemberMlsId', 25)->nullable()->comment('MemberMlsId');
			$table->string('MemberMobilePhone', 16)->nullable()->comment('MemberMobilePhone');
			$table->string('MemberNamePrefix', 50)->nullable()->comment('MemberNamePrefix');
			$table->string('MemberNameSuffix', 50)->nullable()->comment('MemberNameSuffix');
			$table->string('MemberNationalAssociationId', 25)->nullable()->comment('MemberNationalAssociationId');
			$table->string('MemberNickname', 50)->nullable()->comment('MemberNickname');
			$table->integer('MemberNumViolations')->nullable()->comment('MemberNumViolations');
			$table->string('MemberOfficePhone', 16)->nullable()->comment('MemberOfficePhone');
			$table->integer('MemberOfficePhoneExt')->nullable()->comment('MemberOfficePhoneExt');
			$table->string('MemberPager', 16)->nullable()->comment('MemberPager');
			$table->string('MemberPostalCode', 10)->nullable()->comment('MemberPostalCode');
			$table->string('MemberPostalCodePlus4', 4)->nullable()->comment('MemberPostalCodePlus4');
			$table->string('MemberPreferredPhone', 16)->nullable()->comment('MemberPreferredPhone');
			$table->integer('MemberPreferredPhoneExt')->nullable()->comment('MemberPreferredPhoneExt');
			$table->text('MemberPrivateEmail')->nullable()->comment('MemberPrivateEmail');
			$table->char('MemberRatePlugFlag', 1)->nullable()->comment('MemberRatePlugFlag');
			$table->date('MemberReinstatementDate')->default('0000-00-00')->comment('MemberReinstatementDate');
			$table->text('MemberRoleList')->nullable()->comment('MemberRoleList');
			$table->string('MemberStateLicense', 50)->nullable()->comment('MemberStateLicense');
			$table->string('MemberStateLicenseState', 50)->nullable()->comment('MemberStateLicenseState');
			$table->string('MemberStateOrProvince', 50)->nullable()->comment('MemberStateOrProvince');
			$table->string('MemberStatus', 50)->nullable()->comment('MemberStatus');
			$table->string('MemberStreetDirSuffix', 50)->nullable()->comment('MemberStreetDirSuffix');
			$table->string('MemberStreetException', 10)->nullable()->comment('MemberStreetException');
			$table->string('MemberStreetName', 50)->nullable()->comment('MemberStreetName');
			$table->integer('MemberStreetNumber')->nullable()->comment('MemberStreetNumber');
			$table->string('MemberStreetSuffix', 50)->nullable()->comment('MemberStreetSuffix');
			$table->date('MemberTerminationDate')->default('0000-00-00')->comment('MemberTerminationDate');
			$table->string('MemberType', 50)->nullable()->comment('MemberType');
			$table->string('MemberSubType', 50)->nullable()->comment('MemberSubType');
			$table->string('MemberUnitDesignation', 50)->nullable()->comment('MemberUnitDesignation');
			$table->string('MemberUnitNumber', 20)->nullable()->comment('MemberUnitNumber');
			$table->string('MemberVoiceMailExt', 15)->nullable()->comment('MemberVoiceMailExt');
			$table->string('MemberVoiceMail', 16)->nullable()->comment('MemberVoiceMail');
			$table->dateTime('ModificationTimestamp')->default('0000-00-00 00:00:00')->comment('ModificationTimestamp');
			$table->bigInteger('OfficeKey')->nullable()->comment('OfficeKey');
			$table->string('OfficeMlsId', 25)->nullable()->comment('OfficeMlsId');
			$table->bigInteger('OfficeBrokerKey')->nullable()->comment('OfficeBrokerKey');
			$table->string('OfficeName', 80)->nullable()->comment('OfficeName');
			$table->string('OfficeBrokerMlsId', 25)->nullable()->comment('OfficeBrokerMlsId');
			$table->dateTime('MemberDateAdded')->default('0000-00-00 00:00:00')->comment('MemberDateAdded');
			$table->text('SocialMediaBlogUrlOrId')->nullable()->comment('SocialMediaBlogUrlOrId');
			$table->text('SocialMediaFacebookUrlOrId')->nullable()->comment('SocialMediaFacebookUrlOrId');
			$table->text('SocialMediaLinkedInUrlOrId')->nullable()->comment('SocialMediaLinkedInUrlOrId');
			$table->text('SocialMediaTwitterUrlOrId')->nullable()->comment('SocialMediaTwitterUrlOrId');
			$table->text('SocialMediaWebsiteUrlOrId')->nullable()->comment('SocialMediaWebsiteUrlOrId');
			$table->text('SocialMediaYouTubeUrlOrId')->nullable()->comment('SocialMediaYouTubeUrlOrId');
			$table->string('MemberSourceInput', 50)->nullable()->comment('MemberSourceInput');
			$table->string('MemberSourceRecordKey')->nullable()->comment('MemberSourceRecordKey');
			$table->string('MemberSourceRecordID', 128)->nullable()->comment('MemberSourceRecordID');
			$table->string('MemberSourceBusinessPartner', 50)->nullable()->comment('MemberSourceBusinessPartner');
			$table->string('MemberSourceTransport', 50)->nullable()->comment('MemberSourceTransport');
			$table->text('SyndicateTo')->nullable()->comment('SyndicateTo');
			$table->string('MemberSubSystemLocale', 50)->nullable()->comment('MemberSubSystemLocale');
			$table->string('MemberSystemLocale', 50)->nullable()->comment('MemberSystemLocale');
			$table->string('MemberPreferredFirstName', 128)->nullable()->comment('MemberPreferredFirstName');
			$table->string('MemberPreferredLastName', 128)->nullable()->comment('MemberPreferredLastName');
			$table->char('MemberBrightConvertedYN', 1)->nullable()->comment('MemberBrightConvertedYN');
			$table->string('MemberAssociationPrimary', 50)->nullable()->comment('MemberAssociationPrimary');
			$table->text('MemberAssociationsFullList')->nullable()->comment('MemberAssociationsFullList');
			$table->char('MemberPreviewYN', 1)->nullable()->comment('MemberPreviewYN');
			$table->dateTime('SourceModificationTimestamp')->default('0000-00-00 00:00:00')->comment('SourceModificationTimestamp');
			$table->string('MemberStreetDirPrefix', 50)->nullable()->comment('MemberStreetDirPrefix');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bright_agent_roster');
	}

}
