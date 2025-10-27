<x-guest-layout>
    <div class="text-center mb-4">
        <h1 class="h3 fw-bold mb-1">Connectez‑vous</h1>
        <p class="text-muted small mb-0">Ravi de vous revoir ! Entrez vos identifiants.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success small" role="alert">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Adresse e‑mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" autofocus required class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="exemple@domaine.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label mb-0">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a class="small" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                @endif
            </div>
            <div class="input-group input-group-lg">
                <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Afficher le mot de passe">👁️</button>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">Se connecter</button>
    </form>

    <div class="text-center text-muted small mt-3">
        <span>Pas encore de compte ? </span>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="fw-semibold">Créer un compte</a>
        @endif
    </div>

    <div class="d-flex align-items-center my-3">
        <div class="flex-grow-1 border-top"></div>
        <div class="px-2 text-muted small">Ou continuer avec</div>
        <div class="flex-grow-1 border-top"></div>
    </div>

    <div class="d-grid gap-2 gap-md-3 d-md-flex justify-content-md-center">
        <a href="#" class="btn btn-outline-secondary w-100"><span class="me-2">G</span>Google</a>
        <a href="#" class="btn btn-outline-secondary w-100"><span class="me-2">f</span>Facebook</a>
        <a href="#" class="btn btn-outline-secondary w-100"><span class="me-2">X</span>X</a>
    </div>

    <script>
        (function(){
            const btn = document.getElementById('togglePassword');
            const input = document.getElementById('password');
            if(btn && input){
                btn.addEventListener('click', function(){
                    const isPwd = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPwd ? 'text' : 'password');
                    this.textContent = isPwd ? '🙈' : '👁️';
                });
            }
        })();
    </script>
</x-guest-layout>

