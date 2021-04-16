<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\BrightMLS\CompanyListings;
use App\Models\DocManagement\Create\Upload\Upload;
use App\Models\DocManagement\Transactions\Upload\TransactionUpload;
use App\Models\DocManagement\Transactions\Documents\TransactionDocuments;

class TestController extends Controller
{
    public function test(Request $request) {

        $bright = ['MDPG603120', 'DCDC517350', 'DCDC517224', 'DCDC517208', 'MDBC525686', 'MDPG603166', 'MDAA464802', 'MDBA546402', 'MDBA547064', 'MDMC751912', 'MDQA147390', 'DCDC516888', 'MDCA182248', 'MDMC752642', 'MDMC751728', 'MDMC751730', 'MDBC525402', 'MDAA463980', 'MDPG602928', 'MDMC752770', 'MDBA546678', 'MDBC525254', 'MDAA464534', 'MDMC752688', 'MDMC752602', 'MDFR280614', 'MDPG601544', 'MDBA546114', 'DCDC516168', 'DCDC516166', 'DCDC516156', 'DCDC516154', 'MDMC752350', 'DCDC514336', 'MDBA546408', 'MDBA546404', 'MDBA546276', 'MDPG602092', 'MDPG595726', 'MDAA464352', 'MDBA546116', 'MDCM125298', 'VAAR179062', 'MDAA464064', 'MDHW292606', 'MDCA182042', 'DCDC514796', 'DCDC515312', 'DCDC515330', 'DCDC511816', 'MDBC524444', 'MDPG601948', 'MDBA545600', 'MDMC750550', 'MDBC523524', 'DCDC514586', 'DCDC514558', 'MDHW292356', 'MDMC750994', 'MDMC750990', 'DCDC514668', 'MDAA463494', 'DCDC514576', 'MDBA545124', 'MDBA544860', 'MDBA544838', 'MDPG601430', 'MDPG601414', 'VAAR178436', 'MDCR203356', 'DCDC513214', 'MDBC523426', 'MDCH222926', 'MDBA544016', 'MDQA147116', 'MDQA147114', 'MDQA147110', 'MDQA147108', 'DCDC512494', 'MDQA147096', 'MDPG600404', 'MDBA543638', 'MDBA543500', 'MDAA462004', 'MDAA461896', 'MDBA543096', 'MDAA461352', 'DCDC512048', 'MDPG599628', 'DCDC511790', 'MDPG599496', 'MDMC747508', 'MDQA146972', 'MDBA542502', 'MDMC747324', 'MDPG597454', 'MDBA542090', 'PAMC684812', 'MDPG598910', 'MDPG598902', 'MDMC747014', 'MDWA178104', 'MDMC2000388', 'MDBC521178', 'MDBA2000178', 'DCDC510364', 'MDMC745424', 'DCDC510064', 'PAMC2000236', 'DCDC506608', 'MDCH222150', 'MDMC745158', 'MDMC744522', 'MDBA537990', 'MDMC737526', 'MDAA458700', 'MDWO119954', 'MDPG595864', 'MDCH221558', 'MDCH221552', 'MDBA538538', 'MDBA537668', 'MDBA538288', 'MDBA538332', 'MDBA538034', 'MDBA538030', 'MDBA538012', 'MDBA537794', 'DCDC502000', 'MDPG594556', 'DCDC504686', 'MDKE117548', 'MDCH221082', 'MDMC740186', 'MDQA146414', 'MDQA146396', 'MDCH220624', 'MDBA535226', 'MDQA146330', 'MDTA140062', 'MDAA454596', 'MDCM124854', 'MDPG590112', 'MDAA453798', 'MDBA532580', 'MDPG589360', 'MDPG589290', 'MDAA446070', 'MDHW287666', 'MDCM124714', 'DCDC495006', 'MDHW287670', 'MDHW287668', 'MDHW287650', 'MDWA175804', 'MDPG585056', 'DCDC491194', 'MDBC508916', 'DCDC489886', 'DCDC489898', 'MDBC508190', 'MDBC508126', 'VAMA108588', 'MDAA447916', 'VANV101536', 'MDPG581474', 'MDBA523976', 'DCDC485620', 'DCDC484984', 'MDBA514098', 'MDCM124356', 'MDMC721108', 'MDCH216506', 'MDQA144888', 'DCDC480476', 'MDMC713628', 'MDAA438244', 'DCDC472828', 'DCDC468624', 'MDBA508922', 'MDBA508842', 'DCDC466696', 'DCDC464002', 'DCDC463546', 'MDAA428234', 'VASP219528', 'MDPG548540', 'MDBA483250', 'MDHR238052', 'MDHR238050', 'MDHR238046', 'MDBC330660', 'MDBC330658', '1000420674', '1000408892', '1003289471'];

        $company = CompanyListings::where('MlsStatus', 'Active') -> get() -> pluck('ListingId') -> toArray();

        dd(array_diff($bright, $company));

        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('rets.rets.url'))
            -> setUsername(config('rets.rets.username'))
            -> setPassword(config('rets.rets.password'))
            -> setRetsVersion('RETS/1.8')
            -> setUserAgent('Bright RETS Application/1.0')
            -> setHttpAuthenticationMethod('digest')
            -> setOption('disable_follow_location', false);
            //-> setOption('use_post_method', true);

