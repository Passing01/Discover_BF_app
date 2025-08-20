<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventBooking;
use App\Models\TicketTemplate;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventCreatorController extends Controller
{
    protected function ensureRole(): void
    {
        if (Auth::user()?->role !== 'event_organizer' && !Auth::user()?->isAdmin()) {
            abort(403);
        }
    }

    public function index()
    {
        $this->ensureRole();
        $events = Event::where('organizer_id', Auth::id())->latest()->paginate(12);
        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        $this->ensureRole();
        $templates = TicketTemplate::where('user_id', Auth::id())->orderBy('name')->get();
        return view('organizer.events.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $this->ensureRole();
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date','after_or_equal:start_date'],
            'location' => ['required','string','max:255'],
            'ticket_price' => ['nullable','numeric','min:0'],
            'category' => ['nullable','string','max:100'],
            'image' => ['required','image','max:4096'],
            'ticket_template_id' => ['nullable','string'],
            'ticket_types' => ['nullable','array'],
            'ticket_types.*.name' => ['required_with:ticket_types','string','max:120'],
            'ticket_types.*.price' => ['nullable','numeric','min:0'],
            'ticket_types.*.capacity' => ['nullable','integer','min:0'],
            'ticket_types.*.sales_start_at' => ['nullable','date'],
            'ticket_types.*.sales_end_at' => ['nullable','date','after_or_equal:ticket_types.*.sales_start_at'],
        ]);
        $data['organizer_id'] = Auth::id();
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $data['image_path'] = $path;
        }
        $event = Event::create($data);
        // Persist ticket types if provided
        $firstTypeId = null;
        if (!empty($data['ticket_types'])) {
            foreach ($data['ticket_types'] as $tt) {
                if (!empty($tt['name'])) {
                    $createdType = $event->ticketTypes()->create([
                        'name' => $tt['name'],
                        'description' => $tt['description'] ?? null,
                        'price' => $tt['price'] ?? 0,
                        'currency' => 'XOF',
                        'capacity' => $tt['capacity'] ?? null,
                        'sales_start_at' => $tt['sales_start_at'] ?? null,
                        'sales_end_at' => $tt['sales_end_at'] ?? null,
                        'status' => 'active',
                    ]);
                    if ($firstTypeId === null) { $firstTypeId = $createdType->id; }
                }
            }
        }
        // Ensure at least one ticket type exists
        if ($firstTypeId === null) {
            $default = $event->ticketTypes()->create([
                'name' => 'Standard',
                'description' => 'Billet standard',
                'price' => $data['ticket_price'] ?? 0,
                'currency' => 'XOF',
                'status' => 'active',
            ]);
            $firstTypeId = $default->id;
        }
        // Option A: issue a test ticket and provide a link
        $ticketLink = null;
        if ($firstTypeId) {
            $ticket = Ticket::create([
                'ticket_type_id' => $firstTypeId,
                'booking_id' => null,
                'uuid' => (string) Str::uuid(),
                'status' => 'issued',
                'issued_at' => now(),
            ]);
            $ticketLink = route('tickets.show.uuid', $ticket->uuid);
        }
        return redirect()->route('organizer.events.index')->with([
            'status' => 'Évènement créé.',
            'ticket_link' => $ticketLink,
        ]);
    }

    public function edit(Event $event)
    {
        $this->ensureRole();
        abort_unless($event->organizer_id === Auth::id() || Auth::user()->isAdmin(), 403);
        $event->load('ticketTypes');
        $templates = TicketTemplate::where('user_id', Auth::id())->orderBy('name')->get();
        return view('organizer.events.edit', compact('event','templates'));
    }

    public function update(Request $request, Event $event)
    {
        $this->ensureRole();
        abort_unless($event->organizer_id === Auth::id() || Auth::user()->isAdmin(), 403);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'start_date' => ['required','date'],
            'end_date' => ['required','date','after_or_equal:start_date'],
            'location' => ['required','string','max:255'],
            'ticket_price' => ['nullable','numeric','min:0'],
            'category' => ['nullable','string','max:100'],
            'image' => ['nullable','image','max:4096'],
            'ticket_template_id' => ['nullable','string'],
            'ticket_types' => ['nullable','array'],
            'ticket_types.*.id' => ['nullable','string'],
            'ticket_types.*.name' => ['required_with:ticket_types','string','max:120'],
            'ticket_types.*.price' => ['nullable','numeric','min:0'],
            'ticket_types.*.capacity' => ['nullable','integer','min:0'],
            'ticket_types.*.sales_start_at' => ['nullable','date'],
            'ticket_types.*.sales_end_at' => ['nullable','date','after_or_equal:ticket_types.*.sales_start_at'],
        ]);
        // Image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('events', 'public');
            $data['image_path'] = $path;
        }
        $event->update($data);
        // Sync ticket types (create/update). Basic approach: upsert by id if present, else create
        $keptIds = [];
        if (!empty($data['ticket_types'])) {
            foreach ($data['ticket_types'] as $tt) {
                if (!empty($tt['id'])) {
                    $event->ticketTypes()->where('id', $tt['id'])->update([
                        'name' => $tt['name'],
                        'description' => $tt['description'] ?? null,
                        'price' => $tt['price'] ?? 0,
                        'capacity' => $tt['capacity'] ?? null,
                        'sales_start_at' => $tt['sales_start_at'] ?? null,
                        'sales_end_at' => $tt['sales_end_at'] ?? null,
                    ]);
                    $keptIds[] = $tt['id'];
                } elseif (!empty($tt['name'])) {
                    $created = $event->ticketTypes()->create([
                        'name' => $tt['name'],
                        'description' => $tt['description'] ?? null,
                        'price' => $tt['price'] ?? 0,
                        'currency' => 'XOF',
                        'capacity' => $tt['capacity'] ?? null,
                        'sales_start_at' => $tt['sales_start_at'] ?? null,
                        'sales_end_at' => $tt['sales_end_at'] ?? null,
                        'status' => 'active',
                    ]);
                    $keptIds[] = $created->id;
                }
            }
        }
        // Delete ticket types not present anymore
        if (!empty($keptIds)) {
            $event->ticketTypes()->whereNotIn('id', $keptIds)->delete();
        }
        return redirect()->route('organizer.events.index')->with('status', 'Évènement mis à jour.');
    }

    public function destroy(Event $event)
    {
        $this->ensureRole();
        abort_unless($event->organizer_id === Auth::id() || Auth::user()->isAdmin(), 403);
        // Prevent deletion if bookings exist
        if ($event->bookings()->exists()) {
            return redirect()->route('organizer.events.index')->with('status', "Impossible de supprimer: des réservations existent.");
        }
        // Delete related ticket types
        $event->ticketTypes()->delete();
        // Remove stored image if any
        if (!empty($event->image_path)) {
            Storage::disk('public')->delete($event->image_path);
        }
        $event->delete();
        return redirect()->route('organizer.events.index')->with('status', "Évènement supprimé.");
    }

    // Organizer: list all bookings (sales) for their events
    public function salesIndex(Request $request)
    {
        $this->ensureRole();
        $query = $this->salesBaseQuery($request);
        $bookings = (clone $query)->latest()->paginate(15)->withQueryString();
        $totalAmount = (clone $query)->sum('total_amount');
        $events = Event::where('organizer_id', Auth::id())->orderBy('start_date','desc')->get(['id','name']);
        return view('organizer.bookings.index', compact('bookings','totalAmount','events'));
    }

    // Organizer: show a single booking if belongs to their event
    public function salesShow(EventBooking $booking)
    {
        $this->ensureRole();
        $booking->load(['event','tickets.type']);
        abort_unless(($booking->event?->organizer_id) === Auth::id() || Auth::user()->isAdmin(), 403);
        return view('organizer.bookings.show', ['booking' => $booking]);
    }

    protected function salesBaseQuery(Request $request)
    {
        $query = EventBooking::with(['event','tickets'])
            ->whereHas('event', function($q){ $q->where('organizer_id', Auth::id()); });

        // Filters
        $status = strtolower(trim((string) $request->input('status')));
        if ($status && in_array($status, ['paid','pending','cancelled','refunded'])) {
            $query->where('status', $status);
        }
        $eventId = $request->input('event_id');
        if (!empty($eventId)) {
            $query->where('event_id', $eventId);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    // Organizer: export filtered sales as CSV
    public function salesExport(Request $request)
    {
        $this->ensureRole();
        $query = $this->salesBaseQuery($request)->latest();
        $filename = 'organizer-sales-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function() use ($query) {
            $handle = fopen('php://output', 'w');
            // CSV header
            fputcsv($handle, ['Reference','Event','Buyer','Email','Amount','Currency','Status','Created At']);
            $query->chunk(500, function($rows) use ($handle) {
                foreach ($rows as $b) {
                    fputcsv($handle, [
                        $b->id,
                        $b->event?->name,
                        $b->buyer_name ?? $b->user?->name,
                        $b->buyer_email ?? $b->user?->email,
                        $b->total_amount,
                        $b->currency ?? 'XOF',
                        $b->status,
                        optional($b->created_at)->format('Y-m-d H:i:s'),
                    ]);
                }
            });
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
