<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Services\Tickets\WeasyPrintRenderer;
use App\Services\Tickets\PdfTemplateRenderer;

class TicketController extends Controller
{
    // Public preview by UUID (no auth required for scan)
    public function showByUuid(string $uuid)
    {
        $ticket = Ticket::with(['type.event.ticketTemplate','booking','type.event.organizer'])->where('uuid', $uuid)->firstOrFail();

        // Prepare QR code HTML and possible overlay logo (if Imagick not available)
        $qrHtml = '';
        $didMerge = false;
        $overlayLogoData = null;
        try {
            $tpl = optional(optional($ticket->type)->event)->ticketTemplate;
            $qrSize = (int)($tpl->qr_size ?? 180);

            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                $format = class_exists('Imagick') ? 'png' : 'svg';
                $qr = \SimpleSoftwareIO\QrCode\Facades\QrCode::format($format)->size($qrSize)->margin(0);

                $organizer = optional(optional($ticket->type)->event)->organizer;
                $orgProfile = $organizer?->organizerProfile;
                $logo = $orgProfile->logo_path ?? ($organizer->profile_picture ?? null);
                $logoFs = null;
                if ($logo && !\Illuminate\Support\Str::startsWith($logo, ['http://','https://','/'])) {
                    $try = storage_path('app/public/'.ltrim($logo,'/'));
                    if (is_file($try)) { $logoFs = $try; }
                }

                if (!empty($logoFs) && class_exists('Imagick')) {
                    $qr = $qr->merge($logoFs, 0.2, true);
                    $didMerge = true;
                }

                $qrHtml = $qr->generate(route('tickets.show.uuid', $ticket->uuid));
                if (!$didMerge && !empty($logoFs)) {
                    $mime = function_exists('mime_content_type') ? mime_content_type($logoFs) : 'image/png';
                    $overlayLogoData = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($logoFs));
                }
            }
        } catch (\Throwable $e) {
            // If anything fails, keep $qrHtml empty; view will show a fallback message
        }

        return view('tickets.show', compact('ticket', 'qrHtml', 'didMerge', 'overlayLogoData'));
    }

    // Download ticket as PDF if dompdf is available; otherwise return HTML
    public function downloadByUuid(string $uuid)
    {
        $ticket = Ticket::with(['type.event.ticketTemplate','booking'])->where('uuid', $uuid)->firstOrFail();

        // If a cached PDF already exists, return it immediately
        $dir = storage_path('app/public/tickets');
        $cachedPath = $dir.DIRECTORY_SEPARATOR.'ticket-'.$ticket->uuid.'.pdf';
        if (is_file($cachedPath)) {
            return response()->download($cachedPath, 'ticket-'.$ticket->uuid.'.pdf');
        }

        // Preferred path A: If a PDF template is uploaded, use FPDI overlay first
        try {
            $tpl = optional(optional($ticket->type)->event)->ticketTemplate;
            if ($tpl && !empty($tpl->pdf_path) && class_exists('setasign\\Fpdi\\Fpdi')) {
                if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                app(PdfTemplateRenderer::class)->render($ticket, $cachedPath);
                return response()->download($cachedPath, 'ticket-'.$ticket->uuid.'.pdf');
            }
        } catch (\Throwable $e) {
            // ignore and try other fallbacks
        }

        // Preferred: WeasyPrint if available
        try {
            $renderer = app(WeasyPrintRenderer::class);
            if ($renderer->isAvailable()) {
                if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                $renderer->renderViewToPdf('tickets.pdf', compact('ticket'), $cachedPath);
                return response()->download($cachedPath, 'ticket-'.$ticket->uuid.'.pdf');
            }
        } catch (\Throwable $e) {
            // ignore and try other fallbacks
        }

        // Fallback: dompdf if installed
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tickets.pdf', compact('ticket'))
                ->setPaper('a4', 'portrait')
                ->setOption('isRemoteEnabled', true);
            return $pdf->download('ticket-'.$ticket->uuid.'.pdf');
        }

        // Fallback: show HTML with a notice
        return response()->view('tickets.show', [
            'ticket' => $ticket,
            'no_pdf' => true,
        ]);
    }
}
