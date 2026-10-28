@extends('layouts.app')

@section('title', 'Pengaturan Target - UangKos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Pengaturan Target</h1>
            <p>Atur target uang saku, frekuensi, dan jadwal kirimanmu.</p>
        </div>
        <a class="button btn-quiet" href="{{ auth()->user()->application_mode === 'salary' ? route('salary.index') : route('budget.index') }}">Kembali ke dashboard</a>
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

    <section class="card form-card mode-settings-card">
        <h2>Jenis Pencatatan</h2>
        <p class="schedule-description">Pilih tampilan keuangan yang ingin kamu gunakan. Data Uang Kos tetap tersimpan jika kamu berpindah mode.</p>
        <form id="applicationModeForm" method="POST" action="{{ route('application-mode.update') }}">
            @csrf
            @method('PUT')
            <fieldset class="mode-options" aria-label="Pilih jenis pencatatan">
                <label class="mode-choice">
                    <input type="radio" name="application_mode" value="salary" @checked(auth()->user()->application_mode === 'salary') required>
                    <span class="mode-choice-card">
                        <span class="mode-choice-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="14" rx="2"></rect><path d="M16 14h2M7 6V4h10v2M7 10h5"></path></svg>
                        </span>
                        <strong>Gaji Bulanan</strong>
                        <small>Kelola pemasukan gaji, pengeluaran, dan anggaran bulanan.</small>
                        <span class="mode-choice-check" aria-hidden="true"></span>
                    </span>
                </label>
                <label class="mode-choice">
                    <input type="radio" name="application_mode" value="kos" @checked(auth()->user()->application_mode === 'kos') required>
                    <span class="mode-choice-card">
                        <span class="mode-choice-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"></path></svg>
                        </span>
                        <strong>Uang Kos</strong>
                        <small>Catat uang saku, jadwal kiriman, dan pengeluaran harian.</small>
                        <span class="mode-choice-check" aria-hidden="true"></span>
                    </span>
                </label>
            </fieldset>
            <p class="mode-settings-hint" role="status">Mode akan langsung disimpan saat kamu memilih.</p>
        </form>
    </section>
    <script nonce="{{ request()->attributes->get('cspNonce') }}">
        document.querySelectorAll('#applicationModeForm input[name="application_mode"]').forEach(option => {
            option.addEventListener('change', () => option.form.requestSubmit());
        });
    </script>

    @if (auth()->user()->application_mode === 'kos')
    <section class="card form-card" style="max-width: 620px; margin-top: 18px">
        <h2>Target Uang Saku Mingguan</h2>
        <p class="page-heading" style="display:block; margin:8px 0 18px">Target ini menjadi saldo dasar. Pemasukan akan menambah dan pengeluaran akan mengurangi saldo.</p>
        <form method="POST" action="{{ route('budget.update') }}">
            @csrf
            @method('PUT')
            <label for="weekly_budget">Target mingguan (Rp)</label>
            <input id="weekly_budget" name="weekly_budget" type="text" inputmode="numeric" data-currency-input autocomplete="off" value="{{ old('weekly_budget', number_format(auth()->user()->weekly_budget, 0, ',', '.')) }}" required>
            <button class="btn-main" type="submit">Simpan Target</button>
        </form>
    </section>

    <section class="card form-card" style="max-width: 620px; margin-top: 18px">
        <h2>Jadwal Kiriman Uang Saku</h2>
        <p style="margin:8px 0 18px">Atur frekuensi kiriman dan tanggal kiriman berikutnya. Jadwal ini menjadi acuan hitung mundur dan rekomendasi harian.</p>
        <form method="POST" action="{{ route('budget.schedule.setup.update') }}">
            @csrf
            @method('PUT')
            <label for="payday_interval">Frekuensi kiriman</label>
            <select id="payday_interval" name="payday_interval" required>
                <option value="weekly" @selected(old('payday_interval', auth()->user()->payday_interval) === 'weekly')>Setiap minggu</option>
                <option value="monthly" @selected(old('payday_interval', auth()->user()->payday_interval) === 'monthly')>Setiap bulan</option>
                <option value="bimonthly" @selected(old('payday_interval', auth()->user()->payday_interval) === 'bimonthly')>Setiap 2 bulan</option>
                <option value="workdays" @selected(old('payday_interval', auth()->user()->payday_interval) === 'workdays')>Setiap beberapa hari kerja</option>
            </select>
            <div id="workdayIntervalField" @if (old('payday_interval', auth()->user()->payday_interval) !== 'workdays') hidden @endif>
                <label for="payday_workday_interval">Jumlah hari kerja antar kiriman</label>
                <input id="payday_workday_interval" name="payday_workday_interval" type="number" min="1" max="90" value="{{ old('payday_workday_interval', auth()->user()->payday_workday_interval ?? 5) }}" @if (old('payday_interval', auth()->user()->payday_interval) === 'workdays') required @else disabled @endif>
                <p class="schedule-hint">Sabtu dan Minggu dilewati. Hari libur nasional tetap dihitung sebagai hari kerja.</p>
            </div>
            <label for="next_payday">Tanggal kiriman berikutnya</label>
            <input id="next_payday" name="next_payday" type="date" min="{{ now()->addDay()->toDateString() }}" value="{{ old('next_payday', $nextPayday?->toDateString() ?? '') }}" required>
            <button class="btn-main" type="submit">Simpan Jadwal Kiriman</button>
        </form>
    </section>
    @else
        <section class="card salary-settings-note">
            <h2>Mode Gaji Bulanan</h2>
            <p>Halaman khusus Gaji Bulanan masih disiapkan. Pengaturan Pencatatan Uang Kos akan tampil kembali saat mode Kos dipilih.</p>
        </section>
    @endif
@endsection

@if (auth()->user()->application_mode === 'kos')
@push('scripts')
    <script nonce="{{ request()->attributes->get('cspNonce') }}">
        const paydayInterval = document.getElementById('payday_interval');
        const workdayIntervalField = document.getElementById('workdayIntervalField');
        const workdayIntervalInput = document.getElementById('payday_workday_interval');

        function toggleWorkdayInterval() {
            const isWorkdaySchedule = paydayInterval.value === 'workdays';
            workdayIntervalField.hidden = !isWorkdaySchedule;
            workdayIntervalInput.disabled = !isWorkdaySchedule;
            workdayIntervalInput.required = isWorkdaySchedule;
        }

        paydayInterval.addEventListener('change', toggleWorkdayInterval);
        toggleWorkdayInterval();
    </script>
@endpush
@endif
