@extends('layouts.app')

@section('title', 'Atur Jadwal Kiriman - UangKos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Atur Jadwal Kiriman</h1>
            <p>Pengaturan ini cukup dilakukan sekali. Jadwalnya bisa diubah kapan saja di Pengaturan Target.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="errors" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="card form-card schedule-setup-card">
        <h2>Kapan biasanya uang saku dikirim?</h2>
        <p class="schedule-description">Pilih seberapa sering kiriman datang dan tanggal kiriman berikutnya. Jadwal ini dipakai untuk hitung mundur dan rekomendasi batas belanja harian.</p>
        <form method="POST" action="{{ route('budget.schedule.setup.update') }}">
            @csrf
            @method('PUT')
            <label for="payday_interval">Seberapa sering menerima kiriman?</label>
            <select id="payday_interval" name="payday_interval" required>
                <option value="weekly" @selected(old('payday_interval') === 'weekly')>Setiap minggu</option>
                <option value="monthly" @selected(old('payday_interval', 'monthly') === 'monthly')>Setiap bulan</option>
                <option value="bimonthly" @selected(old('payday_interval') === 'bimonthly')>Setiap 2 bulan</option>
                <option value="workdays" @selected(old('payday_interval') === 'workdays')>Setiap beberapa hari kerja</option>
            </select>
            <div id="workdayIntervalField" @if (old('payday_interval') !== 'workdays') hidden @endif>
                <label for="payday_workday_interval">Jumlah hari kerja antar kiriman</label>
                <input id="payday_workday_interval" name="payday_workday_interval" type="number" min="1" max="90" value="{{ old('payday_workday_interval', 5) }}" @if (old('payday_interval') === 'workdays') required @else disabled @endif>
                <p class="schedule-hint">Hitungan melewati Sabtu dan Minggu. Hari libur nasional tetap dihitung sebagai hari kerja.</p>
            </div>
            <label for="next_payday">Tanggal kiriman berikutnya</label>
            <input id="next_payday" name="next_payday" type="date" min="{{ now()->addDay()->toDateString() }}" value="{{ old('next_payday', now()->addDay()->toDateString()) }}" required>
            <p class="schedule-hint">Pilih tanggal kiriman yang akan datang. Sistem akan menghitung jadwal berikutnya sesuai periode pilihan.</p>
            <button class="btn-main" type="submit">Simpan dan Lanjutkan</button>
        </form>
    </section>
@endsection

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
