<?php

namespace App\Http\Controllers;

use App\Models\TicketTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Tickets\WeasyPrintRenderer;
use Illuminate\Support\Facades\Storage;

class TicketTemplateController extends Controller
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
        $templates = TicketTemplate::where('user_id', Auth::id())->orderBy('name')->paginate(15);
        return view('organizer.templates.index', compact('templates'));
    }

    public function create()
    {
        $this->ensureRole();
        return view('organizer.templates.create');
    }

    // Dedicated page just to create a template from a PDF
    public function createPdf()
    {
        $this->ensureRole();
        return view('organizer.templates.pdf_upload');
    }

    public function store(Request $request)
    {
        $this->ensureRole();
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'primary_color' => ['nullable','string','max:20'],
            'secondary_color' => ['nullable','string','max:20'],
            'text_color' => ['nullable','string','max:20'],
            'bg_image' => ['nullable','image','max:4096'],
            'pdf_template' => ['nullable','file','mimetypes:application/pdf','max:15360'],
            'overlay_color' => ['nullable','string','max:20'],
            'overlay_opacity' => ['nullable','numeric','min:0','max:1'],
            'logo_enabled' => ['nullable','boolean'],
            'logo_position' => ['nullable','string','max:20'],
            'logo_size' => ['nullable','integer','min:24','max:256'],
            'corner_radius' => ['nullable','integer','min:0','max:64'],
            'card_shadow_enabled' => ['nullable','boolean'],
            'logo_placement' => ['nullable','string','max:40'],
            'image_placement' => ['nullable','string','max:40'],
            'shape' => ['nullable','string','max:40'],
            'font_family' => ['nullable','string','max:60'],
            'qr_position' => ['nullable','string','max:40'],
            'qr_size' => ['nullable','integer','min:32','max:512'],
            'overlay_fields' => ['nullable','array'],
        ]);
        $payload = $data;
        // normalize booleans
        $payload['logo_enabled'] = (bool)($request->boolean('logo_enabled'));
        $payload['card_shadow_enabled'] = (bool)($request->boolean('card_shadow_enabled'));
        // normalize colors (allow French names)
        foreach (['primary_color','secondary_color','text_color','overlay_color'] as $cf) {
            if (!empty($payload[$cf])) {
                $payload[$cf] = $this->normalizeColor($payload[$cf]);
            }
        }
        $payload['user_id'] = Auth::id();
        if ($request->hasFile('bg_image')) {
            $payload['bg_image_path'] = $request->file('bg_image')->store('ticket_templates/'.Auth::id(), 'public');
        }
        if ($request->hasFile('pdf_template')) {
            $payload['pdf_path'] = $request->file('pdf_template')->store('ticket_templates/'.Auth::id(), 'public');
            // Try to detect page count with FPDI
            try {
                if (class_exists('setasign\\Fpdi\\Fpdi')) {
                    $fpdi = new \setasign\Fpdi\Fpdi();
                    $fpdi->setSourceFile(Storage::disk('public')->path($payload['pdf_path']));
                    $payload['pdf_page_count'] = $fpdi->setSourceFile(Storage::disk('public')->path($payload['pdf_path']));
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        if (!empty($data['overlay_fields'])) {
            $payload['overlay_fields'] = $data['overlay_fields'];
        }
        TicketTemplate::create($payload);
        return redirect()->route('organizer.templates.index')->with('status', 'Modèle créé.');
    }

    // Handle dedicated PDF-only creation and redirect to overlay editor immediately
    public function storePdf(Request $request)
    {
        $this->ensureRole();
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'pdf_template' => ['nullable','file','mimetypes:application/pdf','max:15360'],
            'bg_image' => ['nullable','image','max:4096'],
        ]);
        if (!$request->hasFile('pdf_template') && !$request->hasFile('bg_image')) {
            return back()->withErrors(['pdf_template' => 'Veuillez fournir au moins un PDF ou une image de fond.'])->withInput();
        }
        $payload = [
            'name' => $data['name'],
            'user_id' => Auth::id(),
        ];
        if ($request->hasFile('pdf_template')) {
            $payload['pdf_path'] = $request->file('pdf_template')->store('ticket_templates/'.Auth::id(), 'public');
            try {
                if (class_exists('setasign\\Fpdi\\Fpdi')) {
                    $fpdi = new \setasign\Fpdi\Fpdi();
                    $payload['pdf_page_count'] = $fpdi->setSourceFile(Storage::disk('public')->path($payload['pdf_path']));
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        if ($request->hasFile('bg_image')) {
            $payload['bg_image_path'] = $request->file('bg_image')->store('ticket_templates/'.Auth::id(), 'public');
        }
        $template = TicketTemplate::create($payload);
        $msg = $request->hasFile('pdf_template')
            ? 'PDF importé. Définissez les placements.'
            : "Image importée sans PDF. Vous pourrez définir des placements (utilisés par l'aperçu HTML et le fallback).";
        return redirect()->route('organizer.templates.overlay', $template)
            ->with('status', $msg);
    }

    public function edit(TicketTemplate $template)
    {
        $this->ensureRole();
        abort_unless($template->user_id === Auth::id() || Auth::user()->isAdmin(), 403);
        return view('organizer.templates.edit', compact('template'));
    }

    public function update(Request $request, TicketTemplate $template)
    {
        $this->ensureRole();
        abort_unless($template->user_id === Auth::id() || Auth::user()->isAdmin(), 403);
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'primary_color' => ['nullable','string','max:20'],
            'secondary_color' => ['nullable','string','max:20'],
            'text_color' => ['nullable','string','max:20'],
            'bg_image' => ['nullable','image','max:4096'],
            'pdf_template' => ['nullable','file','mimetypes:application/pdf','max:15360'],
            'overlay_color' => ['nullable','string','max:20'],
            'overlay_opacity' => ['nullable','numeric','min:0','max:1'],
            'logo_enabled' => ['nullable','boolean'],
            'logo_position' => ['nullable','string','max:20'],
            'logo_size' => ['nullable','integer','min:24','max:256'],
            'corner_radius' => ['nullable','integer','min:0','max:64'],
            'card_shadow_enabled' => ['nullable','boolean'],
            'logo_placement' => ['nullable','string','max:40'],
            'image_placement' => ['nullable','string','max:40'],
            'shape' => ['nullable','string','max:40'],
            'font_family' => ['nullable','string','max:60'],
            'qr_position' => ['nullable','string','max:40'],
            'qr_size' => ['nullable','integer','min:32','max:512'],
            'overlay_fields' => ['nullable','array'],
        ]);
        $payload = $data;
        $payload['logo_enabled'] = (bool)($request->boolean('logo_enabled'));
        $payload['card_shadow_enabled'] = (bool)($request->boolean('card_shadow_enabled'));
        foreach (['primary_color','secondary_color','text_color','overlay_color'] as $cf) {
            if (!empty($payload[$cf])) {
                $payload[$cf] = $this->normalizeColor($payload[$cf]);
            }
        }
        if ($request->hasFile('bg_image')) {
            $payload['bg_image_path'] = $request->file('bg_image')->store('ticket_templates/'.Auth::id(), 'public');
        }
        if ($request->hasFile('pdf_template')) {
            // delete previous if exists
            if (!empty($template->pdf_path)) {
                @Storage::disk('public')->delete($template->pdf_path);
            }
            $payload['pdf_path'] = $request->file('pdf_template')->store('ticket_templates/'.Auth::id(), 'public');
            try {
                if (class_exists('setasign\\Fpdi\\Fpdi')) {
                    $fpdi = new \setasign\Fpdi\Fpdi();
                    $payload['pdf_page_count'] = $fpdi->setSourceFile(Storage::disk('public')->path($payload['pdf_path']));
                }
            } catch (\Throwable $e) { /* ignore */ }
        }
        if (!empty($data['overlay_fields'])) {
            $payload['overlay_fields'] = $data['overlay_fields'];
        }
        $template->update($payload);
        return redirect()->route('organizer.templates.index')->with('status', 'Modèle mis à jour.');
    }

    private function normalizeColor(string $input): string
    {
        $val = trim(strtolower($input));
        $map = [
            'noir' => '#000000', 'black' => '#000000',
            'blanc' => '#ffffff', 'white' => '#ffffff',
            'rouge' => '#ff0000', 'red' => '#ff0000',
            'bleu' => '#0000ff', 'blue' => '#0000ff',
            'vert' => '#008000', 'green' => '#008000',
            'jaune' => '#ffff00', 'yellow' => '#ffff00',
            'orange' => '#ffa500',
            'violet' => '#800080', 'purple' => '#800080',
            'gris' => '#808080', 'gray' => '#808080', 'grey' => '#808080',
            'marron' => '#8b4513', 'brown' => '#8b4513',
            'rose' => '#ff69b4', 'pink' => '#ff69b4',
            'cyan' => '#00ffff', 'magenta' => '#ff00ff',
        ];
        if (isset($map[$val])) return $map[$val];
        // accept hex with or without leading #
        if (preg_match('/^#?[0-9a-f]{6}$/i', $input)) {
            return str_starts_with($input, '#') ? $input : ('#'.ltrim($input,'#'));
        }
        return $input; // fallback as provided
    }

    public function preview(TicketTemplate $template)
    {
        $this->ensureRole();
        abort_unless($template->user_id === Auth::id() || Auth::user()->isAdmin(), 403);
        // Render a simple preview with the template styles
        return view('organizer.templates.preview', compact('template'));
    }

    // Simple overlay editor page (form-based). For visual drag-drop, a separate UI can be added later.
    public function editor(TicketTemplate $template)
    {
        $this->ensureRole();
        abort_unless($template->user_id === Auth::id() || Auth::user()->isAdmin(), 403);
        return view('organizer.templates.overlay', compact('template'));
    }

    public function saveOverlay(Request $request, TicketTemplate $template)
    {
        $this->ensureRole();
        abort_unless($template->user_id === Auth::id() || Auth::user()->isAdmin(), 403);
        $data = $request->validate([
            'page' => ['nullable','integer','min:1'],
            'fields' => ['nullable','array'],
            'fields.*.type' => ['required','string'],
            'fields.*.x' => ['required','numeric','min:0'],
            'fields.*.y' => ['required','numeric','min:0'],
            'fields.*.fontSize' => ['nullable','integer','min:6','max:72'],
            'fields.*.color' => ['nullable','string','max:20'],
            'fields.*.value' => ['nullable','string','max:40'],
            'fields.*.text' => ['nullable','string','max:255'],
        ]);
        $overlay = [
            'page' => $data['page'] ?? 1,
            'fields' => $data['fields'] ?? [],
        ];
        $template->overlay_fields = $overlay;
        $template->save();
        return redirect()->route('organizer.templates.overlay', $template)->with('status', 'Zones enregistrées.');
    }

    public function download(TicketTemplate $template)
    {
        $this->ensureRole();
        abort_unless($template->user_id === Auth::id() || Auth::user()->isAdmin(), 403);
        // Try WeasyPrint first
        try {
            $renderer = app(WeasyPrintRenderer::class);
            if ($renderer->isAvailable()) {
                $dir = storage_path('app/public/ticket_templates');
                if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                $pdfPath = $dir.DIRECTORY_SEPARATOR.'template-'.$template->id.'.pdf';
                $renderer->renderViewToPdf('organizer.templates.preview', compact('template'), $pdfPath);
                return response()->download($pdfPath, 'template-'.$template->id.'.pdf');
            }
        } catch (\Throwable $e) {
            // ignore; fallback below
        }

        // Fallback to DomPDF if available
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('organizer.templates.preview', compact('template'))
                ->setPaper('a4', 'portrait');
            return $pdf->download('template-'.$template->id.'.pdf');
        }
        // Final fallback: HTML preview with notice
        return view('organizer.templates.preview', ['template' => $template, 'no_pdf' => true]);
    }
}
