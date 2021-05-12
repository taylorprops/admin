<?php

namespace App\Jobs\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\BrightMLS\CompanyListings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class FindWithdrawListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('rets.rets.url'))
            -> setUsername(config('rets.rets.username'))
            -> setPassword(config('rets.rets.password'))
            -> setRetsVersion('RETS/1.8')
            -> setUserAgent('Bright RETS Application/1.0')
            -> setHttpAuthenticationMethod('digest')
            -> setOption('disable_follow_location', false)
            -> setOption('use_post_method', true);

        $rets = new \PHRETS\Session($rets_config);

        $connect = $rets -> Login();

        if(!$connect -> getBroker()) {
            sleep(5);
            $connect = $rets -> Login();
        }

        if($connect -> getBroker()) {

            try {

                $resource = 'Property';
                $class = 'ALL';

                // get company listings count
                // not closed or withdrawn
                $company_listings_keys = CompanyListings::whereNotIn('MlsStatus', ['Withdrawn', 'CLOSED']) -> where('MlsListDate', '>=', '2016-01-01') -> get() -> pluck('ListingKey') -> toArray();
                $company_listings_count = count($company_listings_keys);

                // get bright listings count
                $bright_office_codes = implode(',', config('bright_office_codes'));
                // not closed
                $query = '(MlsStatus=~MlsStatus200004325492),(MLSListDate=2016-01-01+),((ListOfficeMlsId=|'.$bright_office_codes.')|(BuyerOfficeMlsId=|'.$bright_office_codes.'))';

                $results = $rets -> Search(
                    $resource,
                    $class,
                    $query,
                    [
                        'Count' => '0',
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
                    arsort($missing_company);
                    $withdrawn = array_diff($company_listings_keys, $bright_listing_keys);

                    if(count($missing_company) > 0) {

                        $missing = array_slice($missing_company, 0, 1000);
                        $query = '(ListingKey='.implode(',', $missing).')';

                        $resource = 'Property';
                        $class = 'ALL';

                        $results = $rets -> Search(
                            $resource,
                            $class,
                            $query
                        );

                        $listings = $results -> toArray();

                        foreach($listings as $listing) {

                            $listing_key = $listing['ListingKey'];

                            if($listing['ListingId'] != '') {

                                $add_listing = CompanyListings::firstOrCreate([
                                    'ListingKey' => $listing_key
                                ]);

                                foreach($listing as $col => $val) {
                                    $add_listing -> $col = $val;
                                }

                                $add_listing -> save();

                            }

                        }

                    }

                    if(count($withdrawn) > 0) {

                        $update_listings = CompanyListings::whereIn('ListingKey', $withdrawn)
                            -> update([
                                'MlsStatus' => 'Withdrawn',
                                'CloseDate' => date('Y-m-d')
                            ]);

                    }

                    $rets -> Disconnect();

                }


            } catch (Throwable $exception) {

                if ($exception instanceof QueryException) {

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

                    $rets -> Disconnect();

                }

            }

        } else {

            throw new \Exception('unable to log in to rets');
            return false;

        }

    }
}
