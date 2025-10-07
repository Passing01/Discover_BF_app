<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Resources\HotelResource;
use App\Http\Resources\RoomResource;

class HotelController extends Controller
{
    /**
     * Afficher la liste des hôtels.
     */
    public function index()
    {
        $hotels = Hotel::with('rooms')->get();
        // dd($hotels);
        return HotelResource::collection($hotels);
    }

    /**
     * Afficher les chambres d'un hôtel spécifique.
     */
    public function showRooms(Hotel $hotel)
    {
        $rooms = $hotel->rooms;
        return RoomResource::collection($rooms);
    }
}