<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\Tickets\WeasyPrintRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateTicketPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $ticketUuid
    ) {}

    public function handle(WeasyPrintRenderer $renderer): void
    {
        try {
            if (!$renderer->isAvailable()) {
                return; // silently skip if not available; controller will fallback when needed
            }
            $ticket = Ticket::with(['type.event.ticketTemplate','booking'])->where('uuid', $this->ticketUuid)->first();
            if (!$ticket) { return; }

            $dir = storage_path('app/public/tickets');
            if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
            $pdfPath = $dir.DIRECTORY_SEPARATOR.'ticket-'.$ticket->uuid.'.pdf';

            // If already generated, skip
            if (is_file($pdfPath)) { return; }

            $renderer->renderViewToPdf('tickets.pdf', compact('ticket'), $pdfPath);
        } catch (\Throwable $e) {
            Log::warning('GenerateTicketPdf failed for '.$this->ticketUuid.': '.$e->getMessage());
        }
    }
}
