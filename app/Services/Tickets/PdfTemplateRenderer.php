<?php

namespace App\Services\Tickets;

use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfTemplateRenderer
{
    /**
     * Render final ticket PDF by overlaying fields on an uploaded PDF template.
     * Requires setasign/fpdi. If unavailable, throws and caller should fallback.
     */
    public function render(Ticket $ticket, string $outputPath): void
    {
        if (!class_exists('setasign\\Fpdi\\Fpdi')) {
            throw new \RuntimeException('FPDI not installed');
        }

        $tpl = optional(optional($ticket->type)->event)->ticketTemplate;
        if (!$tpl || empty($tpl->pdf_path)) {
            throw new \InvalidArgumentException('No PDF template on ticket template');
        }

        $diskPath = 'public/'.ltrim($tpl->pdf_path, '/');
        if (!Storage::exists($diskPath)) {
            throw new \RuntimeException('PDF template file not found: '.$tpl->pdf_path);
        }
        $local = Storage::path($diskPath);

        $buyerName = $ticket->booking->buyer_name ?? '';
        $buyerEmail = $ticket->booking->buyer_email ?? '';
        $eventName = optional($ticket->type->event)->name ?? '';
        $qrUrl = route('tickets.show.uuid', $ticket->uuid);

        $overlay = $tpl->overlay_fields ?? [];
        $pageNum = (int)($overlay['page'] ?? 1);
        $fields = $overlay['fields'] ?? [];

        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($local);
        $pageNum = max(1, min($pageCount, $pageNum));

        $tmpFiles = [];
        // Import each page and overlay only on target page
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tplId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);
            if ($i === $pageNum) {
                // Draw text fields
                foreach ($fields as $f) {
                    $type = $f['type'] ?? 'text';
                    $x = (float)($f['x'] ?? 10);
                    $y = (float)($f['y'] ?? 10);
                    $fontSize = (int)($f['fontSize'] ?? 12);
                    $color = $f['color'] ?? '#000000';
                    $valueKey = $f['value'] ?? '';
                    $value = '';
                    switch ($valueKey) {
                        case 'buyer_name': $value = $buyerName; break;
                        case 'buyer_email': $value = $buyerEmail; break;
                        case 'event_name': $value = $eventName; break;
                        case 'ticket_uuid': $value = $ticket->uuid; break;
                        default: $value = (string)($f['text'] ?? '');
                    }
                    if ($type === 'text' && $value !== '') {
                        [$r,$g,$b] = $this->hexToRgb($color);
                        $pdf->SetTextColor($r, $g, $b);
                        $pdf->SetFont('Helvetica','', $fontSize);
                        $pdf->SetXY($x, $y);
                        $pdf->Write(5, $value);
                    }
                    if ($type === 'qr') {
                        // Try to render a visual QR as PNG and place it on the PDF
                        $qrContent = $qrUrl;
                        $qrSizeMm = isset($f['size']) ? (float)$f['size'] : (isset($f['fontSize']) ? max(14.0, (float)$f['fontSize'] * 1.4) : 30.0);
                        $tmpDir = storage_path('app/tmp');
                        if (!is_dir($tmpDir)) { @mkdir($tmpDir, 0775, true); }
                        $tmpPng = $tmpDir.DIRECTORY_SEPARATOR.'qr-'.$ticket->uuid.'-'.$i.'-'.substr(sha1(json_encode($f)),0,8).'.png';
                        $placed = false;
                        try {
                            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                                // Generate a crisp PNG (pixels). Width in mm is handled by FPDF using $qrSizeMm.
                                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->margin(0)->size(600)->generate($qrContent, $tmpPng);
                                if (is_file($tmpPng)) {
                                    $pdf->Image($tmpPng, $x, $y, $qrSizeMm, $qrSizeMm, 'PNG');
                                    $tmpFiles[] = $tmpPng;
                                    $placed = true;
                                }
                            }
                        } catch (\Throwable $e) {
                            Log::warning('QR PNG generation failed: '.$e->getMessage());
                        }
                        if (!$placed) {
                            // Fallback to text if PNG generation failed
                            $label = $f['label'] ?? 'Scan:';
                            $pdf->SetTextColor(0,0,0);
                            $pdf->SetFont('Helvetica','', max(8, $fontSize-2));
                            $pdf->SetXY($x, $y);
                            $pdf->Write(5, $label.' '.$qrContent);
                        }
                    }
                }
            }
        }

        $dir = dirname($outputPath);
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
        $pdf->Output($outputPath, 'F');

        // Cleanup temporary files
        foreach ($tmpFiles as $t) {
            @unlink($t);
        }
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $int = hexdec($hex);
        return [($int >> 16) & 255, ($int >> 8) & 255, $int & 255];
    }
}
