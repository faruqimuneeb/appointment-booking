<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Retrieve the services
        $services = \App\Models\Service::all();

        foreach ($services as $service) {
            // Create a service configuration with default values
            \App\Models\ServiceConfiguration::create([
                'service_id' => $service->id,
                'days_to_generate_slots' => 7,//$service->serviceConfigurations->days_to_generate_slots,
            ]);
        }
    }
}
