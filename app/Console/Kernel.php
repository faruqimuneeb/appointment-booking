<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Service;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $startDate = now()->toDateString(); // Start date for creating slots
        

        $schedule->call(function () use ($startDate, $endDate) {
            $services = Service::all();

            foreach ($services as $service) {
                $endDate = now()->addDays($service->serviceConfigurations->days_to_generate_slots)->toDateString(); // End date for creating slots
                // Check if the service is active
                if ($service->is_active) {
                    // Get the opening and closing hours for the service
                    $serviceHours = $service->serviceHours()->where('day', now()->format('l'))->first();

                    // Check if the service is open on the current day
                    if ($serviceHours && $service->isDayOff() === false && $service->isHoliday() === false) {
                        // Generate slots based on the service configuration
                        $service->generateSlots($startDate, $endDate, $serviceHours->opening_time, $serviceHours->closing_time);
                    }
                }
            }
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
