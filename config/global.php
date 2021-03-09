<?php

$bad_characters = ['/\#/', '/\</', '/\$/', '/\+/', '/\%/', '/\>/', '/\!/', '/\&/', '/\*/', '/\'/', '/\|/', '/\{/', '/\?/', '/\"/', '/\=/', '/\}/', '/\//', '/\:/', '/\s/', '/\@/', '/\;/', '/\,/', '/\(/', '/\)/', '/\[/', '/\]/', '/\./'];

$street_suffixes = ['ALLEY', 'AVENUE', 'BEND', 'BOULEVARD', 'BRANCH', 'CIRCLE', 'CIR', 'CORNER', 'COURSE', 'COURT', 'COVE', 'CRESCENT', 'CROSSING', 'DRIVE', 'DRIVEWAY', 'EXTENSION', 'GARDENS', 'GARTH', 'GATEWAY', 'GLEN', 'GROVE', 'HARBOR', 'HIGHWAY', 'HILL', 'HOLLOW', 'KNOLLS', 'LANDING', 'LANE', 'LOOP', 'MEWS', 'MILLS', 'NORTHWAY', 'PARKWAY', 'PASSAGE', 'PATH', 'PIKE', 'PLACE', 'RIDGE', 'ROAD', 'ROUTE', 'ROW', 'RUN', 'SQUARE', 'STREET', 'TERRACE', 'TRACE', 'TRAIL', 'TURN', 'VIEW', 'VISTA', 'WALK', 'WAY'];
$street_dir_suffixes = ['E', 'EAST', 'N', 'NE', 'NORTH', 'NORTHEAST', 'NORTHWEST', 'NW', 'S', 'SE', 'SOUTH', 'SOUTHEAST', 'SOUTHWEST', 'SW', 'W', 'WEST'];

$loader = '
<div class="preloader-wrapper active">
    <div class="spinner-layer spinner-blue-only">
        <div class="circle-clipper left">
            <div class="circle"></div>
        </div>
        <div class="gap-patch">
            <div class="circle"></div>
        </div>
        <div class="circle-clipper right">
            <div class="circle"></div>
        </div>
    </div>
</div>';

$select_columns_bright = 'Appliances,AssociationFee,AssociationFeeFrequency,AssociationYN,AttachedGarageYN,BasementFinishedPercent,BasementYN,BathroomsTotalInteger,BedroomsTotal,City,CloseDate,ClosePrice,CondoYN,Cooling,County,ElementarySchool,FireplaceYN,FullStreetAddress,GarageYN,Heating,HighSchool,Latitude,LeaseAmount,ListingId,ListingSourceRecordKey,ListingTaxID,ListPictureURL,ListPrice,LivingArea,Longitude,LotSizeAcres,LotSizeSquareFeet,MajorChangeTimestamp,MiddleOrJuniorSchool,MLSListDate,MlsStatus,NewConstructionYN,NumAttachedGarageSpaces,NumDetachedGarageSpaces,Pool,PostalCode,PropertySubType,PropertyType,PublicRemarks,SaleType,StateOrProvince,StreetDirPrefix,StreetDirSuffix,StreetName,StreetNumber,StreetSuffix,StreetSuffixModifier,StructureDesignType,SubdivisionName,TotalPhotos,UnitBuildingType,UnitNumber,YearBuilt,ListOfficeName,ListOfficeMlsId,ListAgentMlsId,ListAgentFirstName,ListAgentLastName,ListAgentEmail,ListAgentPreferredPhone,BuyerOfficeName,BuyerOfficeMlsId,BuyerAgentMlsId,BuyerAgentFirstName,BuyerAgentLastName,BuyerAgentEmail,BuyerAgentPreferredPhone';

$select_columns_bright_agents = ['ListAgentFirstName,ListAgentLastName,ListAgentEmail,ListAgentPreferredPhone,BuyerOfficeName,BuyerOfficeMlsId,BuyerAgentMlsId,BuyerAgentFirstName,BuyerAgentLastName,BuyerAgentEmail,BuyerAgentPreferredPhone'];

return [
    'vars' => [
        'app_stage' => env('APP_STAGE'),
        'bad_characters' => $bad_characters,
        'street_suffixes' => $street_suffixes,
        'street_dir_suffixes' => $street_dir_suffixes,
        'select_columns_bright' => $select_columns_bright,
        'select_columns_bright_agents' => $select_columns_bright_agents,
        'loader' => $loader,
        'active_states' => explode(',', env('ACTIVE_STATES')),
        'google_api_key' => env('GOOGLE_API_KEY'),
        'socrata_api_key' => env('SOCRATA_API_KEY'),
        'tinymce_api_key' => env('TINYMCE_KEY'),
        'company_street' => env('COMPANY_STREET'),
        'company_city' => env('COMPANY_CITY'),
        'company_state' => env('COMPANY_STATE'),
        'company_zip' => env('COMPANY_ZIP'),
        'property_email' => env('PROPERTY_EMAIL'),
    ],
];
