<?php

namespace App\Models\DocManagement\Create\Fields;

use App\Models\DocManagement\Create\Fields\FieldInputs;
use App\Models\DocManagement\Transactions\Contracts\Contracts;
use App\Models\DocManagement\Transactions\EditFiles\UserFieldsInputs;
//use App\Models\DocManagement\Transactions\EditFiles\UserFieldsValues;
use App\Models\DocManagement\Transactions\Listings\Listings;
use App\Models\DocManagement\Transactions\Members\Members;
use App\Models\DocManagement\Transactions\Referrals\Referrals;
use Illuminate\Database\Eloquent\Model;

class CommonFields extends Model
{
    protected $connection = 'mysql';
    public $table = 'docs_create_common_fields';
    public $timestamps = false;
    protected $guarded = [];

    /* public function scopeGetCommonFields() {
        $common_fields = CommonFields::select('field_name', 'field_type') -> orderBy('field_order', 'ASC') -> get();
        $common_field_types = $common_fields -> mapToGroups(function ($item, $key) {
            return [ $item['field_type'] => [ $item['field_name'] ] ];
        });
        $common_field_types -> toArray();

        $field_types = FieldTypes::select('field_type') -> get() -> toArray();

        $common = array();
        foreach($field_types as $type) {

            if($common_field_types -> get($type['field_type'])) {
                $common[$type['field_type']] = $common_field_types -> get($type['field_type']) -> all();
            }
        }
        return $common;
    } */

    public function ScopeGetCommonNameValue($query, $common_name, $input_id, $field_type, $Listing_ID, $Contract_ID, $Referral_ID, $transaction_type, $Agent_ID)
    {
        if ($field_type == 'system') {
            $field_input = UserFieldsInputs::where('id', $input_id)->where('field_type', 'common')->get();
            $common_name_search = $this->where('field_name', $common_name)->first();

            $members_modal = new Members();

            if ($transaction_type == 'listing') {
                $property = Listings::find($Listing_ID);
                $members = $members_modal->where('Listing_ID', $Listing_ID)->orWhere('Contract_ID', $Contract_ID)->get();
            } elseif ($transaction_type == 'contract') {
                $property = Contracts::find($Contract_ID);
                $members = $members_modal->where('Listing_ID', $Listing_ID)->orWhere('Contract_ID', $Contract_ID)->get();
            } elseif ($transaction_type == 'referral') {
                $property = Referrals::find($Referral_ID);
                $members = $members_modal->where('Referral_ID', $Referral_ID)->get();
            }

            $value = '';

            if (! empty($common_name_search) && $common_name_search->db_column_name != '') {
                $db_column_name = $common_name_search->db_column_name;
                if ($property->$db_column_name != '') {
                    $value = $property->$db_column_name;
                }
            } else {
                $member = null;

                if ($common_name == 'Seller or Landlord One Name') {
                    $member = $members->where('member_type_id', $members_modal->GetMemberTypeID('Seller'));
                    if (count($member) > 0) {
                        $member = $member->values();
                        $member = $member[0];
                    } else {
                        $member = null;
                    }
                } elseif ($common_name == 'Seller or Landlord Two Name') {
                    $member = $members->where('member_type_id', $members_modal->GetMemberTypeID('Seller'));
                    if (count($member) > 1) {
                        $member = $member->values();
                        $member = $member[1];
                    } else {
                        $member = null;
                    }
                } elseif ($common_name == 'Buyer or Renter One Name') {
                    $member = $members->where('member_type_id', $members_modal->GetMemberTypeID('Buyer'));
                    if (count($member) > 0) {
                        $member = $member->values();
                        $member = $member[0];
                    } else {
                        $member = null;
                    }
                } elseif ($common_name == 'Buyer or Renter Two Name') {
                    $member = $members->where('member_type_id', $members_modal->GetMemberTypeID('Buyer'));
                    if (count($member) > 1) {
                        $member = $member->values();
                        $member = $member[1];
                    } else {
                        $member = null;
                    }
                } elseif ($common_name == 'List Agent Company') {
                    $value = $property->ListOfficeName;
                } elseif ($common_name == 'Selling Agent Company') {
                    $value = $property->BuyerOfficeName;
                } elseif ($common_name == 'Listing Agent Name') {
                    $value = $property->ListAgentFirstName.' '.$property->ListAgentLastName;
                } elseif ($common_name == 'Buyers Agent Name') {
                    $value = $property->BuyerAgentFirstName.' '.$property->BuyerAgentLastName;
                }

                if ($member) {
                    $value = $member->first_name.' '.$member->last_name;
                } else {
                    $common_address_fields = $this->where('field_type', 'address')->pluck('field_name');

                    foreach ($common_address_fields as $address_field) {
                        if ($value == '') {
                            $address_field = trim($address_field);
                            $address_type = str_replace(' Address', '', $address_field);
                            $address_type = str_replace('Property', '', $address_type);
                            $address_type = str_replace('or Renter ', '', $address_type);
                            $address_type = str_replace('or Landlord ', '', $address_type);
                            $address_type = str_replace(' ', '', $address_type);

                            $field_street = $address_type.'FullStreetAddress';
                            $field_city = $address_type.'City';
                            $field_state = $address_type.'StateOrProvince';
                            $field_zip = $address_type.'PostalCode';
                            $field_county = $address_type.'County';

                            if (stristr($common_name, 'Full Address')) {
                                $value = $property->$field_street.' '.$property->$field_city.' '.$property->$field_state.' '.$property->$field_zip;
                            } elseif (stristr($common_name, 'Street')) {
                                $value = $property->$field_street;
                            } elseif (stristr($common_name, 'City')) {
                                $value = $property->$field_city;
                            } elseif (stristr($common_name, 'State')) {
                                $value = $property->$field_state;
                            } elseif (stristr($common_name, 'Zip Code')) {
                                $value = $property->$field_zip;
                            } elseif (stristr($common_name, 'County')) {
                                $value = $property->$field_county;
                            }
                        }
                    }
                }
            }
            if ($value == null) {
                $value = '';
            }

            return trim($value);
        } elseif ($field_type == 'user') {
        }
    }
}
