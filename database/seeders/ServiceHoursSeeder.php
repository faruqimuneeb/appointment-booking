<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceHour;

class ServiceHoursSeeder extends Seeder
{
    public function run()
    {
        // Men's Haircut Service Hours
        $menHaircut = Service::where('name', 'Men Haircut')->first();
        ServiceHour::create([
            'day_of_week' => 'Sunday',
            'service_id' => $menHaircut->id,
            'opening_time' => null,
            'closing_time' => null,
        ]);
        ServiceHour::create([
            'day_of_week' => 'Monday',
            'service_id' => $menHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Tuesday',
            'service_id' => $menHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Wednesday',
            'service_id' => $menHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Thursday',
            'service_id' => $menHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Friday',
            'service_id' => $menHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Saturday',
            'service_id' => $menHaircut->id,
            'opening_time' => '10:00',
            'closing_time' => '22:00',
        ]);

        // Women's Haircut Service Hours
        $womenHaircut = Service::where('name', 'Women Haircut')->first();
        ServiceHour::create([
            'day_of_week' => 'Sunday',
            'service_id' => $womenHaircut->id,
            'opening_time' => null,
            'closing_time' => null,
        ]);
        ServiceHour::create([
            'day_of_week' => 'Monday',
            'service_id' => $womenHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Tuesday',
            'service_id' => $womenHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Wednesday',
            'service_id' => $womenHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Thursday',
            'service_id' => $womenHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Friday',
            'service_id' => $womenHaircut->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Saturday',
            'service_id' => $womenHaircut->id,
            'opening_time' => '10:00',
            'closing_time' => '22:00',
        ]);

        // Hair Colouring Service Hours
        $hairColouring = Service::where('name', 'Hair Colouring')->first();
        ServiceHour::create([
            'day_of_week' => 'Sunday',
            'service_id' => $hairColouring->id,
            'opening_time' => null,
            'closing_time' => null,
        ]);
        ServiceHour::create([
            'day_of_week' => 'Monday',
            'service_id' => $hairColouring->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Tuesday',
            'service_id' => $hairColouring->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Wednesday',
            'service_id' => $hairColouring->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Thursday',
            'service_id' => $hairColouring->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Friday',
            'service_id' => $hairColouring->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::create([
            'day_of_week' => 'Saturday',
            'service_id' => $hairColouring->id,
            'opening_time' => '10:00',
            'closing_time' => '22:00',
        ]);
    }
}
