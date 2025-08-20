<?php

namespace App\Http\Controllers;

use App\Models\BusTrip;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index()
    {
        $trips = BusTrip::query()
            ->where('seats_available', '>', 0)
            ->orderBy('departure_time')
            ->paginate(12);

        return view('transport.bus.index', compact('trips'));
    }

    public function show(BusTrip $trip)
    {
        return view('transport.bus.show', compact('trip'));
    }
}
