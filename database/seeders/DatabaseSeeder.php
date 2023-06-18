<?php

namespace Database\Seeders;

// use App\Models\ServiceHoliday;
// use App\Models\Service;
// use App\Models\ServiceBreak;
// use App\Models\ServiceHour;
// use App\Models\ServiceOffDay;
// use Database\Seeders\ServiceHoursSeeder
use Illuminate\Database\Seeder;
// use SlotSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();
        $this->call(ServiceSeeder::class);
        $this->call(ServiceHoursSeeder::class);
        // $this->call(ServiceOffDaySee::class);
        $this->call(ServiceHolidaysSeeder::class);
        $this->call(ServiceBreaksSeeder::class);
        $this->call(ServiceConfigurationSeeder::class);
        $this->call(SlotSeeder::class);
    }
}
