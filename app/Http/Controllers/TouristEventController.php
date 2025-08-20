<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TouristEventController extends Controller
{
    public function index()
    {
        $events = Event::query()->orderBy('start_date')->paginate(12);
        return view('tourist.events.index', compact('events'));
    }

    public function show(Event $event)
    {
        $event->load('ticketTypes');
        return view('tourist.events.show', compact('event'));
    }
}
