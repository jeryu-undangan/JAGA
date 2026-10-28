@extends('layouts.app')

@section('title', 'Pengaturan Akun - UangKos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Pengaturan Akun &amp; Aplikasi</h1>
            <p>Kelola profil, keamanan, tampilan, dan data keuanganmu.</p>
        </div>
        <a class="button btn-quiet" href="{{ $user->application_mode === 'salary' ? route('salary.index') : route('budget.index') }}">Kembali ke dashboard</a>
    </div>

    @if (session('status'))
        <div class="alert" role="status">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="side-column">
        <section class="card form-card">
            <h2>Profil Pengguna</h2>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <label for="name">Nama tampilan</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" required>
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required>
                <label for="photo">Foto profil (JPG, PNG, atau WebP; maksimal 2 MB)</label>
                <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp">
                <button class="btn-main" type="submit">Simpan Profil</button>
            </form>
        </section>

        <section class="card form-card">
            <h2>Preferensi &amp; Notifikasi</h2>
            <form method="POST" action="{{ route('preferences.update') }}">
                @csrf
                @method('PUT')
                <label for="date_format">Format tanggal</label>
                <select id="date_format" name="date_format" required>
                    <option value="d/m/Y" @selected(old('date_format', $user->date_format) === 'd/m/Y')>22/10/2026</option>
                    <option value="d M Y" @selected(old('date_format', $user->date_format) === 'd M Y')>22 Oct 2026</option>
                    <option value="Y-m-d" @selected(old('date_format', $user->date_format) === 'Y-m-d')>2026-10-22</option>
                </select>
                <label for="low_balance_percent">Peringatan saldo menipis</label>
                <select id="low_balance_percent" name="low_balance_percent" required>
                    @foreach ([10, 20, 30, 50] as $percent)
                        <option value="{{ $percent }}" @selected((int) old('low_balance_percent', $user->low_balance_percent) === $percent)>Saat saldo tersisa {{ $percent }}% dari target</option>
                    @endforeach
                </select>
                <label for="dark_mode">Mode tampilan</label>
                <select id="dark_mode" name="dark_mode" required>
                    <option value="0" @selected(! old('dark_mode', (int) $user->dark_mode))>Terang</option>
                    <option value="1" @selected((bool) old('dark_mode', (int) $user->dark_mode))>Gelap</option>
                </select>
                <p class="metric-note">Mata uang menggunakan Rupiah (Rp). Pengingat harian belum diaktifkan.</p>
                <button class="btn-main" type="submit">Simpan Preferensi</button>
            </form>
        </section>

        <section class="card form-card">
            <h2>Ubah Kata Sandi</h2>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <label for="current_password">Kata sandi saat ini</label>
                <div class="password-field">
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                    <button class="password-toggle" type="button" data-password-toggle aria-label="Tampilkan kata sandi saat ini" aria-controls="current_password">
                        <svg class="password-icon-visible" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="password-icon-hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.4 3.8-1"></path></svg>
                    </button>
                </div>
                <label for="password">Kata sandi baru (mengandung 8 karakter atau lebih, huruf kapital, dan angka)</label>
                <div class="password-field">
                    <input id="password" name="password" type="password" autocomplete="new-password" required>
                    <button class="password-toggle" type="button" data-password-toggle aria-label="Tampilkan kata sandi baru" aria-controls="password">
                        <svg class="password-icon-visible" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="password-icon-hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.4 3.8-1"></path></svg>
                    </button>
                </div>
                <label for="password_confirmation">Ulangi kata sandi baru</label>
                <div class="password-field">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                    <button class="password-toggle" type="button" data-password-toggle aria-label="Tampilkan ulang kata sandi baru" aria-controls="password_confirmation">
                        <svg class="password-icon-visible" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg class="password-icon-hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"></path><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a15.7 15.7 0 0 1-3.1 3.8M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7c1.4 0 2.7-.4 3.8-1"></path></svg>
                    </button>
                </div>
                <button class="btn-main" type="submit">Ubah Kata Sandi</button>
            </form>
            <p class="metric-note">PIN atau biometrik tidak tersedia di aplikasi web ini.</p>
        </section>

        @if ($user->application_mode === 'kos')
        <section class="card form-card danger-zone">
            <h2>Reset Riwayat Transaksi</h2>
            <p class="metric-note">Tindakan ini menghapus permanen semua catatan transaksi akunmu dan tidak dapat dibatalkan.</p>
            <form method="POST" action="{{ route('transactions.destroy-all') }}" onsubmit="return confirm('Yakin menghapus seluruh riwayat transaksi? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <label for="reset_confirmation">Ketik HAPUS untuk mengonfirmasi</label>
                <input id="reset_confirmation" name="confirmation" type="text" autocomplete="off" required>
                <button class="btn-delete" type="submit" style="margin-top:12px">Hapus Semua Riwayat</button>
            </form>
        </section>
        @endif
    </div>
@endsection
