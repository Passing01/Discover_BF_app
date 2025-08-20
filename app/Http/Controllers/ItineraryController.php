<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\Guide;
use Carbon\Carbon;

class ItineraryController extends Controller
{
    // Show planning form (dates, interests, budget)
    public function create()
    {
        return view('tourist.plan');
    }

    // Store plan in session (simple rule-based placeholder)
    public function store(Request $request)
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'interests' => ['required', 'array', 'min:1'],
            'budget' => ['required', 'in:low,medium,high'],
        ]);

        // Générer un plan dynamique basé sur les dates et intérêts
        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = Carbon::parse($data['end_date'])->startOfDay();
        $daysCount = $start->diffInDays($end) + 1; // inclusif
        $daysCount = max(1, min($daysCount, 21)); // borne 1..21

        // Pools simplifiés par intérêt
        $interestPools = [
            'culture' => [
                ['city' => 'Ouagadougou', 'activity' => 'Sites historiques et arts traditionnels'],
                ['city' => 'Bobo-Dioulasso', 'activity' => 'Mosquée, vieille ville et musique'],
            ],
            'craft' => [
                ['city' => 'Bobo-Dioulasso', 'activity' => 'Ateliers artisanaux et marchés locaux'],
                ['city' => 'Koudougou', 'activity' => 'Tissage et sculpture'],
            ],
            'food' => [
                ['city' => 'Ouagadougou', 'activity' => 'Parcours gastronomique et spécialités locales'],
                ['city' => 'Banfora', 'activity' => 'Dégustations et cuisine de terroir'],
            ],
            'nature' => [
                ['city' => 'Banfora', 'activity' => 'Cascades de Karfiguéla et dômes de Fabédougou'],
                ['city' => 'Nazinga', 'activity' => 'Observation de la faune et paysages'],
            ],
        ];

        // Construire une file d’activités selon les intérêts choisis; fallback culture
        $selected = array_values(array_intersect(array_keys($interestPools), $data['interests'])) ?: ['culture'];
        $queue = [];
        foreach ($selected as $key) {
            $queue = array_merge($queue, $interestPools[$key]);
        }
        if (empty($queue)) {
            $queue = $interestPools['culture'];
        }

        // Génération jour par jour
        $plan = [];
        for ($i = 1; $i <= $daysCount; $i++) {
            $date = $start->copy()->addDays($i - 1)->toDateString();
            $item = $queue[($i - 1) % count($queue)];
            $plan[] = [
                'day' => $i,
                'date' => $date,
                'city' => $item['city'],
                'activity' => $item['activity'],
            ];
        }

        session([
            'itinerary' => [
                'user_id' => Auth::id(),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'interests' => $data['interests'],
                'budget' => $data['budget'],
                'days' => $plan,
            ],
        ]);

        return redirect()->route('tourist.itinerary')->with('status', 'Itinéraire généré.');
    }

    // Show current itinerary with suggestions
    public function show()
    {
        $itinerary = session('itinerary');
        $suggestedEvents = Event::query()->latest()->take(5)->get();
        $suggestedHotels = Hotel::query()->latest()->take(5)->get();
        $guides = Guide::query()->latest()->take(5)->get();

        return view('tourist.itinerary', compact('itinerary', 'suggestedEvents', 'suggestedHotels', 'guides'));
    }
}
