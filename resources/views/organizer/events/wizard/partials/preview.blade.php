<div class="pmw-preview">
  <div class="mb-2">
    <strong>Aperçu</strong>
  </div>
  <div>
    @php($d = $draft ?? [])
    @php($tpl = !empty($d['ticket_template_id']) ? \App\Models\TicketTemplate::find($d['ticket_template_id']) : null)

    @if($tpl)
      @php(
        $overlay = $tpl->overlay_fields ?? []
      )
      <div class="mb-3">
        <div class="small text-muted">Modèle sélectionné</div>
        <div class="fw-semibold">{{ $tpl->name }}</div>
      </div>
      <div class="frame position-relative mx-auto" style="width: 100%; max-width: 360px; aspect-ratio: 3/4;">
        @if($tpl->bg_image_path)
          <img src="{{ asset('storage/'.$tpl->bg_image_path) }}" alt="bg">
        @endif
        @php(
          $fields = (array)($overlay['fields'] ?? [])
        )
        @php($mmToPx = 3.78) {{-- approx for preview only --}}
        @foreach($fields as $f)
          @php(
            $type = $f['type'] ?? 'text'
          )
          @php(
            $valKey = $f['value'] ?? ''
          )
          @php(
            $textVal = match($valKey) {
              'event_name' => $d['name'] ?? '',
              'start_date' => $d['start_date'] ?? '',
              'end_date' => $d['end_date'] ?? '',
              'location' => $d['location'] ?? '',
              default => ($f['text'] ?? ''),
            }
          )
          @php($left = (float)($f['x'] ?? 0) * $mmToPx)
          @php($top = (float)($f['y'] ?? 0) * $mmToPx)
          @php($fs = (int)($f['fontSize'] ?? 14))
          @php($color = $f['color'] ?? '#000')
          @if($type === 'text')
            <div class="pmw-overlay-text" style="position:absolute; left: {{ $left }}px; top: {{ $top }}px; color: {{ $color }}; font-size: {{ $fs }}px; font-weight: 700;">
              {{ $textVal }}
            </div>
          @elseif($type === 'qr')
            <div style="position:absolute; left: {{ $left }}px; top: {{ $top }}px; width: 96px; height: 96px; background:#fff; border:2px solid #222; display:flex; align-items:center; justify-content:center; color:#222; font-size:12px;">
              QR
            </div>
          @endif
        @endforeach
      </div>
    @else
      <div class="mb-2">
        <div class="small text-muted">Nom</div>
        <div class="fw-semibold">{{ $d['name'] ?? '—' }}</div>
      </div>
      <div class="row g-2">
        <div class="col-6">
          <div class="small text-muted">Début</div>
          <div>{{ $d['start_date'] ?? '—' }}</div>
        </div>
        <div class="col-6">
          <div class="small text-muted">Fin</div>
          <div>{{ $d['end_date'] ?? '—' }}</div>
        </div>
      </div>
      <div class="mt-2">
        <div class="small text-muted">Lieu</div>
        <div>{{ $d['location'] ?? '—' }}</div>
      </div>
      <hr>
      <div class="small text-muted">Affiche</div>
      <div>—</div>
    @endif
  </div>
</div>
