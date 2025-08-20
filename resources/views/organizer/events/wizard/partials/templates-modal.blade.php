<div class="modal fade" id="templatesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content pmw-modal">
      <div class="modal-header">
        <h5 class="modal-title">Suggestions d'affiches</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3 pmw-input">
          <span class="input-group-text">Nom</span>
          <input id="tplQuery" type="text" class="form-control" value="{{ $draft['name'] ?? '' }}" placeholder="Rechercher par nom">
          <button id="tplSearch" class="btn btn-outline-primary" type="button">Rechercher</button>
        </div>
        <div id="tplResults" class="pmw-grid">
          <div class="text-muted">Chargement…</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function(){
  async function fetchSuggestions(q){
    const url = new URL(@json(route('organizer.events.wizard.suggestions')));
    if(q) url.searchParams.set('name', q);
    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    return await res.json();
  }
  function renderResults(list){
    const wrap = document.getElementById('tplResults');
    wrap.innerHTML = '';
    if(!list || list.length === 0){
      wrap.innerHTML = '<div class="text-muted">Aucun résultat.</div>';
      return;
    }
    list.forEach(item => {
      const el = document.createElement('div');
      if(item.kind === 'external'){
        const thumb = item.thumb_url || item.image_url;
        el.innerHTML = `
          <div class="pmw-card">
            <span class="badge-source">${item.provider ? item.provider : 'Web'}</span>
            <div class="pmw-thumb">${thumb ? `<img src="${thumb}" alt="">` : ''}
              <div class="pmw-actions">
                <form method="post" action="${@json(route('organizer.events.wizard.choose_external'))}">
                  <input type="hidden" name="_token" value="${@json(csrf_token())}">
                  <input type="hidden" name="image_url" value="${item.image_url ?? ''}">
                  <input type="hidden" name="name" value="${item.name ?? ''}">
                  <input type="hidden" name="provider" value="${item.provider ?? ''}">
                  <button class="btn btn-sm btn-primary w-100" type="submit">Utiliser</button>
                </form>
              </div>
            </div>
            <div class="pmw-body">
              <div class="pmw-title">${item.name ?? 'Image externe'}</div>
              <div class="pmw-sub">${item.provider ? ('Source: ' + item.provider) : ''}</div>
            </div>
          </div>`;
      } else {
        const src = item.bg_image_path ? `${@json(asset('storage/'))}/${item.bg_image_path}` : '';
        el.innerHTML = `
          <div class="pmw-card">
            <span class="badge-source">Local</span>
            <div class="pmw-thumb">${src ? `<img src="${src}" alt="">` : ''}
              <div class="pmw-actions">
                <form method="post" action="${@json(route('organizer.events.wizard.choose', 'TEMPLATE_ID')).replace('TEMPLATE_ID', item.id)}">
                  <input type="hidden" name="_token" value="${@json(csrf_token())}">
                  <button class="btn btn-sm btn-success w-100" type="submit">Choisir</button>
                </form>
              </div>
            </div>
            <div class="pmw-body">
              <div class="pmw-title">${item.name}</div>
              <div class="pmw-sub">${item.primary_color ?? ''} ${item.secondary_color ?? ''}</div>
            </div>
          </div>`;
      }
      wrap.appendChild(el);
    });
  }
  document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('tplSearch');
    const inp = document.getElementById('tplQuery');
    const wrap = document.getElementById('tplResults');
    async function loadNow(){
      try{
        if(wrap){ wrap.innerHTML = '<div class="text-muted">Chargement…</div>'; }
        const list = await fetchSuggestions(inp ? inp.value.trim() : '');
        renderResults(list);
      } catch(e){
        if(wrap){ wrap.innerHTML = '<div class="text-danger">Erreur lors du chargement des suggestions.</div>'; }
      }
    }
    if(btn){
      btn.addEventListener('click', loadNow);
    }
    // Auto-load on page load using current event name
    loadNow();
    // Also auto-load when modal is shown (in case name changed between steps)
    const modalEl = document.getElementById('templatesModal');
    if (modalEl) {
      modalEl.addEventListener('shown.bs.modal', loadNow);
    }
  });
})();
</script>
@endpush
