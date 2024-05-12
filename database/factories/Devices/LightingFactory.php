<?php

namespace Database\Factories\Devices;

use App\Models\Devices\Lighting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends <Lighting>
 */
class LightingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ieee_address' => $this->faker->macAddress,
            'friendly_name' => $this->faker->word,
            'brightness' => $this->faker->numberBetween(0, 100),
            'energy' => $this->faker->randomFloat(2, 0, 100),
            'linkquality' => $this->faker->numberBetween(0, 100),
            'power' => $this->faker->randomFloat(2, 0, 100),
            'state' => $this->faker->randomElement(['on', 'off']),
        ];
    }
}
