<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;

class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Service::class;

    public function definition()
    {
        return [
            // Define the factory attributes for the Service model
            'name' => $this->faker->word,
            'description' => null,
            'price' => 0.00,
            'max_clients_per_slot' => 3,
            'slot_duration' => $this->faker->numberBetween(30, 60),
            'slot_cleanup_duration' => $this->faker->numberBetween(5, 15),
            'is_active' => true
            // ... other attributes
        ];
    }
}
