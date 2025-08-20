@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  @isset($no_pdf)
    <div class="alert alert-warning">Téléchargement PDF indisponible (package PDF non installé). Vous pouvez tout de même imprimer cette page.</div>
  @endisset
  @php
    $tpl = optional(optional($ticket->type)->event)->ticketTemplate;
    $primary = $tpl?->primary_color ?? '#198754';
    $secondary = $tpl?->secondary_color ?? '#6c757d';
    $text = $tpl?->text_color ?? '#222222';
    $font = $tpl?->font_family ?? 'inherit';
    $qrSize = (int)($tpl?->qr_size ?? 180);
    $qrPos = $tpl?->qr_position ?? 'left';
    $corner = (int)($tpl?->corner_radius ?? 12);
    $shadow = ($tpl?->card_shadow_enabled ?? true) ? 'shadow' : '';
    $logoEnabled = (bool)($tpl?->logo_enabled ?? false);
    $logoPos = $tpl?->logo_position ?? 'top-right';
    $logoSize = (int)($tpl?->logo_size ?? 56);
    $ovColor = $tpl?->overlay_color ?? null;
    $ovOpacity = is_null($tpl?->overlay_opacity) ? 0.0 : (float)($tpl?->overlay_opacity ?? 0.0);
    $bg = $tpl?->bg_image_path ?? null;
    $bgUrl = $bg ? (\Illuminate\Support\Str::startsWith($bg, ['http://','https://','/']) ? $bg : asset('storage/'.$bg)) : null;
  @endphp
  @php
    $bgDataUrl = null;
    if ($bg && !\Illuminate\Support\Str::startsWith($bg, ['http://','https://','/'])) {
        $full = storage_path('app/public/'.ltrim($bg, '/'));
        if (is_file($full)) {
            $mime = function_exists('mime_content_type') ? mime_content_type($full) : 'image/png';
            $data = base64_encode(file_get_contents($full));
            $bgDataUrl = 'data:'.$mime.';base64,'.$data;
        }
    } elseif ($bg && \Illuminate\Support\Str::startsWith($bg, ['http://','https://'])) {
        // Try to inline remote image to avoid CORS issues for html2canvas
        try {
            $context = stream_context_create(['http' => ['timeout' => 2], 'https' => ['timeout' => 2]]);
            $content = @file_get_contents($bg, false, $context);
            if ($content !== false) {
                $ext = pathinfo(parse_url($bg, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);
                $mime = $ext ? 'image/'.strtolower($ext) : 'image/png';
                $bgDataUrl = 'data:'.$mime.';base64,'.base64_encode($content);
            }
        } catch (\Throwable $e) {
            // ignore, will fallback to bgUrl
        }
    }
  @endphp
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div id="ticket-card" class="card {{ $shadow }}" style="border-color: {{ $primary }}; border-radius: {{ $corner }}px; overflow: hidden;">
        <div class="card-body position-relative" style="
          --primary: {{ $primary }}; --secondary: {{ $secondary }};
          color: {{ $text }};
          font-family: {{ $font }};
          @if($bgDataUrl || $bgUrl) background-image: url('{{ $bgDataUrl ?? $bgUrl }}'); background-size: cover; background-position: center; @endif
        ">
          @if($ovColor && $ovOpacity > 0)
            @php
              $hex = ltrim($ovColor, '#');
              if (strlen($hex) === 6) {
                $r = hexdec(substr($hex,0,2)); $g = hexdec(substr($hex,2,2)); $b = hexdec(substr($hex,4,2));
                $rgba = 'rgba('.$r.','.$g.','.$b.','.$ovOpacity.')';
              } else { $rgba = 'rgba(0,0,0,'.$ovOpacity.')'; }
            @endphp
            <div aria-hidden="true" style="position:absolute; inset:0; background: {{ $rgba }};"></div>
          @endif
          @if($logoEnabled)
            @php
              $eventOrg = optional(optional($ticket->type)->event)->organizer;
              $logoPath = $eventOrg?->profile_picture;
              $logoUrl = $logoPath ? (\Illuminate\Support\Str::startsWith($logoPath, ['http://','https://','/']) ? $logoPath : asset('storage/'.$logoPath)) : null;
              $posStyles = ['top-left' => 'top:12px; left:12px;', 'top-right' => 'top:12px; right:12px;', 'bottom-left' => 'bottom:12px; left:12px;', 'bottom-right' => 'bottom:12px; right:12px;'];
            @endphp
            @if($logoUrl)
              <img src="{{ $logoUrl }}" alt="logo" style="position:absolute; {{ $posStyles[$logoPos] ?? 'top:12px; right:12px;' }} width: {{ $logoSize }}px; height: {{ $logoSize }}px; object-fit: contain; z-index: 2; border-radius: 8px; background: transparent;" />
            @endif
          @endif
          <div style="position: relative; z-index: 3;">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0" style="color: {{ $primary }};">Ticket</h5>
              <span class="badge text-uppercase" style="background: {{ $primary }};">{{ $ticket->status }}</span>
            </div>
            <h3 class="h4">{{ $ticket->type?->event?->name ?? 'Évènement' }}</h3>
            <div class="text-muted mb-2">{{ $ticket->type?->event?->location ?? '' }}</div>
            <div class="mb-3">
            @php
              $event = optional($ticket->type)->event;
              $start = optional($event)->start_date;
              $end = optional($event)->end_date;
            @endphp
            <div class="small text-muted">
              @if(!empty($start)) Du {{ \Carbon\Carbon::parse($start)->translatedFormat('d M Y') }} @endif
              @if(!empty($end)) au {{ \Carbon\Carbon::parse($end)->translatedFormat('d M Y') }} @endif
            </div>
          </div>

          <div class="row g-3 align-items-center">
            <div class="col-md-6 order-{{ (in_array($qrPos, ['left','top'])) ? '1' : '2' }}">
              <div class="p-3 border rounded text-center" style="border-color: {{ $secondary }}; background: transparent; position: relative;">
                @if(!empty($qrHtml))
                  {!! $qrHtml !!}
                  @if(!empty($overlayLogoData) && empty($didMerge))
                    <img src="{{ $overlayLogoData }}" alt="logo" style="position:absolute; width: {{ max(24, (int)($qrSize*0.28)) }}px; height: {{ max(24, (int)($qrSize*0.28)) }}px; object-fit: contain; left: 50%; top: 50%; transform: translate(-50%, -50%); border-radius: 6px; background: transparent; pointer-events: none;" />
                  @endif
                @else
                  <div class="text-muted">QR Code indisponible (package non installé)</div>
                @endif
              </div>
              <div class="small text-muted mt-2">UID: {{ $ticket->uuid }}</div>
              <div class="mt-3 d-flex gap-2">
                <a class="btn btn-sm" style="border-color: {{ $primary }}; color: {{ $primary }};" href="{{ route('tickets.download.uuid', $ticket->uuid) }}">Télécharger PDF</a>
                <button id="download-image" class="btn btn-outline-success btn-sm" type="button">Télécharger image</button>
                <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">Imprimer</button>
              </div>
            </div>
            <div class="col-md-6 order-{{ (in_array($qrPos, ['left','top'])) ? '2' : '1' }}">
              <ul class="list-unstyled mb-0">
                <li><strong style="color: {{ $primary }};">Type:</strong> {{ $ticket->type?->name ?? '—' }}</li>
                <li><strong style="color: {{ $primary }};">Prix du billet:</strong> {{ isset($ticket->type?->price) ? number_format($ticket->type->price, 0, ',', ' ') : '—' }} CFA</li>
                @if($ticket->issued_at)
                  <li><strong style="color: {{ $primary }};">Émis le:</strong> {{ $ticket->issued_at->format('d/m/Y H:i') }}</li>
                @endif
                @if($ticket->booking)
                  <li><strong style="color: {{ $primary }};">Acheteur:</strong> {{ $ticket->booking->buyer_name }}</li>
                  <li><strong style="color: {{ $primary }};">Email:</strong> {{ $ticket->booking->buyer_email }}</li>
                  <li><strong style="color: {{ $primary }};">Total de la commande:</strong> {{ number_format($ticket->booking->total_amount, 0, ',', ' ') }} CFA</li>
                @endif
              </ul>
            </div>
          </div>
          </div>
        </div>
      </div>
      <div class="mt-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Retour</a>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script id="dom-to-image-script" data-loaded="0" src="" defer></script>
<script>
  (function(){
    const btn = document.getElementById('download-image');
    if (!btn) return;
    btn.addEventListener('click', async function(){
      const card = document.getElementById('ticket-card');
      if(!card) return;
      try {
        await ensureHtml2CanvasLoaded();
        const canvas = await window.html2canvas(card, {
          scale: 2,
          backgroundColor: '#ffffff',
          useCORS: true,
          allowTaint: false,
          removeContainer: true,
          logging: false,
          scrollX: 0,
          scrollY: 0,
          windowWidth: document.documentElement.offsetWidth,
          windowHeight: document.documentElement.offsetHeight,
        });
        const link = document.createElement('a');
        link.download = 'ticket-{{ $ticket->uuid }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
      } catch(e) {
        console.warn('html2canvas failed, trying dom-to-image-more...', e);
        try {
          await ensureDomToImageLoaded();
          const dataUrl = await window.domtoimage.toPng(card, {
            bgcolor: '#ffffff',
            cacheBust: true,
            imagePlaceholder: undefined,
            filter: function(node) { return true; }
          });
          const link = document.createElement('a');
          link.download = 'ticket-{{ $ticket->uuid }}.png';
          link.href = dataUrl;
          link.click();
        } catch(e2) {
          alert('Impossible de générer l\'image. Vérifiez les images externes/CORS.');
          console.error(e2);
        }
      }
    });

    async function ensureDomToImageLoaded(){
      if (window.domtoimage) return;
      const scriptEl = document.getElementById('dom-to-image-script');
      if (!scriptEl) throw new Error('script tag missing');
      if (scriptEl.getAttribute('data-loaded') === '1') throw new Error('dom-to-image failed to load previously');
      return new Promise((resolve, reject) => {
        scriptEl.onload = () => { scriptEl.setAttribute('data-loaded','1'); resolve(); };
        scriptEl.onerror = () => reject(new Error('Failed to load dom-to-image-more'));
        scriptEl.src = 'https://cdn.jsdelivr.net/npm/dom-to-image-more@3.3.7/dist/dom-to-image-more.min.js';
      });
    }

    async function ensureHtml2CanvasLoaded(){
      if (window.html2canvas) return;
      // Create a script tag dynamically to avoid SRI/CORS issues
      await new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
        s.async = true;
        s.onload = () => resolve();
        s.onerror = () => reject(new Error('Failed to load html2canvas'));
        document.head.appendChild(s);
      });
    }
  })();
  </script>
@endpush
@endsection
