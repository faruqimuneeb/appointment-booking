<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Slot;
use Carbon\Carbon;

class SlotController extends Controller
{
    //

    public function getBookableSlots()
    {
        $startDate = Carbon::now()->startOfDay();
        $endDate = Carbon::now()->addDays(7)->endOfDay();
        
        // Retrieve the services that are bookable for the user
        $services = Service::all();
        
        $bookableSlots = [];

        foreach ($services as $service) {
            // Query the available slots within the date range for each service
            $slots = Slot::where('service_id', $service->id)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->where('is_bookable', true)
                ->get();
            $bookableSlots = [];
            foreach ($slots as $slot) {
                $bookableSlots[] = ["slot" => $slot, "service" => $slot->service];
            }
        }

        return response()->json([
            'bookable_slots' => $bookableSlots,
        ]);
    }
}
