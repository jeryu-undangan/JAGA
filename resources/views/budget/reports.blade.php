@extends('layouts.app')

@section('title', 'Laporan Keuangan - UangKos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Laporan Keuangan</h1>
            <p>Grafik pemasukan dan pengeluaranmu.</p>
        </div>
        <div class="page-heading-actions">
            <a class="button btn-main" href="{{ route('reports.export', request()->query()) }}">Unduh CSV</a>
            <a class="button btn-quiet" href="{{ route('budget.index') }}">Kembali ke dashboard</a>
        </div>
    </div>

    <section class="overview-grid" aria-label="Ringkasan periode">
        <article class="card metric-card">
            <div class="metric-top"><span>Pemasukan periode ini</span><span class="metric-icon">↙</span></div>
            <div class="metric-value">Rp {{ number_format($periodIncome, 0, ',', '.') }}</div>
        </article>
        <article class="card metric-card">
            <div class="metric-top"><span>Pengeluaran periode ini</span><span class="metric-icon">↗</span></div>
            <div class="metric-value">Rp {{ number_format($periodExpense, 0, ',', '.') }}</div>
        </article>
        <article class="card metric-card">
            <div class="metric-top"><span>Selisih periode ini</span><span class="metric-icon">=</span></div>
            <div class="metric-value">Rp {{ number_format($periodIncome - $periodExpense, 0, ',', '.') }}</div>
        </article>
    </section>

    @if ($periodExpense > $periodIncome)
        <div class="insight warning" style="margin-bottom:18px">Pengeluaran periode ini lebih besar daripada pemasukan. Tinjau kembali catatan pengeluaranmu.</div>
    @endif
    @if ($errors->any())
        <div class="errors" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="content-grid">
        <section class="card">
            <div class="section-heading">
                <div>
                    <h2>Grafik Arus Kas</h2>
                    <p>Perbandingan pemasukan dan pengeluaran</p>
                </div>
            </div>
            <form class="report-filters" method="GET" action="{{ route('reports.index') }}">
                <div>
                    <label for="filter">Jenis filter</label>
                    <select id="filter" name="filter">
                        <option value="month" @selected($filter === 'month')>Pilih bulan</option>
                        <option value="dates" @selected($filter === 'dates')>Rentang tanggal</option>
                    </select>
                </div>
                <div id="monthFilter" @if ($filter === 'dates') hidden @endif>
                    <label for="month">Bulan</label>
                    <input id="month" name="month" type="month" value="{{ $month }}">
                </div>
                <div id="startDateFilter" @if ($filter !== 'dates') hidden @endif>
                    <label for="start_date">Dari tanggal</label>
                    <input id="start_date" name="start_date" type="date" value="{{ $filter === 'dates' ? $startDate : '' }}">
                </div>
                <div id="endDateFilter" @if ($filter !== 'dates') hidden @endif>
                    <label for="end_date">Sampai tanggal</label>
                    <input id="end_date" name="end_date" type="date" value="{{ $filter === 'dates' ? $endDate : '' }}">
                </div>
                <button class="btn-main btn-small" type="submit">Tampilkan</button>
            </form>
            <div class="chart-wrap">
                <canvas id="cashflowChart" aria-label="Grafik pemasukan dan pengeluaran" role="img"></canvas>
                <div class="chart-empty" id="cashflowEmpty">Belum ada transaksi pada periode ini.</div>
            </div>
        </section>

        <section class="card">
            <div class="section-heading">
                <div>
                    <h2>Pengeluaran per Kategori</h2>
                    <p>Rincian pengeluaran dalam periode ini</p>
                </div>
            </div>
            <div class="chart-wrap-small">
                <canvas id="categoryChart" aria-label="Grafik pengeluaran per kategori" role="img"></canvas>
                <div class="chart-empty" id="categoryEmpty">Belum ada pengeluaran di periode ini.</div>
            </div>
        </section>
    </div>

    <section class="card" style="margin-top:18px">
        <div class="section-heading">
            <div>
                <h2>Riwayat pada Periode Ini</h2>
                <p>Semua transaksi pada bulan atau rentang tanggal yang dipilih</p>
            </div>
        </div>
        @forelse ($transactions as $transaction)
            <article class="item-transaksi">
                <div>
                    <span class="transaction-name">{{ $transaction->name }}</span>
                    <small class="transaction-meta">{{ $transaction->category }} · {{ $transaction->transaction_date?->format(auth()->user()->date_format ?? 'd/m/Y') ?? $transaction->created_at->format(auth()->user()->date_format ?? 'd/m/Y') }}</small>
                </div>
                <span class="{{ $transaction->type === 'income' ? 'pemasukan-text' : 'pengeluaran-text' }}">
                    {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                </span>
            </article>
        @empty
            <p class="pesan-kosong">Tidak ada transaksi pada periode tanggal yang dipilih.</p>
        @endforelse
        {{ $transactions->links() }}
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js" nonce="{{ request()->attributes->get('cspNonce') }}"></script>
    <script nonce="{{ request()->attributes->get('cspNonce') }}">
        const chartLabels = @json($chartLabels);
        const incomeSeries = @json($incomeSeries);
        const expenseSeries = @json($expenseSeries);
        const categoryLabels = @json($categoryLabels);
        const categorySeries = @json($categorySeries);
        const rupiah = value => `Rp ${new Intl.NumberFormat('id-ID').format(value)}`;
        const filterInput = document.getElementById('filter');
        const monthFilter = document.getElementById('monthFilter');
        const startDateFilter = document.getElementById('startDateFilter');
        const endDateFilter = document.getElementById('endDateFilter');
        const monthInput = document.getElementById('month');
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');

        function toggleReportFilters() {
            const useDates = filterInput.value === 'dates';
            monthFilter.hidden = useDates;
            monthInput.disabled = useDates;
            startDateFilter.hidden = !useDates;
            endDateFilter.hidden = !useDates;
            startDateInput.disabled = !useDates;
            endDateInput.disabled = !useDates;
            startDateInput.required = useDates;
            endDateInput.required = useDates;
        }

        filterInput.addEventListener('change', toggleReportFilters);
        toggleReportFilters();

        if (incomeSeries.some(value => value > 0) || expenseSeries.some(value => value > 0)) {
            new Chart(document.getElementById('cashflowChart'), {
                type: 'bar',
                data: { labels: chartLabels, datasets: [
                    { label: 'Pemasukan', data: incomeSeries, backgroundColor: '#0879f9', borderRadius: 5, maxBarThickness: 18 },
                    { label: 'Pengeluaran', data: expenseSeries, backgroundColor: '#ff9b72', borderRadius: 5, maxBarThickness: 18 }
                ] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 18 } },
                        tooltip: { callbacks: { label: context => `${context.dataset.label}: ${rupiah(context.parsed.y)}` } }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#8491a5', maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
                        y: { beginAtZero: true, grid: { color: '#edf1f6' }, ticks: { color: '#8491a5', callback: value => rupiah(value) } }
                    }
                }
            });
        } else {
            document.getElementById('cashflowChart').style.display = 'none';
            document.getElementById('cashflowEmpty').style.display = 'grid';
        }

        if (categorySeries.length) {
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: { labels: categoryLabels, datasets: [{
                    data: categorySeries,
                    backgroundColor: ['#0879f9', '#41b883', '#ffb547', '#9b8afb', '#ff8577'],
                    borderWidth: 0,
                    hoverOffset: 5
                }] },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 16 } },
                        tooltip: { callbacks: { label: context => `${context.label}: ${rupiah(context.parsed)}` } }
                    }
                }
            });
        } else {
            document.getElementById('categoryChart').style.display = 'none';
            document.getElementById('categoryEmpty').style.display = 'grid';
        }
    </script>
@endpush