        $rets = new \PHRETS\Session($rets_config);
        $connect = $rets -> Login();

        $resource = 'Property';
        $class = 'ALL';

        try {

            // get company listings count
            $company_listings_keys = CompanyListings::get() -> pluck('ListingKey') -> toArray();
            $company_listings_count = count($company_listings_keys);

            // get bright listings count
            $bright_office_codes = implode(',', config('bright_office_codes'));

            $query = '(ListOfficeMlsId=|'.$bright_office_codes.')';

            $results = $rets -> Search(
                $resource,
                $class,
                $query,
                [
                    'Count' => 1,
                    'Select' => 'ListingKey'
                ]
            );

            $bright_listings = $results -> toArray();
            $bright_listings_count = $results -> count();

            $bright_listing_keys = [];
            foreach($bright_listings as $bright_listing) {
                $bright_listing_keys[] = $bright_listing['ListingKey'];
            }

            if($company_listings_count != $bright_listings_count) {

                // get missing listing keys
                $missing_company = array_diff($bright_listing_keys, $company_listings_keys);
                $withdrawn = array_diff($company_listings_keys, $bright_listing_keys);
                dd($missing_company);
                if(count($missing_company) > 0) {

                    $query = '(ListingKey='.implode(',', $missing_company).')';

                    $results = $rets -> Search(
                        $resource,
                        $class,
                        $query
                    );

                    $listings = $results -> toArray();

                    foreach($listings as $listing) {

                        $listing_key = $listing['ListingKey'];

                        $add_listing = CompanyListings::firstOrCreate([
                            'ListingKey' => $listing_key
                        ]);

                        foreach($listing as $col => $val) {
                            $add_listing -> $col = $val;
                        }

                        $add_listing -> save();

                    }

                } else if(count($withdrawn) > 0) {

                    $update_listings = CompanyListings::whereIn('ListingKey', $withdrawn)
                        -> update([
                            'MlsStatus' => 'Withdrawn',
                            'CloseDate' => date('Y-m-d')
                        ]);

                }

            }

            return true;

        } catch (Throwable $exception) {

            if ($exception instanceof ServerException) {

                sleep(3);
                \Artisan::call('bright_mls:update_listings');

            } else if ($exception instanceof QueryException) {

                if(stristr($exception -> getMessage(), 'Column not found')) {

                    $results = $rets -> Search(
                        $resource,
                        $class,
                        $query,
                        [
                            'Limit' => 1
                        ]
                    );

                    $results = $results -> toArray();

                    $system = $rets -> GetSystemMetadata();
                    $table = 'admin.company_listings';

                    $rets_metadata = $rets -> GetTableMetadata($resource, $class);

                    DB::select("SET GLOBAL innodb_strict_mode=OFF");

                    foreach($results as $listing) {

                        foreach ($listing as $key => $val) {
                            $colNames[] = $key;
                        }

                        $columns_in_db = DB::select("SHOW COLUMNS FROM ".$table);

                        $cols = array();

                        foreach($columns_in_db as $column_in_db) {
                            $cols[] = $column_in_db -> Field;
                        }

                        $columns_count = count($columns_in_db);
                        $last_column = $cols[$columns_count -1];

                        $missing = array_diff($colNames,$cols);

                        $s = 0;

                        foreach($missing as $missing_column) {

                            // Get column type
                            foreach ($rets_metadata as $field) {

                                if($field -> getSystemName() == $missing_column) {

                                    $cleaned_comment = addslashes($field -> getLongName());

                                    if ($field -> getInterpretation() == "LookupMulti") {
                                        $column_type =  "TEXT";
                                    } elseif ($field -> getInterpretation() == "Lookup") {
                                        $column_type =  "VARCHAR(50)";
                                    } elseif ($field -> getDataType() == "Int" || $field -> getDataType() == "Small" || $field -> getDataType() == "Tiny") {
                                        $column_type =  "INT(".$field -> getMaximumLength().")";
                                    } elseif ($field -> getDataType() == "Long") {
                                        $column_type =  "BIGINT(".$field -> getMaximumLength().")";
                                    } elseif ($field -> getDataType() == "DateTime") {
                                        $column_type =  "DATETIME default '0000-00-00 00:00:00' NOT NULL";
                                    } elseif ($field -> getDataType() == "Character" && $field -> getMaximumLength() <= 255) {
                                        $column_type =  "VARCHAR(".$field -> getMaximumLength().")";
                                    } elseif ($field -> getDataType() == "Character" && $field -> getMaximumLength() > 255) {
                                        $column_type =  "TEXT";
                                    } elseif ($field -> getDataType() == "Decimal") {
                                        $pre_point = ($field -> getMaximumLength() - $field -> getPrecision());
                                        $post_point = !empty($field -> getPrecision()) ? $field -> getPrecision() : 0;
                                        $column_type =  "DECIMAL({$field -> getMaximumLength()},{$post_point})";
                                    } elseif ($field -> getDataType() == "Boolean") {
                                        $column_type =  "CHAR(1)";
                                    } elseif ($field -> getDataType() == "Date") {
                                        $column_type =  "DATE default '0000-00-00' NOT NULL";
                                    } elseif ($field -> getDataType() == "Time") {
                                        $column_type =  "TIME default '00:00:00' NOT NULL";
                                    } else {
                                        $column_type =  "VARCHAR(255)";
                                    }
                                }
                            }
                            $s += 1;
                            // already selected last column, now add new column after it
                            if($s == 1) {

                                DB::select("ALTER TABLE ".$table." ADD COLUMN `".$missing_column."` ".$column_type." AFTER `".$last_column."`");

                            // get new last column and add new column after it
                            } else {

                                $columns_in_db = DB::select("SHOW COLUMNS FROM ".$table);
                                $cols = array();

                                foreach($columns_in_db as $column_in_db) {
                                    $cols[] = $column_in_db -> Field;
                                }

                                $columns_count = count($columns_in_db);
                                $last_column = $cols[$columns_count -1];

                                DB::select("ALTER TABLE ".$table." ADD COLUMN `".$missing_column."` ".$column_type." AFTER `".$last_column."`");


                            }

                        }

                    }

                    DB::select("SET GLOBAL innodb_strict_mode=ON");

                }

            }

        }

    }
}
