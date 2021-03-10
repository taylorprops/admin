<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this -> faker -> name,
            'email' => $this -> faker -> unique() -> safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$wTl1kq.f36ClPg2pVUomAOXWkeGKBdSMHoAYMCNYeGiiiqky0mqDK', // password
            'remember_token' => Str::random(10),
        ];
    }
}
