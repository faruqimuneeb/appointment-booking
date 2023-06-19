<?php

namespace Database\Factories;

use App\Models\ServiceBreak;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ServiceBreakFactory extends Factory
{
    protected $model = ServiceBreak::class;

    public function definition()
    {
        $dayOfWeek = $this->faker->numberBetween(0, 6);
        $dayOfWeekName = Carbon::create()->startOfWeek()->addDays($dayOfWeek)->format('l');

        return [
            'service_id' => function () {
                return \App\Models\Service::factory()->create()->id;
            },
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'day_of_week' => $dayOfWeekName,
            'start_time' => $this->faker->time('H:i:s'),
            'end_time' => $this->faker->time('H:i:s'),
        ];
    }
}
