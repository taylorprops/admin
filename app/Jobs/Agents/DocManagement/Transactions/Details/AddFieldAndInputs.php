<?php

namespace App\Jobs\Agents\DocManagement\Transactions\Details;

use App\Models\DocManagement\Create\Fields\CommonFields;
use App\Models\DocManagement\Create\Fields\CommonFieldsSubGroups;
use App\Models\DocManagement\Create\Fields\Fields;
use App\Models\DocManagement\Transactions\EditFiles\UserFields;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
use App\Models\DocManagement\Transactions\Listings\Listings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddFieldAndInputs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //public $tries = 5;

    protected $file_id;
    protected $new_file_id;
    protected $Agent_ID;
    protected $Listing_ID;
    protected $Contract_ID;
    protected $Referral_ID;
    protected $transaction_type;
    protected $property;
    protected $file_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(/* $queue_name,  */$file_id, $new_file_id, $Agent_ID, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, $property, $file_type)
    {

        //$this -> queue = $queue_name;
        $this -> file_id = $file_id;
        $this -> new_file_id = $new_file_id;
        $this -> Agent_ID = $Agent_ID;
        $this -> Listing_ID = $Listing_ID;
        $this -> Contract_ID = $Contract_ID;
        $this -> Referral_ID = $Referral_ID;
        $this -> transaction_type = $transaction_type;
        $this -> property = $property;
        $this -> file_type = $file_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {

            $property = $this -> property;

            $fields = Fields::where('file_id', $this -> file_id) -> with('common_field') -> get();

            foreach ($fields as $field) {
                $field_type = $field -> field_type;
                $field_category = $field -> field_category;

                $field_inputs = 'no';

                if ($field_type == 'address' || ($field_type == 'name' && preg_match('/(Renter|Owner)/', $field -> field_name))) {
                    $field_inputs = 'yes';
                }

                if ($field_type == '') {
                    $field_type = $field_category;
                }

                $new_field = new UserFields();
                $new_field -> file_id = $this -> new_file_id;

                $new_field -> common_field_id = $field -> common_field_id;
                $new_field -> create_field_id = $field -> field_id;
                $new_field -> group_id = $field -> group_id;
                $new_field -> page = $field -> page;
                $new_field -> field_category = $field_category;
                $new_field -> field_type = $field_type;
                //$new_field -> field_created_by = 'system'; this is the default value
                $new_field -> field_name = $field -> field_name;
                $new_field -> field_name_display = $field -> field_name_display;
                $new_field -> field_name_type = $field -> field_name_type;
                $new_field -> number_type = $field -> number_type;
                $new_field -> field_sub_group_id = $field -> field_sub_group_id;
                $new_field -> top_perc = $field -> top_perc;
                $new_field -> left_perc = $field -> left_perc;
                $new_field -> width_perc = $field -> width_perc;
                $new_field -> height_perc = $field -> height_perc;

                $new_field -> Agent_ID = $this -> Agent_ID;
                $new_field -> Listing_ID = $this -> Listing_ID;
                $new_field -> Contract_ID = $this -> Contract_ID;
                $new_field -> Referral_ID = $this -> Referral_ID;
                $new_field -> transaction_type = $this -> transaction_type;
                $new_field -> file_type = $this -> file_type;
                $new_field -> field_inputs = $field_inputs;

                $new_field -> save();

                $new_field_id = $new_field -> id;

                $for_sale = $property -> SaleRent == 'sale' || $property -> SaleRent == 'both' ? 'yes' : 'no';

                // add inputs

                // if $field_inputs == 'yes' there will be 2 or 4/5 inputs, otherwise just one
                if ($field_inputs == 'yes') {
                    $sub_group_title = CommonFieldsSubGroups::GetSubGroupTitle($new_field -> field_sub_group_id);
                    if ($sub_group_title == '') {
                        $sub_group_title = 'Property';
                    }

                    if ($new_field -> field_type == 'name') {
                        if (preg_match('/Buyer/', $sub_group_title)) {
                            $name_type = $for_sale == 'yes' ? 'Buyer' : 'Renter';
                            $input_name_one_display = $name_type.' One Name';
                            $input_name_one_db_column = 'BuyerOneFullName';
                            $input_name_two_display = $name_type.' Two Name';
                            $input_name_two_db_column = 'BuyerTwoFullName';
                        } elseif (preg_match('/Seller/', $sub_group_title)) {
                            $name_type = $for_sale == 'yes' ? 'Seller' : 'Owner';
                            $input_name_one_display = $name_type.' One Name';
                            $input_name_one_db_column = 'SellerOneFullName';
                            $input_name_two_display = $name_type.' Two Name';
                            $input_name_two_db_column = 'SellerTwoFullName';
                        }

                        $input_one = new UserFieldsInputs();
                        $input_one -> file_id = $new_field -> file_id;
                        $input_one -> group_id = $new_field -> group_id;
                        $input_one -> file_type = $new_field -> file_type;
                        $input_one -> field_type = $new_field -> field_type;
                        $input_one -> transaction_field_id = $new_field -> id;
                        $input_one -> input_name_display = $input_name_one_display;
                        $input_one -> input_db_column = $input_name_one_db_column;
                        $input_one -> Agent_ID = $new_field -> Agent_ID;
                        $input_one -> Listing_ID = $new_field -> Listing_ID;
                        $input_one -> Contract_ID = $new_field -> Contract_ID;
                        $input_one -> Referral_ID = $new_field -> Referral_ID;
                        $input_one -> transaction_type = $new_field -> transaction_type;
                        $input_one -> save();

                        $input_two = $input_one -> replicate();
                        $input_two -> input_name_display = $input_name_two_display;
                        $input_two -> input_db_column = $input_name_two_db_column;
                        $input_two -> save();

                    } elseif ($new_field -> field_type == 'address') {

                        // using Renter and Owner to find Buyer or Renter/Seller or Owner because 'Buyer' matches Buyer Agent
                        if (preg_match('/Renter/', $sub_group_title)) {
                            $name_type = $for_sale == 'yes' ? 'Buyer' : 'Renter';

                            // get name type to match db columns
                            if (preg_match('/One/', $sub_group_title)) {
                                $db_type = 'BuyerOne';
                            } elseif (preg_match('/Two/', $sub_group_title)) {
                                $db_type = 'BuyerTwo';
                            } elseif (preg_match('/Both/', $sub_group_title)) {
                                $db_type = 'BuyerOne';
                                $db_name = 'Buyer';
                            }
                        } elseif (preg_match('/Owner/', $sub_group_title)) {
                            $name_type = $for_sale == 'yes' ? 'Seller' : 'Owner';

                            // get name type to match db columns
                            if (preg_match('/One/', $sub_group_title)) {
                                $db_type = 'SellerOne';
                            } elseif (preg_match('/Two/', $sub_group_title)) {
                                $db_type = 'SellerTwo';
                            } elseif (preg_match('/Both/', $sub_group_title)) {
                                $db_type = 'SellerOne';
                                $db_name = 'Seller';
                            }
                        } elseif (preg_match('/Office/', $sub_group_title)) {
                            $name_type = 'List Agent Office';
                            $db_type = 'ListOffice';
                            if (preg_match('/Buyer/', $sub_group_title)) {
                                $name_type = 'Buyer Agent Office';
                                $db_type = 'BuyerOffice';
                            }
                        } else {
                            $name_type = $sub_group_title;
                            $db_type = str_replace('Property', '', $sub_group_title);
                        }

                        $input_address_one_display = $name_type.' Street Address';
                        $input_address_one_db_column = $db_type.'FullStreetAddress';
                        $input_address_two_display = $name_type.' City';
                        $input_address_two_db_column = $db_type.'City';
                        $input_address_three_display = $name_type.' State';
                        $input_address_three_db_column = $db_type.'StateOrProvince';
                        $input_address_four_display = $name_type.' Zip';
                        $input_address_four_db_column = $db_type.'PostalCode';
                        if ($sub_group_title == 'Property') {
                            $input_address_five_display = 'Property County';
                            $input_address_five_db_column = $db_type.'County';
                        }

                        $input_one = new UserFieldsInputs();
                        $input_one -> file_id = $new_field -> file_id;
                        $input_one -> group_id = $new_field -> group_id;
                        $input_one -> file_type = $new_field -> file_type;
                        $input_one -> field_type = $new_field -> field_type;
                        $input_one -> transaction_field_id = $new_field -> id;
                        $input_one -> input_name_display = $input_address_one_display;
                        $input_one -> input_db_column = $input_address_one_db_column;
                        $input_one -> Agent_ID = $new_field -> Agent_ID;
                        $input_one -> Listing_ID = $new_field -> Listing_ID;
                        $input_one -> Contract_ID = $new_field -> Contract_ID;
                        $input_one -> Referral_ID = $new_field -> Referral_ID;
                        $input_one -> transaction_type = $new_field -> transaction_type;
                        $input_one -> save();

                        $input_two = $input_one -> replicate();
                        $input_two -> input_name_display = $input_address_two_display;
                        $input_two -> input_db_column = $input_address_two_db_column;
                        $input_two -> save();

                        $input_three = $input_one -> replicate();
                        $input_three -> input_name_display = $input_address_three_display;
                        $input_three -> input_db_column = $input_address_three_db_column;
                        $input_three -> save();

                        $input_four = $input_one -> replicate();
                        $input_four -> input_name_display = $input_address_four_display;
                        $input_four -> input_db_column = $input_address_four_db_column;
                        $input_four -> save();

                        if ($sub_group_title == 'Property') {
                            $input_five = $input_one -> replicate();
                            $input_five -> input_name_display = $input_address_five_display;
                            $input_five -> input_db_column = $input_address_five_db_column;
                            $input_five -> save();
                        }
                    }
                } else {
                    $common_field = CommonFields::find($new_field -> common_field_id);
                    $input_db_column = $common_field ? $common_field -> db_column_name : '';

                    $input = new UserFieldsInputs();
                    $input -> file_id = $new_field -> file_id;
                    $input -> group_id = $new_field -> group_id;
                    $input -> file_type = $new_field -> file_type;
                    $input -> field_type = $new_field -> field_type;
                    $input -> number_type = $new_field -> number_type;
                    $input -> transaction_field_id = $new_field -> id;
                    $input -> input_name_display = $new_field -> field_name_display;
                    $input -> input_db_column = $input_db_column;
                    $input -> Agent_ID = $new_field -> Agent_ID;
                    $input -> Listing_ID = $new_field -> Listing_ID;
                    $input -> Contract_ID = $new_field -> Contract_ID;
                    $input -> Referral_ID = $new_field -> Referral_ID;
                    $input -> transaction_type = $new_field -> transaction_type;
                    $input -> save();
                }

                // add values for common fields
                $inputs = UserFieldsInputs::where('transaction_field_id', $new_field -> id)
                    -> whereNotNull('input_db_column')
                    -> get();

                foreach ($inputs as $input) {
                    $column = $input -> input_db_column;
                    $value = $property -> $column;
                    $input -> input_value = $value;
                    $input -> save();
                }
            }

        } catch (\Exception $e) {

            return $e -> getMessage();

        }
    }
}
