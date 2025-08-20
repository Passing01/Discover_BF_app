<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guide;
use App\Models\TourBooking;
use App\Models\EventBooking;
use App\Models\GuideContact;

class GuideDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $guide = Guide::where('user_id', $user->id)->first();

        $tourBookings = collect();
        $eventBookings = collect();
        $contacts = collect();

        if ($guide) {
            $tourBookings = TourBooking::where('guide_id', $guide->id)->latest()->take(10)->get();
            $eventBookings = EventBooking::where('guide_id', $guide->id)->latest()->take(10)->get();
        }

        // GuideContact stores guide_id as the User id for the guide (see TouristSiteController)
        $contacts = GuideContact::where('guide_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('guide.dashboard', compact('user', 'guide', 'tourBookings', 'eventBookings', 'contacts'));
    }

    public function updateAvailability(Request $request)
    {
        $data = $request->validate([
            'available_from' => ['required', 'date'],
            'available_to' => ['required', 'date', 'after_or_equal:available_from'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $guide = Guide::firstOrCreate(
            ['user_id' => Auth::id()],
            ['name' => Auth::user()->name ?? 'Guide', 'bio' => '']
        );

        // Persist minimal availability on the guide (if columns exist) or stash in session as fallback
        if ($guide->fillable && (in_array('available_from', $guide->getFillable()) || $guide->isFillable('available_from'))) {
            $guide->available_from = $data['available_from'];
            $guide->available_to = $data['available_to'];
            $guide->availability_note = $data['note'] ?? null;
            $guide->save();
        } else {
            session(['guide_availability' => $data]);
        }

        return back()->with('status', 'Disponibilité mise à jour.');
    }

    public function messagesIndex(Request $request)
    {
        $user = Auth::user();
        $query = GuideContact::where('guide_id', $user->id)->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        $contacts = $query->paginate(15)->withQueryString();
        return view('guide.messages.index', compact('contacts'));
    }

    public function messagesMarkRead(GuideContact $contact)
    {
        abort_if($contact->guide_id !== Auth::id(), 403);
        if ($contact->status === 'new') {
            $contact->status = 'contacted';
            $contact->save();
        }
        return back()->with('status', 'Message marqué comme lu.');
    }

    public function messagesClose(GuideContact $contact)
    {
        abort_if($contact->guide_id !== Auth::id(), 403);
        if ($contact->status !== 'closed') {
            $contact->status = 'closed';
            $contact->save();
        }
        return back()->with('status', 'Message fermé.');
    }

    public function editProfile()
    {
        $user = Auth::user();
        $guide = Guide::firstOrCreate(
            ['user_id' => $user->id],
            [
                'spoken_languages' => [],
                'hourly_rate' => null,
                'description' => '',
                'certified' => false,
            ]
        );
        return view('guide.profile.edit', compact('guide'));
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'description' => ['nullable','string','max:2000'],
            'spoken_languages' => ['nullable','string','max:500'],
            'hourly_rate' => ['nullable','numeric','min:0'],
            'certified' => ['nullable','boolean'],
        ]);

        $guide = Guide::firstOrCreate(['user_id' => Auth::id()]);

        // Parse languages: comma or semicolon separated into array
        $langs = isset($data['spoken_languages']) && strlen($data['spoken_languages'])
            ? collect(preg_split('/[;,]/', $data['spoken_languages']))->map(fn($s) => trim($s))->filter()->values()->all()
            : [];

        $guide->description = $data['description'] ?? '';
        $guide->spoken_languages = $langs;
        $guide->hourly_rate = $data['hourly_rate'] ?? null;
        $guide->certified = (bool)($data['certified'] ?? false);
        $guide->save();

        return redirect()->route('guide.profile.edit')->with('status', 'Profil mis à jour.');
    }
}
