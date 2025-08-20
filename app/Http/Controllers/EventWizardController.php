<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EventWizardController extends Controller
{
    private string $sessionKey = 'event_wizard';

    protected function ensureOrganizer(): void
    {
        if (Auth::user()?->role !== 'event_organizer' && !Auth::user()?->isAdmin()) {
            abort(403);
        }
    }

    public function start(Request $request)
    {
        $this->ensureOrganizer();
        // reset draft if asked
        if ($request->boolean('reset')) {
            $request->session()->forget($this->sessionKey);
        }
        if (!$request->session()->has($this->sessionKey)) {
            $request->session()->put($this->sessionKey, [
                'step' => 1,
                'data' => [
                    'name' => null,
                    'start_date' => null,
                    'end_date' => null,
                    'location' => null,
                    'description' => null,
                    'category' => null,
                    'ticket_price' => null,
                    'ticket_template_id' => null,
                ],
            ]);
        }
        return redirect()->route('organizer.events.wizard.show', ['step' => 1]);
    }

    public function show(Request $request, int $step)
    {
        $this->ensureOrganizer();
        $draft = $request->session()->get($this->sessionKey);
        if (!$draft) {
            return redirect()->route('organizer.events.wizard.start');
        }
        $step = max(1, min($step, 5));
        return view('organizer.events.wizard.steps.step'.$step, [
            'draft' => $draft['data'],
            'currentStep' => $step,
        ]);
    }

    public function submit(Request $request, int $step)
    {
        $this->ensureOrganizer();
        $draft = $request->session()->get($this->sessionKey);
        if (!$draft) {
            return redirect()->route('organizer.events.wizard.start');
        }
        $step = (int)$step;
        switch ($step) {
            case 1: // Infos de base
                $data = $request->validate([
                    'name' => ['required','string','max:160'],
                    'start_date' => ['required','date'],
                    'end_date' => ['nullable','date','after_or_equal:start_date'],
                    'location' => ['required','string','max:200'],
                ]);
                $draft['data'] = array_merge($draft['data'], $data);
                $request->session()->put($this->sessionKey, $draft);
                // après step1, ouvrir la sélection d'affiches sur la page step2 (via modal JS)
                return redirect()->route('organizer.events.wizard.show', 2)->with('open_templates_modal', true);

            case 2: // Détails
                $data = $request->validate([
                    'description' => ['required','string','max:2000'],
                    'category' => ['required','string','max:100'],
                    'ticket_price' => ['nullable','numeric','min:0'],
                ]);
                $draft['data'] = array_merge($draft['data'], $data);
                $request->session()->put($this->sessionKey, $draft);
                return redirect()->route('organizer.events.wizard.show', 3);

            case 3: // Organisateur (actuellement minimal, on peut étendre)
                // Pas de champs obligatoires ici pour MVP, l'organisateur est l'utilisateur courant
                return redirect()->route('organizer.events.wizard.show', 4);

            case 4: // Affiche: doit avoir un template choisi
                $data = $request->validate([
                    'ticket_template_id' => ['required','uuid','exists:ticket_templates,id'],
                ]);
                $draft['data'] = array_merge($draft['data'], $data);
                $request->session()->put($this->sessionKey, $draft);
                return redirect()->route('organizer.events.wizard.show', 5);

            case 5: // Vérifier & Publier
                // Validation finale
                $this->validateFinal($draft['data']);
                $event = new Event();
                $event->organizer_id = Auth::id();
                $event->name = $draft['data']['name'];
                $event->description = $draft['data']['description'];
                $event->start_date = $draft['data']['start_date'];
                $event->end_date = $draft['data']['end_date'];
                $event->location = $draft['data']['location'];
                $event->category = $draft['data']['category'];
                $event->ticket_price = $draft['data']['ticket_price'];
                $event->ticket_template_id = $draft['data']['ticket_template_id'];
                $event->save();

                // Clear draft
                $request->session()->forget($this->sessionKey);

                return redirect()->route('organizer.events.edit', $event)->with('status', "Événement créé. Vous pouvez maintenant peaufiner l'affiche si nécessaire.");
        }
        abort(404);
    }

    public function suggestions(Request $request)
    {
        $this->ensureOrganizer();
        $name = trim((string)$request->get('name', ''));
        $q = TicketTemplate::query()->where('user_id', Auth::id());
        if ($name !== '') {
            $q->where('name', 'like', '%'.$name.'%');
        }
        $local = $q->orderBy('name')->limit(12)->get(['id','name','bg_image_path','primary_color','secondary_color'])
            ->map(function($t){
                return [
                    'kind' => 'local',
                    'id' => $t->id,
                    'name' => $t->name,
                    'bg_image_path' => $t->bg_image_path,
                    'primary_color' => $t->primary_color,
                    'secondary_color' => $t->secondary_color,
                ];
            })->all();

        $external = [];
        $query = $name !== '' ? $name : 'event poster';
        // Try Pexels first if API key exists
        $pexelsKey = config('services.pexels.key') ?? env('PEXELS_API_KEY');
        if ($pexelsKey) {
            try {
                $resp = Http::withHeaders([
                    'Authorization' => $pexelsKey,
                ])->get('https://api.pexels.com/v1/search', [
                    'query' => $query,
                    'per_page' => 12,
                    'orientation' => 'portrait',
                ]);
                if ($resp->ok()) {
                    foreach ((array)$resp->json('photos') as $ph) {
                        $external[] = [
                            'kind' => 'external',
                            'provider' => 'pexels',
                            'id' => 'pexels_'.$ph['id'],
                            'name' => $ph['alt'] ?: $query,
                            'image_url' => $ph['src']['large2x'] ?? ($ph['src']['large'] ?? $ph['src']['original'] ?? null),
                            'thumb_url' => $ph['src']['medium'] ?? $ph['src']['small'] ?? null,
                            'attribution' => $ph['photographer_url'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // ignore external API errors for UX
            }
        } else {
            // Fallback Unsplash if key exists
            $unsplashKey = config('services.unsplash.key') ?? env('UNSPLASH_ACCESS_KEY');
            if ($unsplashKey) {
                try {
                    $resp = Http::withHeaders([
                        'Accept-Version' => 'v1',
                        'Authorization' => 'Client-ID '.$unsplashKey,
                    ])->get('https://api.unsplash.com/search/photos', [
                        'query' => $query,
                        'per_page' => 12,
                        'orientation' => 'portrait',
                    ]);
                    if ($resp->ok()) {
                        foreach ((array)$resp->json('results') as $ph) {
                            $external[] = [
                                'kind' => 'external',
                                'provider' => 'unsplash',
                                'id' => 'unsplash_'.$ph['id'],
                                'name' => $ph['description'] ?? $ph['alt_description'] ?? $query,
                                'image_url' => $ph['urls']['regular'] ?? $ph['urls']['full'] ?? null,
                                'thumb_url' => $ph['urls']['small'] ?? null,
                                'attribution' => $ph['links']['html'] ?? null,
                            ];
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        }

        return response()->json(array_merge($local, $external));
    }

    public function choosePoster(Request $request, TicketTemplate $template)
    {
        $this->ensureOrganizer();
        $draft = $request->session()->get($this->sessionKey) ?: ['data' => []];
        $draft['data']['ticket_template_id'] = $template->id;
        $request->session()->put($this->sessionKey, $draft);
        return back()->with('status', "Affiche sélectionnée: {$template->name}");
    }

    public function chooseExternal(Request $request)
    {
        $this->ensureOrganizer();
        $data = $request->validate([
            'image_url' => ['required','url'],
            'name' => ['nullable','string','max:160'],
            'provider' => ['nullable','string','max:40'],
        ]);
        try {
            $resp = Http::get($data['image_url']);
            if (!$resp->ok()) {
                return back()->withErrors(['image_url' => 'Impossible de télécharger l\'image sélectionnée.']);
            }
            $bytes = $resp->body();
            $ext = 'jpg';
            $mime = $resp->header('Content-Type');
            if ($mime === 'image/png') { $ext = 'png'; }
            elseif ($mime === 'image/webp') { $ext = 'webp'; }
            $filename = 'templates/'.Str::uuid().'.'.$ext;
            \Storage::disk('public')->put($filename, $bytes);

            $tpl = new TicketTemplate();
            $tpl->user_id = Auth::id();
            $tpl->name = $data['name'] ?: 'Affiche importée';
            $tpl->bg_image_path = $filename;
            $tpl->primary_color = null;
            $tpl->secondary_color = null;
            $tpl->overlay_fields = [
                'fields' => [
                    ['type' => 'text','value' => 'event_name','x' => 20,'y' => 20,'fontSize' => 22,'color' => '#ffffff'],
                    ['type' => 'text','value' => 'start_date','x' => 20,'y' => 40,'fontSize' => 16,'color' => '#ffffff'],
                    ['type' => 'text','value' => 'location','x' => 20,'y' => 55,'fontSize' => 16,'color' => '#ffffff'],
                ],
            ];
            $tpl->save();

            $draft = $request->session()->get($this->sessionKey) ?: ['data' => []];
            $draft['data']['ticket_template_id'] = $tpl->id;
            $request->session()->put($this->sessionKey, $draft);
            return back()->with('status', "Affiche importée et sélectionnée: {$tpl->name}");
        } catch (\Throwable $e) {
            return back()->withErrors(['image_url' => 'Erreur lors de l\'import de l\'image.']);
        }
    }

    private function validateFinal(array $d): void
    {
        validator($d, [
            'name' => ['required','string','max:160'],
            'start_date' => ['required','date'],
            'end_date' => ['nullable','date','after_or_equal:start_date'],
            'location' => ['required','string','max:200'],
            'description' => ['required','string','max:2000'],
            'category' => ['required','string','max:100'],
            'ticket_template_id' => ['required','uuid','exists:ticket_templates,id'],
        ])->validate();
    }
}
