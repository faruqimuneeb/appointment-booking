<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\ServiceHour;
use App\Models\Slot;
use App\Models\Appointment;

use function PHPSTORM_META\map;

class BookAppointmentTest extends TestCase
{
    // use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Perform any additional setup steps specific to your application
    }

    /**
     * Test the appointment booking process.
     *
     * @return void
     */
    public function testAppointmentBooking()
    {
        // Create a user
        $user = User::factory()->create();

        // Access the authenticated user
        $authenticatedUser = auth()->user();
        // Generate a token for the user
        $token = $user->createToken('api_token')->plainTextToken;

        // Set the Authorization header with the Bearer token
        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];
        // Create a service for Men Haircut
        $menHaircutService = Service::factory()->create([
            'name' => 'Men Haircut',
            "description" => '',
            'price' => 0.00,
            'max_clients_per_slot' => 3,
            'slot_duration' => 30,
            'slot_cleanup_duration' => 5,
            'is_active' => 1
        ]);
        //Create Service Hours for this Service

        $serviceHours = ServiceHour::factory()->create([
            'day_of_week' => 'Sunday',
            'service_id' => $menHaircutService->id,
            'opening_time' => null,
            'closing_time' => null,
        ]);
        ServiceHour::factory()->create([
            'day_of_week' => 'Monday',
            'service_id' => $menHaircutService->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::factory()->create([
            'day_of_week' => 'Tuesday',
            'service_id' => $menHaircutService->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::factory()->create([
            'day_of_week' => 'Wednesday',
            'service_id' => $menHaircutService->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::factory()->create([
            'day_of_week' => 'Thursday',
            'service_id' => $menHaircutService->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::factory()->create([
            'day_of_week' => 'Friday',
            'service_id' => $menHaircutService->id,
            'opening_time' => '08:00',
            'closing_time' => '20:00',
        ]);
        ServiceHour::factory()->create([
            'day_of_week' => 'Saturday',
            'service_id' => $menHaircutService->id,
            'opening_time' => '10:00',
            'closing_time' => '22:00',
        ]);
        // Create a slot for Men Haircut
        $menHaircutSlot = Slot::factory()->create([
            'service_id' => $menHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->setHour(8)->setMinute(30)->setSecond(0),
            'end_time' => now()->setHour(9)->setMinute(05)->setSecond(0),
            'is_bookable' => true
        ]);
        // print_r($menHaircutSlot);
        // exit();
        // Make an API request to book an appointment for Men Haircut
        $response = $this->postJson('/api/book-appointment', [
            'slot_id' => $menHaircutSlot->id,
            "people" => [
                [
                    'email' => 'john@example.com',
                    "phone_no" => "3144327451",
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ]
            ]
        ],$headers);
        // print_r($response);
        // exit();
        // Assert a successful response
        $response->assertStatus(200);
        // $response->assertJsonFragment([
        //     // "appointment" => [
        //     //     "service_id",
        //     //     "slot_id",
        //     //     "user_id",
        //     //     'updated_at',
        //     //     'created_at',
        //     //     'id',
        //     // ],
        //     'message' => 'Appointment booked successfully',
        // ]);
        $response->assertJsonStructure([
            'appointment' => [
                'service_id',
                'slot_id',
                'user_id',
                'updated_at',
                'created_at',
                'id',
            ],
            'message',
        ]);
        $appointment = $appointment = $response->json()['appointment'];
        // Assert that the appointment was created in the database
        $this->assertDatabaseHas('appointment_details', [
            'appointment_id' => $appointment['id'],
            // 'user_id' => $user->id,
            "phone_no" => "3144327451",
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
        
    }

    // Implement additional test methods to cover other scenarios and user stories
}