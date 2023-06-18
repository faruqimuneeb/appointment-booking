<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
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
        $this->actingAs($user);

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

        // Create a slot for Men Haircut
        $menHaircutSlot = Slot::factory()->create([
            'service_id' => $menHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30)->setSecond(0),
            'is_bookable' => true
        ]);

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

        // Assert a successful response
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Appointment created successfully']);

        // Assert that the appointment was created in the database
        $this->assertDatabaseHas('appointment_details', [
            'appointment_id' => $response->appointment->id,
            // 'user_id' => $user->id,
            "phone_no" => "3144327451",
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        // Create a service for Women Haircut
        $womenHaircutService = Service::factory()->create([
            'name' => 'Women Haircut',
            "description" => '',
            'price' => 0.00,
            'max_clients_per_slot' => 3,
            'slot_duration' => 60,
            'slot_cleanup_duration' => 10,
            'is_active' => 1
        ]);

        // Create a slot for Women Haircut
        $womenHaircutSlot = Slot::factory()->create([
            'service_id' => $womenHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addDay()->setHour(14)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(14)->setMinute(30)->setSecond(0),
            'is_bookable' => true
        ]);

        // Make an API request to book an appointment for Women Haircut
        $response = $this->postJson('/api/book-appointment', [
            'slot_id' => $womenHaircutSlot->id,
            "people" => [
                [
                    'email' => 'jesica@example.com',
                    "phone_no" => "3144327451",
                    'first_name' => 'jesica',
                    'last_name' => 'Poe',
                ]
            ]
        ], $headers);

        // Assert a successful response
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Appointment created successfully']);

        // Assert that the appointment was created in the database
        $this->assertDatabaseHas('appointment_details', [
            'appointment_id' => $response->appointment->id,
            // 'user_id' => $user->id,
            "phone_no" => "3144327451",
            'email' => 'jane@example.com',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);

        // Perform additional assertions based on your user stories and business stories

        
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

        // Create a slot for Men Haircut
        $menHaircutSlotBeforeOpening = Slot::factory()->create([
            'service_id' => $menHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30)->setSecond(0),
            'is_bookable' => true
        ]);

        // Make an API request to book an appointment for Men Haircut
        $response = $this->postJson('/api/book-appointment', [
            'slot_id' => $menHaircutSlotBeforeOpening->id,
            "people" => [
                [
                    'email' => 'john@example.com',
                    "phone_no" => "3144327451",
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ]
            ]
        ],$headers);

        // Assert a successful response
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Cannot book an appointment before opening time']);

        // Assert that the appointment was created in the database
        $this->assertDatabaseHas('appointment_details', [
            'appointment_id' => $response->appointment->id,
            // 'user_id' => $user->id,
            "phone_no" => "3144327451",
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

         // Create a slot for Men Haircut
         $menHaircutSlotDuringBreak = Slot::factory()->create([
            'service_id' => $menHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30)->setSecond(0),
            'is_bookable' => true
        ]);

        // Make an API request to book an appointment for Men Haircut
        $response = $this->postJson('/api/book-appointment', [
            'slot_id' => $menHaircutSlotDuringBreak->id,
            "people" => [
                [
                    'email' => 'john@example.com',
                    "phone_no" => "3144327451",
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ]
            ]
        ], $headers);

        // Assert a successful response
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Cannot book an appointment during break time']);

        // Create a slot for Men Haircut
        $menHaircutSlotAfterOffTime = Slot::factory()->create([
            'service_id' => $menHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30)->setSecond(0),
            'is_bookable' => true
        ]);

        // Make an API request to book an appointment for Men Haircut
        $response = $this->postJson('/api/book-appointment', [
            'slot_id' => $menHaircutSlotAfterOffTime->id,
            "people" => [
                [
                    'email' => 'john@example.com',
                    "phone_no" => "3144327451",
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ]
            ]
        ], $headers);

        // Assert a successful response
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Cannot book an appointment after off time']);

        
        // Create a service for Men Haircut
        $womenHaircutService = Service::factory()->create([
            'name' => 'Women Haircut',
            "description" => '',
            'price' => 0.00,
            'max_clients_per_slot' => 3,
            'slot_duration' => 30,
            'slot_cleanup_duration' => 5,
            'is_active' => 1
        ]);

        // Create a slot for Men Haircut
        $womenHaircutSlotBeforeOpening = Slot::factory()->create([
            'service_id' => $womenHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30)->setSecond(0),
            'is_bookable' => true
        ]);

        // Make an API request to book an appointment for Men Haircut
        $response = $this->postJson('/api/book-appointment', [
            'slot_id' => $womenHaircutSlotBeforeOpening->id,
            "people" => [
                [
                    'email' => 'john@example.com',
                    "phone_no" => "3144327451",
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ]
            ]
        ],$headers);

        // Assert a successful response
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Cannot book an appointment before opening time']);

        // Assert that the appointment was created in the database
        $this->assertDatabaseHas('appointment_details', [
            'appointment_id' => $response->appointment->id,
            // 'user_id' => $user->id,
            "phone_no" => "3144327451",
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

         // Create a slot for Men Haircut
         $womenHaircutSlotDuringBreak = Slot::factory()->create([
            'service_id' => $womenHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30)->setSecond(0),
            'is_bookable' => true
        ]);

        // Make an API request to book an appointment for Men Haircut
        $response = $this->postJson('/api/book-appointment', [
            'slot_id' => $womenHaircutSlotDuringBreak->id,
            "people" => [
                [
                    'email' => 'john@example.com',
                    "phone_no" => "3144327451",
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ]
            ]
        ], $headers);

        // Assert a successful response
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Cannot book an appointment during break time']);

        // Create a slot for Men Haircut
        $womenHaircutSlotAfterOffTime = Slot::factory()->create([
            'service_id' => $womenHaircutService->id,
            'date' => now()->format('Y-m-d'),
            'start_time' => now()->addDay()->setHour(10)->setMinute(0)->setSecond(0),
            'end_time' => now()->addDay()->setHour(10)->setMinute(30)->setSecond(0),
            'is_bookable' => true
        ]);

        // Make an API request to book an appointment for Men Haircut
        $response = $this->postJson('/api/book-appointment', [
            'slot_id' => $womenHaircutSlotAfterOffTime->id,
            "people" => [
                [
                    'email' => 'john@example.com',
                    "phone_no" => "3144327451",
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ]
            ]
        ], $headers);
        // Assert a successful response
        $response->assertStatus(422);
        $response->assertJson(['message' => 'Cannot book an appointment after off time']);
        
    }

    // Implement additional test methods to cover other scenarios and user stories
}