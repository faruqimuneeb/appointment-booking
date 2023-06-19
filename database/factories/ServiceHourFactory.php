<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;
use App\Models\ServiceHour;


class ServiceHourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = ServiceHour::class;
    public function definition()
    {
        return [
            'service_id' => function () {
                return Service::factory()->create()->id;
            },
            'day_of_week' => $this->faker->dayOfWeek(),
            'opening_time' => $this->faker->time('H_i_s'),
            'closing_time' => $this->faker->time('H_i_s')
        ];
    }
}
