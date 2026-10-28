@extends('layouts.app')

@section('title', 'Pilih Jenis Pencatatan - UangKos')

@section('content')
    <div class="mode-picker">
        <div class="mode-picker-brand">
            <img src="{{ asset('app-logo.png') }}" alt="Logo UangKos">
            <span>UangKos</span>
        </div>
        <section class="card mode-picker-card" aria-labelledby="modePickerTitle">
            <span class="mode-picker-eyebrow">PENGATURAN AWAL</span>
            <h1 id="modePickerTitle">Pilih kebutuhanmu</h1>
            <p>Tentukan tampilan yang sesuai. Pilihan ini bisa diubah nanti melalui Pengaturan.</p>

            @if ($errors->any())
                <div class="errors" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('application-mode.update') }}">
                @csrf
                @method('PUT')
                <fieldset class="mode-options">
                    <legend class="sr-only">Pilih jenis pencatatan</legend>
                    <label class="mode-choice">
                        <input type="radio" name="application_mode" value="salary" @checked(old('application_mode', auth()->user()->application_mode) === 'salary') required>
                        <span class="mode-choice-card">
                            <span class="mode-choice-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="14" rx="2"></rect><path d="M16 14h2M7 6V4h10v2M7 10h5"></path></svg>
                            </span>
                            <strong>Pekerja</strong>
                            <small>Kelola gaji bulanan, pemasukan tambahan, pengeluaran, dan anggaran.</small>
                            <span class="mode-choice-check" aria-hidden="true"></span>
                        </span>
                    </label>
                    <label class="mode-choice">
                        <input type="radio" name="application_mode" value="kos" @checked(old('application_mode', auth()->user()->application_mode) === 'kos') required>
                        <span class="mode-choice-card">
                            <span class="mode-choice-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"></path></svg>
                            </span>
                            <strong>Anak Kos</strong>
                            <small>Catat uang saku, kiriman, pengeluaran harian, dan batas aman.</small>
                            <span class="mode-choice-check" aria-hidden="true"></span>
                        </span>
                    </label>
                </fieldset>
                <button class="btn-main" type="submit">Lanjutkan</button>
            </form>
        </section>
    </div>
@endsection
