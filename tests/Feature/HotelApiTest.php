<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de l'affichage des hôtels.
     */
    public function test_can_list_hotels()
    {
        Hotel::factory()->count(5)->create();

        $response = $this->getJson('/api/hotels');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

    /**
     * Test de l'affichage des chambres d'un hôtel.
     */
    public function test_can_list_hotel_rooms()
    {
        $hotel = Hotel::factory()->create();
        Room::factory()->count(10)->create(['hotel_id' => $hotel->id]);

        $response = $this->getJson("/api/hotels/{$hotel->id}/rooms");

        $response->assertStatus(200)
                 ->assertJsonCount(10, 'data');
    }
}