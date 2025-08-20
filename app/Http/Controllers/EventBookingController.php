<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventBooking;
use App\Models\Ticket;
use App\Jobs\GenerateTicketPdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventBookingController extends Controller
{
    // Show booking form for a public event
    public function create(Event $event)
    {
        $event->load('ticketTypes');
        // Prevent self-booking: organizers cannot book their own events
        if (Auth::check() && (Auth::id() === ($event->organizer_id ?? null))) {
            return redirect()->route('events.show', $event)
                ->withErrors(['booking' => "Vous ne pouvez pas réserver votre propre évènement."]);
        }
        // Relaxed filter: consider all active types available regardless of sales window
        $available = $event->ticketTypes->filter(function($tt) {
            return $tt->status === 'active';
        });
        if ($available->isEmpty()) {
            return redirect()->route('events.show', $event)
                ->with('status', 'Aucun billet n\'est disponible pour cet évènement.');
        }
        // Attach a temp property for the view
        $event->setRelation('ticketTypes', $available->values());
        return view('bookings.create', compact('event'));
    }

    // Process booking and issue tickets
    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'buyer_name' => ['required','string','max:120'],
            'buyer_email' => ['required','email','max:160'],
            'quantities' => ['required','array'],
            'quantities.*' => ['nullable','integer','min:0','max:50'],
        ]);

        // Restrict booking to tourists only
        if (Auth::check()) {
            $role = Auth::user()->role ?? 'tourist';
            if (in_array($role, ['hotel_manager','guide','restaurant_manager','admin'])) {
                return back()->withErrors([
                    'buyer_name' => "Seuls les touristes peuvent réserver des évènements.",
                ])->withInput();
            }
            // Prevent self-booking even if role passes middleware (defense-in-depth)
            if (Auth::id() === ($event->organizer_id ?? null)) {
                return back()->withErrors([
                    'buyer_name' => "Vous ne pouvez pas réserver votre propre évènement.",
                ])->withInput();
            }
        }

        $event->load('ticketTypes');
        // Relaxed filter: active types only
        $available = $event->ticketTypes->filter(function($tt) {
            return $tt->status === 'active';
        })->keyBy('id');
        $quantities = collect($data['quantities'] ?? []);
        $selected = $quantities->filter(fn($q) => (int)$q > 0);
        abort_if($selected->isEmpty(), 422, 'Aucune quantité sélectionnée.');

        // Compute total
        $total = 0;
        foreach ($selected as $typeId => $qty) {
            $type = $available->get($typeId);
            if (!$type) { continue; }
            $total += ($type->price ?? 0) * (int)$qty;
        }

        $booking = EventBooking::create([
            'event_id' => $event->id,
            'user_id' => Auth::id(),
            'buyer_name' => $data['buyer_name'],
            'buyer_email' => $data['buyer_email'],
            'status' => 'confirmed',
            'total_amount' => $total,
        ]);

        $tickets = [];
        foreach ($selected as $typeId => $qty) {
            $qty = (int)$qty;
            for ($i = 0; $i < $qty; $i++) {
                $tickets[] = Ticket::create([
                    'ticket_type_id' => $typeId,
                    'booking_id' => $booking->id,
                    'uuid' => (string) Str::uuid(),
                    'status' => 'issued',
                    'issued_at' => now(),
                ]);
            }
        }

        // Kick off async PDF generation for each ticket (WeasyPrint if available)
        foreach ($tickets as $t) {
            try { GenerateTicketPdf::dispatch($t->uuid); } catch (\Throwable $e) { /* ignore */ }
        }

        // If only one ticket, send user straight to live preview
        if (count($tickets) === 1) {
            return redirect()->route('tickets.show.uuid', $tickets[0]->uuid);
        }

        return redirect()->route('bookings.show', $booking)->with('ticket_ids', collect($tickets)->pluck('uuid')->all());
    }

    // Booking confirmation + list of tickets
    public function show(EventBooking $booking)
    {
        $booking->load(['event', 'tickets.type']);
        return view('bookings.show', compact('booking'));
    }

    // List bookings for current tourist
    public function mine(Request $request)
    {
        $bookings = EventBooking::with(['event','tickets'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(12);
        return view('bookings.mine', compact('bookings'));
    }
}
