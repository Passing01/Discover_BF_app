<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ticket {{ $ticket->uuid }}</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    .ticket { border: 1px solid #ddd; padding: 16px; }
    .row { display: flex; flex-wrap: wrap; margin: 0 -8px; }
    .col { padding: 0 8px; }
    .col-6 { width: 50%; }
    .mb-2 { margin-bottom: .5rem; }
    .mb-3 { margin-bottom: 1rem; }
    .fw-bold { font-weight: 700; }
    .muted { color: #6c757d; }
    .h4 { font-size: 1.25rem; margin: 0; }
    .small { font-size: .875rem; }
    .badge { display: inline-block; padding: .25rem .5rem; background: #198754; color: #fff; border-radius: .25rem; font-size: .75rem; text-transform: uppercase; }
    ul { margin: 0; padding-left: 1rem; }
  </style>
</head>
<body>
  @php
    $tpl = optional(optional($ticket->type)->event)->ticketTemplate;
    $primary = $tpl->primary_color ?? '#198754';
    $secondary = $tpl->secondary_color ?? '#6c757d';
    $text = $tpl->text_color ?? '#222222';
    $font = $tpl->font_family ?? 'DejaVu Sans, Arial, Helvetica, sans-serif';
    $qrSize = (int)($tpl->qr_size ?? 180);
    $qrPos = $tpl->qr_position ?? 'left';
    $corner = (int)($tpl->corner_radius ?? 12);
    $logoEnabled = (bool)($tpl->logo_enabled ?? false);
    $logoPos = $tpl->logo_position ?? 'top-right';
    $logoSize = (int)($tpl->logo_size ?? 56);
    $ovColor = $tpl->overlay_color ?? null;
    $ovOpacity = is_null($tpl->overlay_opacity) ? 0.0 : (float)$tpl->overlay_opacity;
    $bg = $tpl->bg_image_path ?? null;
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
    }
  @endphp
  <div class="ticket" style="border-color: {{ $primary }}; border-radius: {{ $corner }}px; overflow: hidden; position: relative; @if($bgDataUrl || $bgUrl) background-image: url('{{ $bgDataUrl ?? $bgUrl }}'); background-size: cover; background-position: center; @endif font-family: {{ $font }}; color: {{ $text }};">
    @if($ovColor && $ovOpacity > 0)
      @php
        $hex = ltrim($ovColor, '#');
        if (strlen($hex) === 6) {
          $r = hexdec(substr($hex,0,2)); $g = hexdec(substr($hex,2,2)); $b = hexdec(substr($hex,4,2));
          $rgba = 'rgba('.$r.','.$g.','.$b.','.$ovOpacity.')';
        } else { $rgba = 'rgba(0,0,0,'.$ovOpacity.')'; }
      @endphp
      <div style="position:absolute; left:0; top:0; right:0; bottom:0; background: {{ $rgba }};"></div>
    @endif
    @if($logoEnabled)
      @php
        $eventOrg = optional(optional($ticket->type)->event)->organizer;
        $logoPath = $eventOrg?->profile_picture;
        $logoUrl = null;
        if ($logoPath) {
          if (\Illuminate\Support\Str::startsWith($logoPath, ['http://','https://','/'])) {
            $logoUrl = $logoPath;
          } else {
            $fs = storage_path('app/public/'.ltrim($logoPath,'/'));
            if (is_file($fs)) {
              $mime = function_exists('mime_content_type') ? mime_content_type($fs) : 'image/png';
              $logoUrl = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($fs));
            } else {
              $logoUrl = asset('storage/'.$logoPath);
            }
          }
        }
        $posStyles = ['top-left' => 'top:8px; left:8px;', 'top-right' => 'top:8px; right:8px;', 'bottom-left' => 'bottom:8px; left:8px;', 'bottom-right' => 'bottom:8px; right:8px;'];
      @endphp
      @if($logoUrl)
        <img src="{{ $logoUrl }}" alt="logo" style="position:absolute; {{ $posStyles[$logoPos] ?? 'top:8px; right:8px;' }} width: {{ $logoSize }}px; height: {{ $logoSize }}px; object-fit: contain; z-index: 2; border-radius: 6px; background: transparent;" />
      @endif
    @endif
    <div style="position: relative; z-index: 3;">
     <div class="mb-2">
       <span class="badge" style="background: {{ $primary }};">{{ $ticket->status }}</span>
     </div>
     <h1 class="h4" style="color: {{ $text }};">{{ $ticket->type->event->name ?? 'Évènement' }}</h1>
     <div class="muted small mb-3" style="color: {{ $text }};">{{ $ticket->type->event->location ?? '' }}</div>

    <div class="row mb-3">
      @php($qrLeft = $qrPos === 'left' || $qrPos === 'top')
      <div class="col col-6" style="order: {{ $qrLeft ? 1 : 2 }};">
        <div style="border: 1px solid {{ $secondary }}; padding: 8px; text-align: center; background: transparent; position: relative;">
          @php
            $event = optional($ticket->type)->event;
            $organizer = optional($event)->organizer;
            $logo = $organizer->profile_picture ?? null;
            $logoFs = null;
            if ($logo && !\Illuminate\Support\Str::startsWith($logo, ['http://','https://','/'])) {
              $try = storage_path('app/public/'.ltrim($logo,'/'));
              if (is_file($try)) { $logoFs = $try; }
            }
          @endphp
          @if(class_exists('SimpleSoftwareIO\\QrCode\\Facades\\QrCode'))
            @php($qrFormat = class_exists('Imagick') ? 'png' : 'svg')
            @php($qrGen = SimpleSoftwareIO\\QrCode\\Facades\\QrCode::format($qrFormat)->size($qrSize)->margin(0))
            @php($didMerge = false)
            @if(!empty($logoFs) && class_exists('Imagick'))
              @php($qrGen = $qrGen->merge($logoFs, 0.2, true))
              @php($didMerge = true)
            @endif
            {!! $qrGen->generate(route('tickets.show.uuid', $ticket->uuid)) !!}
            @if(!empty($logoFs) && !$didMerge)
              @php
                $mime = function_exists('mime_content_type') ? mime_content_type($logoFs) : 'image/png';
                $logoData = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($logoFs));
              @endphp
              <img src="{{ $logoData }}" alt="logo" style="position:absolute; width: {{ max(24, (int)($qrSize*0.28)) }}px; height: {{ max(24, (int)($qrSize*0.28)) }}px; object-fit: contain; left: 50%; top: 50%; transform: translate(-50%, -50%); border-radius: 6px; background: transparent;" />
            @endif
          @else
            <div class="muted small">QR Code indisponible (package non installé)</div>
          @endif
          <div class="muted small">UID: {{ $ticket->uuid }}</div>
        </div>
      </div>
      <div class="col col-6" style="order: {{ $qrLeft ? 2 : 1 }};">
        <ul>
          <li><span class="fw-bold" style="color: {{ $primary }};">Type:</span> {{ $ticket->type->name }}</li>
          <li><span class="fw-bold" style="color: {{ $primary }};">Tarif:</span> {{ number_format($ticket->type->price, 0, ',', ' ') }} CFA</li>
          @if($ticket->issued_at)
            <li><span class="fw-bold" style="color: {{ $primary }};">Émis le:</span> {{ $ticket->issued_at->format('d/m/Y H:i') }}</li>
          @endif
          @if($ticket->booking)
            <li><span class="fw-bold" style="color: {{ $primary }};">Acheteur:</span> {{ $ticket->booking->buyer_name }}</li>
            <li><span class="fw-bold" style="color: {{ $primary }};">Email:</span> {{ $ticket->booking->buyer_email }}</li>
            <li><span class="fw-bold" style="color: {{ $primary }};">Total de la commande:</span> {{ number_format($ticket->booking->total_amount, 0, ',', ' ') }} CFA</li>
          @endif
        </ul>
      </div>
    </div>
  </div>
</body>
</html>
