<?php

namespace App\Http\Controllers\Agents\DocManagement\Functions;

use App\Http\Controllers\Controller;
use App\Models\Resources\LocationData;
use Illuminate\Http\Request;

class GlobalFunctionsController extends Controller
{
    public function get_location_details(Request $request) {

		$zip = $request -> zip;
        $location_details = LocationData::select('city', 'state', 'county') -> where('zip', $zip) -> first();
        if ($location_details) {
            return $location_details -> toJson();
        }

        return null;
    }

    public function tax_records($street_number, $street_name, $unit, $zip, $tax_id, $state) {
        $details = '';
        // only able to get tax records for MD at this point
        if ($state == 'MD') {
            if ($tax_id != '') {
                $url = 'https://opendata.maryland.gov/resource/ed4q-f8tm.json?account_id_mdp_field_acctid='.urlencode($tax_id);
            } else {
                $unit_number = '';
                if ($unit != '') {
                    $unit_number = '&premise_address_condominium_unit_no_sdat_field_28='.urlencode($unit);
                }
                $url = 'https://opendata.maryland.gov/resource/ed4q-f8tm.json?$where=starts_with%28mdp_street_address_mdp_field_address,%20%27'.$street_number.'%20'.urlencode(strtoupper($street_name)).'%27%29&mdp_street_address_zip_code_mdp_field_zipcode='.$zip.$unit_number;
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [
                'X-App-Token: Ya0ATXETWXYaL8teBlGPUbYZ5',
                'cache-control: no-cache',
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            ]);

            $response = curl_exec($curl);

            $err = curl_error($curl);
            if ($err) {
                return response() -> json([
                    'error' => 'yes',
                    'curl' => $err,
                ]);
            }

            curl_close($curl);

            // if no response from searching by tax id
            if (! stristr($response, 'account_id_mdp_field_acctid') && $tax_id != '') {
                // search again by address
                $this -> tax_records($street_number, $street_name, $unit, $zip, '', $state);

            // if no response after searching by address
            } elseif (! stristr($response, 'account_id_mdp_field_acctid') && $tax_id == '') {
            } else {

                // if tax record found
                //$properties = preg_replace('/\\n/', '', $response);
                $properties = json_decode($response, true);

                if (count($properties) == 1) {
                    $property = $properties[0];

                    $tax_county = str_replace(' County', '', $property['county_name_mdp_field_cntyname']);
                    $tax_county = str_replace('\'', '', $tax_county);
                    $details = [
                        'County' => $tax_county ?? null,
                        'ListingTaxID' => $property['account_id_mdp_field_acctid'] ?? null,
                        'Longitude' => $property['mdp_longitude_mdp_field_digxcord_converted_to_wgs84'] ?? null,
                        'Latitude' => $property['mdp_latitude_mdp_field_digycord_converted_to_wgs84'] ?? null,
                        'StreetNumber' => $property['premise_address_number_mdp_field_premsnum_sdat_field_20'] ?? null,
                        'StreetName' => $property['premise_address_name_mdp_field_premsnam_sdat_field_23'] ?? null,
                        'StreetSuffix' => $property['premise_address_type_mdp_field_premstyp_sdat_field_24'] ?? null,
                        'FullStreetAddress' => $property['mdp_street_address_mdp_field_address'] ?? null,
                        'City' => $property['premise_address_city_mdp_field_premcity_sdat_field_25'] ?? null,
                        'PostalCode' => $property['premise_address_zip_code_mdp_field_premzip_sdat_field_26'] ?? null,
                        'TaxPropertyType' => $property['land_use_code_mdp_field_lu_desclu_sdat_field_50'] ?? null,
                        'YearBuilt' => $property['c_a_m_a_system_data_year_built_yyyy_mdp_field_yearblt_sdat_field_235'] ?? null,
                        'StateOrProvince' => $state ?? null,
                        'UnitNumber' => $property['premise_address_condominium_unit_no_sdat_field_28'] ?? null,
                        'District' => $property['record_key_district_ward_sdat_field_2'] ?? null,
                        'LegalDescription1' => $property['legal_description_line_1_mdp_field_legal1_sdat_field_17'] ?? null,
                        'TaxRecordLink' => $property['real_property_search_link']['url'] ?? null,
                        'LegalDescription2' => $property['legal_description_line_2_mdp_field_legal2_sdat_field_18'] ?? null,
                        'LegalDescription3' => $property['legal_description_line_3_mdp_field_legal3_sdat_field_19'] ?? null,
                        'DeedReference1' => $property['deed_reference_1_liber_mdp_field_dr1liber_sdat_field_30'] ?? null,
                        'Deed Reference2' => $property['deed_reference_1_folio_mdp_field_dr1folio_sdat_field_31'] ?? null,
                        'TownCode' => $property['town_code_mdp_field_towncode_desctown_sdat_field_36'] ?? null,
                        'Subdivision Code' => $property['subdivision_code_mdp_field_subdivsn_sdat_field_37'] ?? null,
                        'Map' => $property['map_mdp_field_map_sdat_field_42'] ?? null,
                        'Grid' => $property['grid_mdp_field_grid_sdat_field_43'] ?? null,
                        'Parcel' => $property['parcel_mdp_field_parcel_sdat_field_44'] ?? null,
                        'ZoningCode' => $property['zoning_code_mdp_field_zoning_sdat_field_45'] ?? null,
                        'ResidenceType' => $property['mdp_street_address_type_code_mdp_field_resityp'] ?? null,
                        'UtilitiesWater' => $property['property_factors_utilities_water_mdp_field_pfuw_sdat_field_63'] ?? null,
                        'UtilitiesSewage' => $property['property_factors_utilities_sewer_mdp_field_pfus_sdat_field_64'] ?? null,

                    ];

                    if (isset($property['real_property_search_link'])) {
                        // Owner name not available from response so we have to follow the link provided in the results and get the owner's name from that page
                        $link = $property['real_property_search_link']['url'];
                        $page = new \DOMDocument();
                        libxml_use_internal_errors(true);
                        $page -> loadHTMLFile($link);
                        //echo $page -> saveHTML();

                        $owner1 = $page -> getElementById('MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_ucDetailsSearch_dlstDetaisSearch_lblOwnerName_0');
                        if (! $owner1) {
                            $owner1 = $page -> getElementById('MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_query_ucDetailsSearch_query_dlstDetaisSearch_lblOwnerName_0');
                        }

                        $owner2 = $page -> getElementById('MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_ucDetailsSearch_dlstDetaisSearch_lblOwnerName2_0');
                        if (! $owner2) {
                            $owner2 = $page -> getElementById('MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_query_ucDetailsSearch_query_dlstDetaisSearch_lblOwnerName2_0');
                        }

                        $details['Owner1'] = htmlspecialchars($this -> extractText($owner1));
                        $details['Owner2'] = htmlspecialchars($this -> extractText($owner2));

                        /* $details['frederick_city'] = 'no';
                        if(stristr($property['town_code_mdp_field_towncode_desctown_sdat_field_36'], 'Frederick')) { //MainContent_MainContent_cphMainContentArea_ucSearchType_wzrdRealPropertySearch_query_ucDetailsSearch_query_dlstDetaisSearch_lblSpecTaxTown_0
                            $details['frederick_city'] = 'yes';
                        }
                        $details['condo'] = 'no';
                        if(stristr($property['land_use_code_mdp_field_lu_desclu_sdat_field_50'], 'condominium')) {
                            $details['condo'] = 'yes';
                        } */
                    }
                } else {
                    return response() -> json([
                        'error' => 'yes',
                        'found' => count($properties),
                    ]);
                }
            }
        }

        return $details;
    }

    public function extractText($node) {
        if (XML_TEXT_NODE === $node -> nodeType || XML_CDATA_SECTION_NODE === $node -> nodeType) {
            return $node -> nodeValue;
        } elseif (XML_ELEMENT_NODE === $node -> nodeType || XML_DOCUMENT_NODE === $node -> nodeType || XML_DOCUMENT_FRAG_NODE === $node -> nodeType) {
            if ('script' === $node -> nodeName) {
                return '';
            }

            $text = '';
            foreach ($node -> childNodes as $childNode) {
                $text .= $this -> extractText($childNode);
            }

            return $text;
        }
    }
}
