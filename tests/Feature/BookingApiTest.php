<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de la création d'une réservation.
     */
    public function test_can_create_booking()
    {
        $user = User::factory()->create();
        $room = Room::factory()->create(['available' => true]);

        $this->actingAs($user);

        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['data' => ['id', 'room_id', 'start_date', 'end_date']]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);
    }

    /**
     * Test de l'affichage d'une réservation.
     */
    public function test_can_view_booking()
    {
        $booking = Booking::factory()->create();

        $response = $this->getJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'room_id', 'start_date', 'end_date']]);
    }
}