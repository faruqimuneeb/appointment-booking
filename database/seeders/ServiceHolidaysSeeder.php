<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\ServiceHoliday;

class ServiceHolidaysSeeder extends Seeder
{
    public function run()
    {
        $services = Service::all();
        $publicHolidayDate = Carbon::now()->addDays(2)->toDateString();

        foreach ($services as $service) {
            ServiceHoliday::create([
                'service_id' => $service->id,
                'date' => $publicHolidayDate,
                'description' => 'Public Holiday',
            ]);

            // Add additional service holidays as needed
            // ...
        }
    }
}
