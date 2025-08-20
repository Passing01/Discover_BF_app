@extends('layouts.tourist')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0 text-orange">Importer un modèle PDF</h1>
    <a href="{{ route('organizer.templates.index') }}" class="btn btn-cream">Retour</a>
  </div>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="panel-cream rounded-20">
    <div class="p-3 p-md-4">
      <form method="POST" action="{{ route('organizer.templates.pdf.store') }}" enctype="multipart/form-data" id="pdfUploadForm">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Nom du modèle</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Ex: Ticket VIP PDF" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="mt-3">
          <label class="form-label">Fichier PDF (optionnel)</label>
          <div id="dropzone" class="border border-2 rounded-20 p-4 text-center" style="border-style: dashed; cursor: pointer; background:#faf7f2;">
            <div class="mb-2 fw-semibold">Glissez-déposez votre PDF ici, ou cliquez pour sélectionner</div>
            <div class="small text-muted">Taille max: 15 Mo. Format accepté: PDF. Vous pouvez aussi importer uniquement une image de fond ci-dessous.</div>
            <input type="file" name="pdf_template" id="pdfInput" accept="application/pdf" class="d-none">
          </div>
          <div id="fileInfo" class="form-text mt-2 d-none"></div>
          <div id="pdfPreview" class="mt-3 d-none">
            <iframe id="pdfFrame" style="width:100%;height:500px;border:1px solid #e5e5e5;border-radius:12px;"></iframe>
          </div>
        </div>

        <div class="mt-4">
          <label class="form-label">Image de fond (optionnel)</label>
          <div id="imgDropzone" class="border border-2 rounded-20 p-4 text-center" style="border-style: dashed; cursor: pointer; background:#faf7f2;">
            <div class="mb-2 fw-semibold">Glissez-déposez une image (JPG/PNG), ou cliquez pour sélectionner</div>
            <div class="small text-muted">Taille max: 4 Mo. Formats acceptés: JPG, PNG, WebP.</div>
            <input type="file" name="bg_image" id="imgInput" accept="image/*" class="d-none">
          </div>
          <div id="imgInfo" class="form-text mt-2 d-none"></div>
          <div id="imgPreview" class="mt-3 d-none">
            <img id="imgThumb" src="#" alt="aperçu" style="max-width:100%;height:auto;border:1px solid #e5e5e5;border-radius:12px;"/>
          </div>
        </div>

        <div class="mt-4 d-flex align-items-center">
          <button class="btn btn-orange" type="submit">Importer et définir les placements</button>
          <span class="ms-3 text-muted small">Après l'import, vous serez redirigé vers l'éditeur pour placer les champs (nom, email, QR, etc.).</span>
        </div>
      </form>
    </div>
  </div>

  <div class="mt-3">
    <details>
      <summary class="fw-semibold">Conseils</summary>
      <ul class="mt-2 mb-0">
        <li>Utilisez un PDF A4 portrait pour de meilleurs résultats.</li>
        <li>Assurez-vous que la zone de contenu est suffisamment contrastée.</li>
        <li>Vous pourrez positionner des champs (texte et QR) au millimètre près sur le PDF.</li>
        <li>Si vous fournissez une image de fond, elle peut servir pour les aperçus HTML ou pour l'export alternatif.</li>
      </ul>
    </details>
  </div>
</div>

@push('scripts')
<script>
  (function(){
    const dropzone = document.getElementById('dropzone');
    const input = document.getElementById('pdfInput');
    const info = document.getElementById('fileInfo');
    const prevWrap = document.getElementById('pdfPreview');
    const frame = document.getElementById('pdfFrame');

    const imgDrop = document.getElementById('imgDropzone');
    const imgInput = document.getElementById('imgInput');
    const imgInfo = document.getElementById('imgInfo');
    const imgPrev = document.getElementById('imgPreview');
    const imgThumb = document.getElementById('imgThumb');

    function showFile(file){
      info.classList.remove('d-none');
      info.textContent = `${file.name} (${(file.size/1024/1024).toFixed(2)} Mo)`;
      // Preview PDF using blob URL
      const url = URL.createObjectURL(file);
      prevWrap.classList.remove('d-none');
      frame.src = url;
    }

    function showImg(file){
      imgInfo.classList.remove('d-none');
      imgInfo.textContent = `${file.name} (${(file.size/1024/1024).toFixed(2)} Mo)`;
      const url = URL.createObjectURL(file);
      imgPrev.classList.remove('d-none');
      imgThumb.src = url;
    }

    dropzone.addEventListener('click', function(){ input.click(); });
    dropzone.addEventListener('dragover', function(e){ e.preventDefault(); dropzone.classList.add('bg-white'); });
    dropzone.addEventListener('dragleave', function(){ dropzone.classList.remove('bg-white'); });
    dropzone.addEventListener('drop', function(e){
      e.preventDefault();
      dropzone.classList.remove('bg-white');
      if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        const f = e.dataTransfer.files[0];
        if (f.type !== 'application/pdf') { alert('Veuillez déposer un fichier PDF.'); return; }
        input.files = e.dataTransfer.files;
        showFile(f);
      }
    });
    input.addEventListener('change', function(){ if (this.files[0]) showFile(this.files[0]); });

    imgDrop.addEventListener('click', function(){ imgInput.click(); });
    imgDrop.addEventListener('dragover', function(e){ e.preventDefault(); imgDrop.classList.add('bg-white'); });
    imgDrop.addEventListener('dragleave', function(){ imgDrop.classList.remove('bg-white'); });
    imgDrop.addEventListener('drop', function(e){
      e.preventDefault();
      imgDrop.classList.remove('bg-white');
      if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        const f = e.dataTransfer.files[0];
        if (!f.type.startsWith('image/')) { alert("Veuillez déposer une image (JPG/PNG/WebP)."); return; }
        imgInput.files = e.dataTransfer.files;
        showImg(f);
      }
    });
    imgInput.addEventListener('change', function(){ if (this.files[0]) showImg(this.files[0]); });
  })();
</script>
@endpush
@endsection
