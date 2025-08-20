<section>
  <div class="mb-2 fw-semibold">{{ __('Mettre à jour le mot de passe') }}</div>
  <div class="text-muted small mb-3">{{ __('Utilisez un mot de passe long et unique pour sécuriser votre compte.') }}</div>

  <form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="mb-3">
      <label for="update_password_current_password" class="form-label">{{ __('Mot de passe actuel') }}</label>
      <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
      @if($errors->updatePassword?->has('current_password'))
        <div class="text-danger small">{{ $errors->updatePassword->first('current_password') }}</div>
      @endif
    </div>

    <div class="row g-2">
      <div class="col-md-6">
        <label for="update_password_password" class="form-label">{{ __('Nouveau mot de passe') }}</label>
        <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
        @if($errors->updatePassword?->has('password'))
          <div class="text-danger small">{{ $errors->updatePassword->first('password') }}</div>
        @endif
      </div>
      <div class="col-md-6">
        <label for="update_password_password_confirmation" class="form-label">{{ __('Confirmer le mot de passe') }}</label>
        <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
        @if($errors->updatePassword?->has('password_confirmation'))
          <div class="text-danger small">{{ $errors->updatePassword->first('password_confirmation') }}</div>
        @endif
      </div>
    </div>

    <div class="mt-3 d-flex align-items-center gap-3">
      <button class="btn btn-primary" type="submit">{{ __('Enregistrer') }}</button>
      @if (session('status') === 'password-updated')
        <span class="badge text-bg-success">{{ __('Sauvegardé') }}</span>
      @endif
    </div>
  </form>
</section>
