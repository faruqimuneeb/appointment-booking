<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\Slot;
use App\Models\ServiceOffDay;
use App\Models\ServiceHoliday;
use Illuminate\Database\Seeder;

class AppointmentsSeeder extends Seeder
{
    public function run()
    {
        $services = Service::all();
        
        foreach ($services as $service) {
            $slots = $service->slots;

            foreach ($slots as $slot) {
                $startDateTime = $slot->day->format('Y-m-d') . ' ' . $slot->start_time;
                $endDateTime = $slot->day->format('Y-m-d') . ' ' . $slot->end_time;

                $appointments = $this->generateAppointments($startDateTime, $endDateTime);

                foreach ($appointments as $appointmentDateTime) {
                    // Check if the appointment falls within valid slot hours
                    if (!$this->isWithinSlotHours($appointmentDateTime, $slot)) {
                        continue;
                    }

                    // Check if the appointment falls within any break times
                    if ($this->isWithinBreakTimes($appointmentDateTime, $slot)) {
                        continue;
                    }

                    // Check if the appointment falls on an off day or holiday
                    if ($this->isHoliday($appointmentDateTime, $service)) {
                        continue;
                    }
                    if ($this->isOffDay($appointmentDateTime, $service)) {
                        continue;
                    }

                    // Create the appointment
                    Appointment::create([
                        'service_id' => $service->id,
                        'slot_id' => $slot->id,
                        'appointment_datetime' => $appointmentDateTime,
                    ]);
                }
            }
        }
    }

    private function generateAppointments($startDateTime, $endDateTime)
    {
        $appointments = [];

        $currentDateTime = new \DateTime($startDateTime);
        $endDateTime = new \DateTime($endDateTime);

        while ($currentDateTime <= $endDateTime) {
            $appointments[] = $currentDateTime->format('Y-m-d H:i:s');
            $currentDateTime->modify('+10 minutes');
        }

        return $appointments;
    }

    private function isWithinSlotHours($appointmentDateTime, $slot)
    {
        $appointmentTime = strtotime($appointmentDateTime);
        $slotStartTime = strtotime($slot->start_time);
        $slotEndTime = strtotime($slot->end_time);

        return ($appointmentTime >= $slotStartTime && $appointmentTime <= $slotEndTime);
    }

    private function isWithinBreakTimes($appointmentDateTime, $slot)
    {
        $appointmentTime = strtotime($appointmentDateTime);
        $breakTimes = $slot->service->breaks;

        foreach ($breakTimes as $breakTime) {
            $breakStartTime = strtotime($breakTime->start_time);
            $breakEndTime = strtotime($breakTime->end_time);

            if ($appointmentTime >= $breakStartTime && $appointmentTime <= $breakEndTime) {
                return true;
            }
        }

        return false;
    }

    private function isHoliday($appointmentDateTime, $service)
    {
        $appointmentDate = date('Y-m-d', strtotime($appointmentDateTime));

        // Check if it's an off day (Sunday)
        if (date('N', strtotime($appointmentDate)) == 7) {
            return true;
        }

        // Check if it's a holiday
        $holiday = ServiceHoliday::where([
            'service_id' => $service->id,
            'date'=> $appointmentDate
        ])->first();

        return $holiday !== null;
    }

    private function isOffDay($appointmentDateTime, $service){
        $appointmentDate = date('Y-m-d', strtotime($appointmentDateTime));

        // Check if it's an off day (Sunday)
        if (date('N', strtotime($appointmentDate)) == 7) {
            return true;
        }

        // Check if it's a offday
        $holiday = ServiceOffDay::where([
            'service_id' => $service->id,
            'off_date'=> $appointmentDate
        ])->first();

        return $holiday !== null;
    }
}
