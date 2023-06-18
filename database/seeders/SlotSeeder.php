<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Slot;
use App\Models\ServiceHour;
use Carbon\Carbon;

class SlotSeeder extends Seeder
{
    public function run()
    {
        $services = Service::all();
        
        foreach ($services as $service) {
            
            $slotDuration = $service->slot_duration;
            $cleanupDuration = $service->slot_cleanup_duration;
            // $slotInterval = $service->slot_interval;
            $maxClientsPerSlot = $service->max_clients_per_slot;

            $currentDay = Carbon::now()->startOfDay();
            
            // Generate slots for the next 7 days
            for ($i = 0; $i < $service->serviceConfigurations->days_to_generate_slots; $i++) {
                
                $day = $currentDay->copy()->addDays($i);
                $serviceHours = ServiceHour::where('service_id', $service->id)
                ->whereNotNull('opening_time')
                ->whereNotNull('closing_time')
                ->where('day_of_week', $day->dayName)
                ->first();
                if(!$serviceHours) continue;
                $openingHours = $serviceHours->opening_time;
                $closingHours = $serviceHours->closing_time;
                
                // Generate slots within the opening and closing hours
                $startTime = $day->copy()->setTimeFromTimeString($openingHours);
                $endTime = $day->copy()->setTimeFromTimeString($closingHours);
                // Check if the day is an off day or holiday
                if ($service->isDayOff($day) || $service->isHoliday($day) || $service->dayHasOffTime($startTime)) {
                    continue;
                }

                while ($startTime->addMinutes($slotDuration)->lte($endTime)) {
                    // Check if the slot falls within a break time
                    if ($service->isWithinBreakTimes($startTime, $endTime, $day)) {
                        $startTime->addMinutes($slotDuration + $cleanupDuration);
                        continue;
                    }
                    
                    $slot = new Slot();
                    $slot->service_id = $service->id;
                    $slot->date = $startTime->toDateString();
                    $slot->start_time = $startTime->toTimeString();
                    $slot->end_time = $startTime->copy()->addMinutes($slotDuration)->toTimeString();
                    $slot->is_bookable =  1;
                    $slot->save();
                    // Move to the next slot
                    $startTime->addMinutes($cleanupDuration);
                }
            }
        }
    }
}
