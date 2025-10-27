<x-guest-layout>
    <div class="text-center mb-4">
        <h1 class="h3 fw-bold mb-1">Create your account</h1>
        <p class="text-muted small mb-0">Join us and start your journey.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label for="first_name" class="form-label">Prénom</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" autofocus class="form-control form-control-lg @error('first_name') is-invalid @enderror" placeholder="Ex: Aïcha">
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 col-md-6">
                <label for="last_name" class="form-label">Nom</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" placeholder="Ex: Ouédraogo">
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="you@example.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-3">
            <label for="phone" class="form-label">Téléphone (optionnel)</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="form-control form-control-lg @error('phone') is-invalid @enderror" placeholder="Ex: +226 70 00 00 00">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-3">
            <label for="password" class="form-label">Mot de passe</label>
            <div class="input-group input-group-lg">
                <input id="password" type="password" name="password" required autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" id="togglePasswordReg" aria-label="Afficher">👁️</button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-3">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <div class="input-group input-group-lg">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="••••••••">
                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConf" aria-label="Afficher">👁️</button>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">Créer mon compte</button>
    </form>

    <div class="text-center text-muted small mt-3">
        <span>Déjà inscrit ? </span>
        <a href="{{ route('login') }}" class="fw-semibold">Se connecter</a>
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
            const t1 = document.getElementById('togglePasswordReg');
            const p1 = document.getElementById('password');
            const t2 = document.getElementById('togglePasswordConf');
            const p2 = document.getElementById('password_confirmation');
            if(t1 && p1){ t1.addEventListener('click', function(){ const isPwd = p1.type==='password'; p1.type = isPwd ? 'text':'password'; this.textContent = isPwd ? '🙈':'👁️'; }); }
            if(t2 && p2){ t2.addEventListener('click', function(){ const isPwd = p2.type==='password'; p2.type = isPwd ? 'text':'password'; this.textContent = isPwd ? '🙈':'👁️'; }); }
        })();
    </script>
</x-guest-layout>

