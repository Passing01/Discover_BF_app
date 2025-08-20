<?php

namespace App\Services\Tickets;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class WeasyPrintRenderer
{
    public function __construct(
        protected ViewFactory $views
    ) {}

    public function isAvailable(): bool
    {
        try {
            $process = Process::fromShellCommandline('weasyprint --version');
            $process->setTimeout(5);
            $process->run();
            return $process->isSuccessful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Render a Blade view to a PDF using WeasyPrint CLI.
     * Returns the absolute path to the generated PDF.
     */
    public function renderViewToPdf(string $view, array $data = [], ?string $outputPath = null): string
    {
        // 1) Render Blade -> HTML string
        $html = $this->views->make($view, $data)->render();

        // 2) Write to temporary HTML file
        $tmpDir = storage_path('app/tmp');
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);
        $inputHtml = tempnam($tmpDir, 'weasy-').'.html';
        file_put_contents($inputHtml, $html);

        // 3) Decide output file
        if (!$outputPath) {
            $outputPath = tempnam($tmpDir, 'weasy-').'.pdf';
        }

        // 4) Run WeasyPrint CLI
        // Use --presentational-hints to better respect inline styles
        $cmd = [
            'weasyprint',
            $inputHtml,
            $outputPath,
            '--presentational-hints',
        ];
        $process = new Process($cmd);
        $process->setTimeout(60);
        $process->run();

        // cleanup input html regardless of success
        @unlink($inputHtml);

        if (!$process->isSuccessful()) {
            Log::warning('WeasyPrint failed: '.$process->getErrorOutput());
            throw new \RuntimeException('WeasyPrint failed to generate PDF.');
        }

        return $outputPath;
    }
}
