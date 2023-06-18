<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Service;
use App\Models\ServiceOffDay;
use App\Models\ServiceHour;
use App\Models\Slot;

class SlotSeeder extends Seeder
{
    public function run()
    {
        // $menHaircutService = Service::where('name', 'Men Haircut')->first();
        // $womenHaircutService = Service::where('name', 'Woman Haircut')->first();
        $services = Service::all();

        $startDate = Carbon::now()->startOfDay();
        $endDate = $startDate->copy()->addDays(7);

        $currentDate = $startDate->copy();
        
        foreach($services as $service){
            // $service_hours = ServiceHours::where('service_id', $service->id);
            // $service_breaks = ServiceBreaks::where('service_id', $service->id);
            // $service_holidays = ServiceOffDays::where('service_id', $service->id);
            while ($currentDate <= $endDate) {
                if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                    $slots = [];
                    // exit(" => ".$currentDate->dayName);
                    $service_hours = ServiceHour::where([
                        'service_id' => $service->id,
                        'day_of_week' => $currentDate->dayName
                    ])->first();
                    // print_r($service_hours);
                    // exit();
                    // $service_hours = $service_hours->get()[0];
                    
                    $slots = $this->generateSlots($service, $currentDate, $service_hours->opening_time, $service_hours->closing_time, $service->slot_duration, $service->max_clients_per_slot);
                    
                    // if ($currentDate->addHours(8)->lte($endDate)) {
                    //     // Generate slots for Men Haircut service
                        // $slots = $this->generateSlots($service, $currentDate, $service_hours->opening_time, $service_hours->closing_time, $service->slot_duration, $service->max_clients_per_slot);
                    // }
    
                    // if ($currentDate->addHours(2)->lte($endDate)) {
                    //     // Generate slots for Woman Haircut service
                    //     $slots = array_merge($slots, $this->generateSlots($womenHaircutService, $currentDate, '08:00', '20:00', 60, 3));
                    // }
    
                    foreach ($slots as $slot) {
                        Slot::create([
                            'service_id' => $slot['service_id'],
                            'date' => $slot['date'],
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                        ]);
                    }
                }
    
                $currentDate->addDay();
            }
        }
    }

    private function generateSlots($service, $currentDate, $startTime, $endTime, $slotDuration, $maxClients)
    {
        $slots = [];
        $time = Carbon::parse($currentDate->format('Y-m-d') . ' ' . $startTime);

        while ($time <= $currentDate->copy()->setTimeFromTimeString($endTime)) {
            $slotEndTime = $time->copy()->addMinutes($slotDuration);

            if ($this->isSlotValid($time, $slotEndTime, $service->id)) {
                $slots[] = [
                    'service_id' => $service->id,
                    'date' => $time->toDateString(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ];
            }

            $time->addMinutes($slotDuration + 5); // Add slot duration and cleanup break
        }

        return $slots;
    }

    private function isSlotValid($startTime, $endTime, $serviceId)
    {
        $dayOfWeek = Carbon::parse($startTime)->dayOfWeek;

        // Check if the day is an off day (Sunday)
        if ($dayOfWeek === Carbon::SUNDAY) {
            return false;
        }

        // Get the service by ID
        $service = Service::find($serviceId);

        // Check if the slot falls within the opening and closing hours of the given day
        if (!$this->isWithinOpeningHours($startTime, $endTime, $service)) {
            return false;
        }

        // Check if the slot falls on a holiday
        if ($this->isHoliday($startTime, $service)) {
            return false;
        }

        // Check if the slot falls within any breaks
        if ($this->isWithinBreaks($startTime, $endTime, $service)) {
            return false;
        }

        return true;
    }

    private function isWithinOpeningHours($startTime, $endTime, $service)
    {
        // $openingTime = Carbon::parse($startTime)->setTimeFromTimeString($service->opening_time);
        // $closingTime = Carbon::parse($startTime)->setTimeFromTimeString($service->closing_time);

        $service_hours = ServiceHour::where('day_of_week', Carbon::parse($startTime)->dayName)->first();
        $openingTime = $service_hours->opening_time;
        $closingTime = $service_hours->closing_time;
        // Check if the slot falls within the opening and closing hours
        if ($openingTime > $startTime || $closingTime < $endTime) {
            return false;
        }

        return true;
    }

    private function isHoliday($dateTime, $service)
    {
        $holiday = ServiceOffDay::where('off_date', Carbon::parse($dateTime)->toDateString())
                        ->where('service_id', $service->id)
                        ->first();

        return $holiday !== null;
    }

    private function isWithinBreaks($startTime, $endTime, $service)
    {
        $breaks = $service->breaks()->get();

        foreach ($breaks as $break) {
            $breakStart = Carbon::parse($startTime)->setTimeFromTimeString($break->start_time);
            $breakEnd = Carbon::parse($startTime)->setTimeFromTimeString($break->end_time);

            // Check if the slot overlaps with any breaks
            if ($breakStart <= $endTime && $breakEnd >= $startTime) {
                return true;
            }
        }

        return false;
    }

}
