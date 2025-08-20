<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use App\Models\BusTrip;
use App\Models\Taxi;
use App\Models\Airport;
use App\Models\Site;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Services\LocalAiService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TravelAssistantController extends Controller
{
    public function index()
    {
        $airports = Airport::orderBy('city')->get();
        return view('assistant.index', compact('airports'));
    }

    public function plan(Request $request)
    {
        $data = $request->validate([
            'origin_iata' => ['nullable','string','max:8'],
            'start_date' => ['required','date','after_or_equal:today'],
            'end_date' => ['required','date','after_or_equal:start_date'],
            'budget' => ['required','numeric','min:0'],
            'interests' => ['nullable','array'],
        ]);

        $start = new \Carbon\Carbon($data['start_date']);
        $end = new \Carbon\Carbon($data['end_date']);

        // Suggest inbound flights to Burkina Faso near start date
        $flights = Flight::with(['origin','destination'])
            ->whereHas('destination', fn($q)=>$q->where('country','Burkina Faso'))
            ->when(!empty($data['origin_iata']), function($q) use ($data) {
                $code = trim($data['origin_iata']);
                $q->whereHas('origin', function($oq) use ($code) {
                    $oq->where('iata_code', 'LIKE', $code)
                       ->orWhere('city','LIKE', "%$code%")
                       ->orWhere('name','LIKE', "%$code%");
                });
            })
            ->whereDate('departure_time', '>=', $start->copy()->subDays(2))
            ->whereDate('departure_time', '<=', $start->copy()->addDays(3))
            ->orderBy('departure_time')
            ->take(5)
            ->get();

        // Suggest domestic bus trips during stay
        $busTrips = BusTrip::with('bus')
            ->whereDate('departure_time', '>=', $start)
            ->whereDate('departure_time', '<=', $end)
            ->orderBy('departure_time')
            ->take(5)
            ->get();

        // Taxis available (for airport transfer estimate)
        $taxis = Taxi::take(3)->get();

        // Simple budget split heuristic
        $budget = (float)$data['budget'];
        $split = [
            'transport' => round($budget * 0.4, 2),
            'hebergement' => round($budget * 0.35, 2),
            'activites' => round($budget * 0.15, 2),
            'restauration' => round($budget * 0.1, 2),
        ];

        // Sites: filter by interests/categories if provided and roughly by activity budget
        $sitesQuery = Site::query();
        if (!empty($data['interests'])) {
            $cats = $data['interests'];
            $sitesQuery->where(function($q) use ($cats) {
                foreach ($cats as $c) {
                    $q->orWhere('category', 'LIKE', "%$c%");
                    $q->orWhere('name', 'LIKE', "%$c%");
                }
            });
        }
        // Filter by budget portion reserved for activities when price ranges exist
        $activitiesBudget = (float) round($data['budget'] * 0.15, 2);
        $sitesQuery->when($activitiesBudget > 0, function($q) use ($activitiesBudget) {
            $q->where(function($w) use ($activitiesBudget) {
                $w->whereNull('price_max')->orWhere('price_max', '<=', $activitiesBudget);
            });
        });
        $sites = $sitesQuery->orderBy('city')->take(8)->get();

        // Events during the stay window
        $events = Event::query()
            ->whereDate('starts_at', '>=', $start->copy()->subDays(1))
            ->whereDate('starts_at', '<=', $end->copy()->addDays(1))
            ->orderBy('starts_at')
            ->take(8)
            ->get();

        // Build a simple day-by-day itinerary
        $itinerary = [];
        $sitesIdx = 0;
        $days = (int) $start->diffInDays($end) + 1;
        for ($d = 0; $d < $days; $d++) {
            $date = $start->copy()->addDays($d);
            $dayKey = $date->toDateString();
            $dayItems = [];
            // Add events of the day first
            foreach ($events as $ev) {
                if ($date->isSameDay(new \Carbon\Carbon($ev->starts_at))) {
                    $dayItems[] = [
                        'type' => 'event',
                        'title' => $ev->title,
                        'city' => $ev->city,
                        'time' => (string) $ev->starts_at,
                        'lat' => $ev->latitude,
                        'lng' => $ev->longitude,
                    ];
                }
            }
            // Add up to 2 sites for this day (cycle list)
            $addSites = min(2, max(0, $sites->count()));
            for ($i = 0; $i < $addSites; $i++) {
                $site = $sites[$sitesIdx % max(1,$sites->count())] ?? null;
                $sitesIdx++;
                if ($site) {
                    $dayItems[] = [
                        'type' => 'site',
                        'title' => $site->name,
                        'city' => $site->city,
                        'time' => null,
                        'lat' => $site->latitude,
                        'lng' => $site->longitude,
                    ];
                }
            }
            $itinerary[$dayKey] = $dayItems;
        }

        // Map points: all suggested sites and dated events within window
        $mapPoints = [];
        foreach ($sites as $s) {
            if ($s->latitude && $s->longitude) {
                $mapPoints[] = [
                    'label' => $s->name,
                    'city' => $s->city,
                    'type' => 'site',
                    'lat' => (float) $s->latitude,
                    'lng' => (float) $s->longitude,
                ];
            }
        }
        foreach ($events as $e) {
            if ($e->latitude && $e->longitude) {
                $mapPoints[] = [
                    'label' => $e->title,
                    'city' => $e->city,
                    'type' => 'event',
                    'lat' => (float) $e->latitude,
                    'lng' => (float) $e->longitude,
                ];
            }
        }

        // Persist for later actions (export/preview/add)
        session(['assistant_plan' => [
            'input' => $data,
            'flights' => $flights,
            'busTrips' => $busTrips,
            'taxis' => $taxis,
            'budgetSplit' => $split,
            'sites' => $sites,
            'events' => $events,
            'itinerary' => $itinerary,
            'mapPoints' => $mapPoints,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
        ]]);

        return view('assistant.plan', [
            'input' => $data,
            'flights' => $flights,
            'busTrips' => $busTrips,
            'taxis' => $taxis,
            'budgetSplit' => $split,
            'sites' => $sites,
            'events' => $events,
            'itinerary' => $itinerary,
            'mapPoints' => $mapPoints,
            'start' => $start,
            'end' => $end,
        ]);
    }

    public function export(Request $request)
    {
        $data = session('assistant_plan');
        if (!$data) {
            return redirect()->route('assistant.index')->with('status', 'Aucun plan à exporter.');
        }
        $filename = 'plan-'.date('Ymd-His').'.json';
        return response()->streamDownload(function() use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }

    public function preview()
    {
        $data = session('assistant_plan');
        if (!$data) {
            return redirect()->route('assistant.index')->with('status', 'Aucun plan à prévisualiser.');
        }
        $today = now()->toDateString();
        $items = $data['itinerary'][$today] ?? [];
        return view('assistant.preview', [
            'date' => $today,
            'items' => $items,
        ]);
    }

    public function addItem(Request $request)
    {
        $type = (string) $request->query('type', 'site');
        $id = (string) $request->query('id');
        $label = (string) $request->query('label', 'Élément');
        $plan = session('assistant_plan');
        if (!$plan) {
            return back()->with('status', 'Générez d\'abord un plan.');
        }
        $custom = session('assistant_custom', []);
        $custom[] = [
            'type' => $type,
            'id' => $id,
            'label' => $label,
            'added_at' => now()->toDateTimeString(),
        ];
        session(['assistant_custom' => $custom]);
        return back()->with('status', 'Ajouté à votre itinéraire.');
    }

    public function recent()
    {
        $plan = session('assistant_plan');
        $custom = session('assistant_custom', []);
        return view('assistant.recent', compact('plan','custom'));
    }

    public function options()
    {
        return view('assistant.options');
    }

    public function aiSuggest(Request $request)
    {
        // Allow longer processing for first LLM call (model warm-up)
        @set_time_limit(180);
        $data = $request->validate([
            'prompt' => ['required','string','max:5000'],
            'context' => ['nullable','array'],
        ]);

        // Compose a concise system-style prompt with context
        $ctx = $data['context'] ?? [];
        $summary = "Vous êtes un assistant de voyage pour un touriste visitant le Burkina Faso. Répondez en français, avec des conseils concrets, structurés en puces courtes, et tenez compte du budget si fourni.";
        $planBits = [];
        if (!empty($ctx['dates'])) $planBits[] = 'Dates: '.$ctx['dates'];
        if (!empty($ctx['budget'])) $planBits[] = 'Budget: '.$ctx['budget'].' FCFA';
        if (!empty($ctx['cities'])) $planBits[] = 'Villes: '.implode(', ', (array)$ctx['cities']);
        if (!empty($ctx['sites'])) $planBits[] = 'Sites: '.implode(', ', array_slice((array)$ctx['sites'], 0, 10));
        if (!empty($ctx['events'])) $planBits[] = 'Événements: '.implode(', ', array_slice((array)$ctx['events'], 0, 10));
        $contextText = $summary.'\nContexte: '.implode(' | ', $planBits).'\n\nQuestion: '.$data['prompt'].'\nRéponse:';

        $llm = new LocalAiService();
        $resp = $llm->generate($contextText, [
            // Keep responses snappy; adjust as needed
            'options' => [
                'num_predict' => 256, // limit tokens to avoid long generations
                // 'temperature' => 0.7,
            ],
        ]);

        if (!($resp['ok'] ?? false)) {
            return response()->json(['ok' => false, 'error' => $resp['error'] ?? 'Erreur inconnue'], 502);
        }
        return response()->json(['ok' => true, 'text' => $resp['text'] ?? '']);
    }

    public function aiStream(Request $request)
    {
        // Streaming runs until done
        @set_time_limit(0);
        $prompt = (string) $request->query('prompt', '');
        $ctx = $request->query('context');
        if (is_string($ctx)) {
            $ctx = json_decode($ctx, true) ?: [];
        } elseif (!is_array($ctx)) {
            $ctx = [];
        }
        if (trim($prompt) === '') {
            return response('Missing prompt', 400);
        }

        $summary = "Vous êtes un assistant de voyage pour un touriste visitant le Burkina Faso. Répondez en français, avec des conseils concrets, structurés en puces courtes, et tenez compte du budget si fourni.";
        $planBits = [];
        if (!empty($ctx['dates'])) $planBits[] = 'Dates: '.$ctx['dates'];
        if (!empty($ctx['budget'])) $planBits[] = 'Budget: '.$ctx['budget'].' FCFA';
        if (!empty($ctx['cities'])) $planBits[] = 'Villes: '.implode(', ', (array)$ctx['cities']);
        if (!empty($ctx['sites'])) $planBits[] = 'Sites: '.implode(', ', array_slice((array)$ctx['sites'], 0, 8));
        if (!empty($ctx['events'])) $planBits[] = 'Événements: '.implode(', ', array_slice((array)$ctx['events'], 0, 8));
        $contextText = $summary.'\nContexte: '.implode(' | ', $planBits).'\n\nQuestion: '.$prompt.'\nRéponse:';

        $host = env('OLLAMA_HOST', 'http://localhost:11434');
        $model = env('OLLAMA_MODEL', 'mistral');

        $response = new StreamedResponse(function() use ($host, $model, $contextText) {
            $client = \Illuminate\Support\Facades\Http::withOptions(['stream' => true])->timeout(0);
            $res = $client->post(rtrim($host,'/').'/api/generate', [
                'model' => $model,
                'prompt' => $contextText,
                'stream' => true,
            ]);

            if (!$res->successful()) {
                echo "data: ".json_encode(['error' => 'LLM HTTP '.$res->status()])."\n\n";
                @ob_flush(); @flush();
                return;
            }

            $body = $res->toPsrResponse()->getBody();
            $buffer = '';
            while (!$body->eof()) {
                $chunk = $body->read(1024);
                if ($chunk === '') { usleep(10000); continue; }
                $buffer .= $chunk;
                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);
                    if ($line === '') continue;
                    $json = json_decode($line, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        if (isset($json['response']) && $json['response'] !== '') {
                            echo 'data: '.str_replace(["\r","\n"], [' ',' '], $json['response'])."\n\n";
                            @ob_flush(); @flush();
                        }
                        if (!empty($json['done'])) {
                            echo "event: done\n";
                            echo "data: ok\n\n";
                            @ob_flush(); @flush();
                            return;
                        }
                    }
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ]);

        return $response;
    }
}
