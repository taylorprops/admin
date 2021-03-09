<?php



namespace Database\Factories\CRM;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Model;
use App\Models\CRM\CRMContacts;

class CRMContactsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CRMContacts::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'Agent_ID' => '3193',
            'contact_first' => $this->faker->firstName,
            'contact_last' => $this->faker->lastName,
            'contact_email' => $this->faker->unique()->safeEmail,
            'contact_phone_cell' => $this->faker->tollFreePhoneNumber,
            'contact_phone_home' => $this->faker->tollFreePhoneNumber,
            'contact_street' => $this->faker->streetAddress,
            'contact_city' => $this->faker->city,
            'contact_state' => $this->faker->stateAbbr,
            'contact_zip' => $this->faker->postcode,
            'contact_type' => $this->faker->randomElement($array = ['Seller', 'Buyer']),
        ];
    }
}
