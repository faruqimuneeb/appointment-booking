<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;
use App\Models\ServiceHours;


class ServiceHourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = ServiceHours::class;
    public function definition()
    {
        // Retrieve the service IDs available in the database
        $serviceIds = Service::pluck('id')->all();

        // Get a random service ID
        $serviceId = $this->faker->randomElement($serviceIds);

        // Generate opening and closing hours for each weekday
        $serviceHours = [];
        $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        foreach ($weekdays as $weekday) {
            $serviceHours[] = [
                'service_id' => $serviceId,
                'day_of_week' => $weekday,
                'opening_time' => $weekday=="Sunday" ? null: $this->faker->time('H:i:s'),
                'closing_time' => $weekday=="Sunday" ? null: $this->faker->time('H:i:s'),
            ];
        }

        return $serviceHours;
    }
}
