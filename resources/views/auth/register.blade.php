@extends('layouts.app')

@section('title', 'Daftar - Pengatur Uang Kos')

@section('content')
    @include('auth.brand')
    <section class="card auth-card">
        <h3>Daftar Akun Baru</h3>

        @if ($errors->any())
            <div class="errors" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form class="auth-form" method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="auth-field">
                <label for="name">Nama</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus>
            </div>
            <div class="auth-field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" aria-describedby="email-status" required>
                <p class="field-status" id="email-status" data-state="neutral" aria-live="polite">Masukkan alamat email yang valid.</p>
            </div>
            <div class="auth-field">
                <label for="password">Password</label>
                <div class="password-field">
                    <input id="password" name="password" type="password" autocomplete="new-password" aria-describedby="password-rules" required>
                    <button class="password-toggle" type="button" data-password-toggle aria-label="Tampilkan password" aria-controls="password">
                        <svg class="password-icon-visible" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="password-icon-hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.4 3.8-1"></path></svg>
                    </button>
                </div>
                <ul class="password-rules" id="password-rules" aria-live="polite">
                    <li data-rule="length" data-state="neutral">Mengandung 8 karakter atau lebih</li>
                    <li data-rule="uppercase" data-state="neutral">Mengandung huruf kapital (A-Z)</li>
                    <li data-rule="number" data-state="neutral">Mengandung angka (0-9)</li>
                </ul>
            </div>
            <div class="auth-field">
                <label for="password_confirmation">Ulangi password</label>
                <div class="password-field">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" aria-describedby="password-confirmation-status" required>
                    <button class="password-toggle" type="button" data-password-toggle aria-label="Tampilkan password" aria-controls="password_confirmation">
                        <svg class="password-icon-visible" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="password-icon-hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.4 3.8-1"></path></svg>
                    </button>
                </div>
                <p class="field-status" id="password-confirmation-status" data-state="neutral" aria-live="polite">Masukkan ulang password yang sama.</p>
            </div>
            <button class="btn-main" type="submit">Daftar</button>
        </form>

        <p class="toggle-auth">Sudah punya akun? <a href="{{ route('login') }}">Login</a></p>
    </section>
@endsection

@push('scripts')
    <script nonce="{{ request()->attributes->get('cspNonce') }}">
        const emailInput = document.getElementById('email');
        const emailStatus = document.getElementById('email-status');
        const passwordInput = document.getElementById('password');
        const confirmationInput = document.getElementById('password_confirmation');
        const confirmationStatus = document.getElementById('password-confirmation-status');

        function setFieldStatus(element, state, message) {
            element.dataset.state = state;
            element.textContent = message;
        }

        function updateEmailStatus() {
            if (!emailInput.value) {
                setFieldStatus(emailStatus, 'neutral', 'Masukkan alamat email yang valid.');
                return;
            }

            const isValid = emailInput.validity.valid;
            setFieldStatus(emailStatus, isValid ? 'valid' : 'invalid', isValid ? 'Format email sesuai.' : 'Format email belum sesuai.');
        }

        function updatePasswordRules() {
            const password = passwordInput.value;
            const rules = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                number: /\d/.test(password),
            };

            document.querySelectorAll('.password-rules [data-rule]').forEach(rule => {
                const state = password ? (rules[rule.dataset.rule] ? 'valid' : 'invalid') : 'neutral';
                rule.dataset.state = state;
            });
        }

        function updatePasswordConfirmation() {
            if (!confirmationInput.value) {
                setFieldStatus(confirmationStatus, 'neutral', 'Masukkan ulang password yang sama.');
                return;
            }

            const matches = confirmationInput.value === passwordInput.value;
            setFieldStatus(confirmationStatus, matches ? 'valid' : 'invalid', matches ? 'Password cocok.' : 'Password belum cocok.');
        }

        emailInput.addEventListener('input', updateEmailStatus);
        passwordInput.addEventListener('input', () => {
            updatePasswordRules();
            updatePasswordConfirmation();
        });
        confirmationInput.addEventListener('input', updatePasswordConfirmation);
    </script>
@endpush
