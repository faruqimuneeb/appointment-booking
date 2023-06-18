<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Service extends Model
{
    use HasFactory;

    public function isDayOff(Carbon $date): bool
    {
        // Check if the provided date falls on a day that is marked as off
        return $this->offDays()->where('off_date', $date->format('Y-m-d'))->exists();
    }

    public function dayHasOffTime(Carbon $date, $start_time = '00:00:00', $end_time = '23:59:59') : bool
    {
        //check if any off times added for the day.
        return  $this->offDays()->where('off_date', $date->format('Y-m-d'))
        ->where('start_time', '>=', $start_time)
        ->where('end_time', '>=', $end_time)
        ->exists();
    }

    public function isHoliday(Carbon $date): bool
    {
        // Check if the provided date falls within any holiday period
        return $this->holidays()->where('date', '<=', $date)
            ->where('date', '>=', $date)
            ->exists();
    }

    public function isWithinBreakTimes(Carbon $start_time, Carbon $end_time, $day): bool
    {
        
        // Check if the provided time falls within any break time period
        $isBreakTime =  $this->breaks()->where('start_time', '>=', $start_time->format('H:i:s'))
            ->where('start_time', '<=', $end_time->format('H:i:s'))
            // ->orWhere(function($query) use ($start_time, $end_time, $day){
            //     $query->where('end_time', '>=', $start_time)
            //     ->where('start_time', '<=', $end_time)
            //     ->where('day_of_week', $day->dayName);
            // })
            ->where('day_of_week', $day->dayName)
            ->exists();
            return $isBreakTime;
    }

    public function breaks(){
        return $this->hasMany(ServiceBreak::class);
    }

    public function offDays(){
        return $this->hasMany(ServiceOffDay::class);
    }

    public function serviceHours(){
        return $this->hasOne(ServiceHour::class);
    }

    public function holidays()
    {
        return $this->hasMany(ServiceHoliday::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function generateSlots($startDate, $endDate, $openingTime, $closingTime)
    {
        $currentDate = Carbon::parse($startDate);
        $closingDateTime = Carbon::parse($closingTime);

        while ($currentDate <= $endDate) {
            $slots = [];

            // Generate slots based on the service's slot configuration
            $slotDuration = $this->slot_duration;
            $cleanupDuration = $this->slot_cleanup_duration;
            $startTime = Carbon::parse($openingTime);
            $endTime = $startTime->copy()->addMinutes($slotDuration);

            while ($endTime <= $closingDateTime) {
                $overlappingSlots = Slot::where('service_id', $this->id)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->exists();
                if (!$overlappingSlots) {
                    $slots[] = [
                        'service_id' => $this->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_bookable' => true,
                    ];
                }

                $startTime = $endTime->addMinutes($cleanupDuration);
                $endTime = $startTime->copy()->addMinutes($slotDuration);
            }

            // Store the generated slots in the database
            Slot::insert($slots);

            // Move to the next day
            $currentDate->addDay();
            $closingDateTime->addDay();
        }
    }

    public function serviceConfigurations(){
        return $this->hasOne(ServiceConfiguration::class);
    }

}
