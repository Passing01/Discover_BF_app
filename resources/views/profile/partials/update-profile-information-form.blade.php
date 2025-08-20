<section>
  <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('patch')

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Prénom</label>
        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
        @error('first_name')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-6">
        <label class="form-label">Nom</label>
        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
        @error('last_name')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-8">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">Avatar</label>
        <input type="file" name="avatar" class="form-control" accept="image/*">
        @error('avatar')<div class="text-danger small">{{ $message }}</div>@enderror
        <div class="form-text">JPEG/PNG/WEBP, 2 Mo max.</div>
        <div class="mt-2 d-flex align-items-center gap-2">
          <img id="profile-avatar-preview" src="#" alt="Preview" class="rounded d-none" style="height:56px;">
          @if($user->profile_picture)
            <img src="{{ asset('storage/'.$user->profile_picture) }}" alt="Avatar actuel" class="rounded" style="height:56px;">
          @endif
        </div>
      </div>

      <div class="col-md-4">
        <label class="form-label">Langue</label>
        @php $lang = old('primary_language', optional($user->profile)->primary_language ?? 'fr'); @endphp
        <select name="primary_language" class="form-select">
          <option value="fr" @selected($lang==='fr')>Français</option>
          <option value="en" @selected($lang==='en')>English</option>
        </select>
        @error('primary_language')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label">Devise</label>
        @php $currency = old('currency', data_get(optional($user->profile)->preferences, 'currency', 'XOF')); @endphp
        <select name="currency" class="form-select">
          <option value="XOF" @selected($currency==='XOF')>FCFA (XOF)</option>
          <option value="EUR" @selected($currency==='EUR')>Euro (EUR)</option>
          <option value="USD" @selected($currency==='USD')>Dollar (USD)</option>
        </select>
        @error('currency')<div class="text-danger small">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4 d-flex align-items-end">
        @php $notify = (bool) old('notify_email', data_get(optional($user->profile)->preferences, 'notify_email', true)); @endphp
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" id="notify_email" name="notify_email" @checked($notify)>
          <label class="form-check-label" for="notify_email">
            Notifications par e‑mail
          </label>
        </div>
      </div>

      <div class="col-12 d-flex gap-2 mt-2">
        <button class="btn btn-primary" type="submit">Enregistrer</button>
        @if (session('status') === 'profile-updated')
          <span class="badge text-bg-success align-self-center">Sauvegardé</span>
        @endif
      </div>
    </div>
  </form>
</section>
