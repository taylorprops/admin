<?php

namespace App\Jobs\BrightMLS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function handle() {

        $rets_config = new \PHRETS\Configuration;
        $rets_config -> setLoginUrl(config('rets.rets.url'))
            -> setUsername(config('rets.rets.username'))
            -> setPassword(config('rets.rets.password'))
            -> setRetsVersion('RETS/1.8')
            -> setUserAgent('Bright RETS Application/1.0')
            -> setHttpAuthenticationMethod('digest')
            -> setOption('disable_follow_location', false) // or 'basic' if required
            -> setOption('use_post_method', true);

        $rets = new \PHRETS\Session($rets_config);
        $connect = $rets -> Login();

        $database = "admin";
        $table_name = "company_listings";
        $resource = "Property";
        $class = "ALL";
        $key_field = "ListingKey";


        $rets_metadata = $rets -> GetTableMetadata($resource, $class);

        $connection = new mysqli($databaseServer,$databaseUser,$databasePassword,$database);
        $sql = create_table_sql_from_metadata($table_name, $rets_metadata, $key_field);
        mysqli_query($connection, $sql);



    }

    function create_table_sql_from_metadata($table_name, $rets_metadata, $key_field, $field_prefix = "") {

        $sql_query = "CREATE TABLE $database.".$table_name." (\n";

        foreach ($rets_metadata as $field) {

            $cleaned_comment = addslashes($field -> getLongName());
            $sql_make = "\t`" . $field_prefix . $field -> getSystemName()."` ";
            if ($field -> getInterpretation() == "LookupMulti") {
                $sql_make .= "TEXT";
            } elseif ($field -> getInterpretation() == "Lookup") {
                $sql_make .= "VARCHAR(50)";
            } elseif ($field -> getDataType() == "Int" || $field -> getDataType() == "Small" || $field -> getDataType() == "Tiny") {
                $sql_make .= "INT(".$field -> getMaximumLength().")";
            } elseif ($field -> getDataType() == "Long") {
                $sql_make .= "BIGINT(".$field -> getMaximumLength().")";
            } elseif ($field -> getDataType() == "DateTime") {
                $sql_make .= "DATETIME default '0000-00-00 00:00:00' NOT NULL";
            } elseif ($field -> getDataType() == "Character" && $field -> getMaximumLength() <= 255) {
                $sql_make .= "VARCHAR(".$field -> getMaximumLength().")";
            } elseif ($field -> getDataType() == "Character" && $field -> getMaximumLength() > 255) {
                $sql_make .= "TEXT";
            } elseif ($field -> getDataType() == "Decimal") {
                $pre_point = ($field -> getMaximumLength() - $field -> getPrecision());
                $post_point = !empty($field -> getPrecision()) ? $field -> getPrecision() : 0;
                $sql_make .= "DECIMAL({$field -> getMaximumLength()},{$post_point})";
            } elseif ($field -> getDataType() == "Boolean") {
                $sql_make .= "CHAR(1)";
            } elseif ($field -> getDataType() == "Date") {
                $sql_make .= "DATE default '0000-00-00' NOT NULL";
            } elseif ($field -> getDataType() == "Time") {
                $sql_make .= "TIME default '00:00:00' NOT NULL";
            } else {
                $sql_make .= "VARCHAR(255)";
            }
            $sql_make .=  " COMMENT '".$cleaned_comment."',\n";
            $sql_query .= $sql_make;

        }

        $sql_query .=  "PRIMARY KEY(`".$field_prefix.$key_field."`) )";

    }

}
