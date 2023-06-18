<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Slot;
use App\Models\Service;
use Faker\Generator as Faker;

class SlotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = Slot::class;
    public function definition()
    {
        // Retrieve the service IDs available in the database
        $serviceIds = Service::pluck('id')->all();

        // Get a random service ID
        $serviceId = $this->faker->randomElement($serviceIds);

        // Retrieve the service details
        $service = Service::find($serviceId);
        // print_r($service->serviceHours);
        // exit();
        // Get the opening and closing hours from the service
        $openingHour = '08:00:00';//$service->serviceHours->opening_time;
        $closingHour = '20:00:00';//$service->serviceHours->closing_time;

        // Determine the slot duration and cleanup duration from the service
        $slotDuration = $service->slot_duration;
        $cleanupDuration = $service->slot_cleanup_duration;

        // Get the current date and time
        $now = now();

        // Calculate the start time for the slot
        $startTime = $now->format('Y-m-d') . ' ' . $openingHour;
        $startTime = $now->copy()->modify($startTime);

        // Calculate the end time for the slot
        $endTime = $startTime->copy()->addMinutes($slotDuration);

        return [
            'service_id' => $serviceId,
            'date' => $now->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_bookable' => true,
        ];
    }
}
