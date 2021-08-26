<?php

namespace Database\Factories;

use App\Models\Management;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class ManagementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Management::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            // 'staff_id' => "GID".(strtoupper(substr(uniqid(), -7 ))),
            'email' => $this->faker->unique()->safeEmail(),
            'country_code' => $this->faker->randomDigit,
            'phone_number' => $this->faker->phoneNumber,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
        ];
    }
}
