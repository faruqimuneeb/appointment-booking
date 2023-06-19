<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentDetails;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function bookAppointment(Request $request){
        $request->validate([
            'slot_id' => 'required|exists:slots,id', // Assuming you have 'slot_id' as the input field for slot ID
            // 'appointment_date' => 'date',
            // 'start_time' => 'date_format:H:i:s',
            // 'end_time' => 'date_format:H:i:s',
            'people' => 'required|array',
            'people.*.email' => 'required|email',
            'people.*.phone_no' => 'required',
            'people.*.first_name' => 'required|string',
            'people.*.last_name' => 'required|string',
        ]);
        
        $slotId = $request->input('slot_id');
        $people = $request->input('people');
        // \DB::enableQueryLog();
        $slot = Slot::findOrFail($slotId);
        // dd(\DB::getQueryLog());
        if(!$slot){
            return response()->json(['message' => 'Slot not found for given date/time'], 404);
        }
        //check if slot is already books, not available or not bookable(seeder data)...
        if(!$slot->is_bookable){
            return response()->json(['message' => 'Slot is not available for booking'], 400);
        }
        $service = $slot->service;
        // print_r($service);
        // exit();
        $day = Carbon::parse($slot->date);
        //check if slot does not starts before opening hours...
        $serviceHours = $service->serviceHours()->where('service_id', $service->id)
        ->whereNotNull('opening_time')
        ->whereNotNull('closing_time')
        ->where('day_of_week', $day->dayName)
        ->first();
        if(!$serviceHours){
            return response()->json(['message' => 'Selected time is off time for this service, please select some other time for appointment.'], 400); 
        }
        $openingHours = $serviceHours->opening_time;
        $closingHours = $serviceHours->closing_time;

        $startTime = Carbon::parse($slot->start_time);
        $endTime = Carbon::parse($slot->end_time);
        // echo $openingHours."\n";
        // exit($slot->start_time);
        if($startTime->isBefore($openingHours) || $endTime->isAfter($closingHours)){
            return response()->json(['message' => 'Selected slot is not in the working hours, please select another'], 400); 
        }
        // dd(\DB::getQueryLog());
        //check if given time for the day is not an off time...
        // exit($slot->start_time);
        // \DB::enableQueryLog();
        if($service->dayHasOffTime(Carbon::parse($slot->date), $slot->start_time, $slot->end_time)){
            return response()->json(['message' => 'Selected time is off time for this service, please select some other time for appointment.'], 400); 
        }
        //check if given date is off day or not.
        if($service->isDayoff(Carbon::parse($slot->date))){
            return response()->json(['message' => 'Selected date is off day, please select some other date/time for appointment.'], 400); 
        }

        //check if given date is holiday or not.
        if($service->isHoliday(Carbon::parse($slot->date))){
            return response()->json(['message' => 'Selected slot lies in holiday, please select some other date/time for appointment.'], 400); 
        }

        //check if given time for the day is not an break time...
        if($service->isWithinBreakTimes(Carbon::parse($slot->start_time), Carbon::parse($slot->end_time), Carbon::parse($slot->date))){
            return response()->json(['message' => 'Selected date/time is break time, please select some other date/time for appointment.'], 400); 
        }

        //check if people are max than the service allowed clients or not...
        if(count($people)> $service->max_clients_per_slot){
            return response()->json(['message' => 'Only '.$service->max_clients_per_slot.' can book this slot in meantime.'], 400);
        }

        $appointment = new Appointment();
        $appointment->service_id = $service->id;
        $appointment->slot_id = $slot->id;
        $appointment->user_id = Auth::user()->id;
        $appointment->save();

        foreach($people as $client){
            $person = new AppointmentDetails();
            // $appointmentDetails->appointment_id = $client['appointment_id'];
            $person->email = $client['email'] ;
            $person->phone_no = $client['phone_no'] ;
            $person->first_name = $client['first_name'] ;
            $person->last_name = $client['last_name'] ;
            $appointment->appointment_details()->save($person);
        }
        $slot->is_bookable = false;
        $slot->save();

        return response()->json([
            'appointment' => $appointment,
            'message' => 'Appointment booked successfully'
        ], 200);
    }
}