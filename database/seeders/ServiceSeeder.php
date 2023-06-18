<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        Service::create([
            'name' => 'Men Haircut',
            'max_clients_per_slot' => 3,
            'slot_duration' => 30,
            'slot_cleanup_duration' => 5,
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Women Haircut',
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'slot_cleanup_duration' => 10,
            'is_active' => true,
        ]);

        Service::create([
            'name' => 'Hair Colouring',
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'slot_cleanup_duration' => 10,
            'is_active' => true,
        ]);
    }
}
