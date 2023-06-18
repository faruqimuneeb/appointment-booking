<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceBreak;

class ServiceBreaksSeeder extends Seeder
{
    public function run()
    {
        $services = Service::all();

        // Define breaks for each service
        foreach ($services as $service) {
            $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            foreach ($daysOfWeek as $day) {
                // Lunch Break
                ServiceBreak::create([
                    'service_id' => $service->id,
                    'day_of_week' => $day,
                    'name' => 'Lunch Break',
                    'start_time' => '12:00',
                    'end_time' => '13:00',
                ]);

                // Cleaning Break
                ServiceBreak::create([
                    'service_id' => $service->id,
                    'day_of_week' => $day,
                    'name' => 'Cleaning Break',
                    'start_time' => '15:00',
                    'end_time' => '16:00',
                ]);

                // Add additional breaks as needed for each day of the week
                // ...
            }
        }
    }
}

