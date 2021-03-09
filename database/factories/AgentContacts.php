<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\Models\CRM\CRMContacts;
use Faker\Generator as Faker;

$factory->define(CRMContacts::class, function (Faker $faker) {
    return [
        'Agent_ID' => '3193',
        'contact_first' => $faker->firstName,
        'contact_last' => $faker->lastName,
        'contact_email' => $faker->unique()->safeEmail,
        'contact_phone_cell' => $faker->tollFreePhoneNumber,
        'contact_phone_home' => $faker->tollFreePhoneNumber,
        'contact_street' => $faker->streetAddress,
        'contact_city' => $faker->city,
        'contact_state' => $faker->stateAbbr,
        'contact_zip' => $faker->postcode,
        'contact_type' => $faker->randomElement($array = ['Seller', 'Buyer']),
    ];
});
