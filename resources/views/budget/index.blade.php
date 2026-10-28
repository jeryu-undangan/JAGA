@extends('layouts.app')

@section('title', 'Dashboard Keuangan - UangKos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Dashboard Keuangan</h1>
            <p>Ringkasan keuanganmu dalam satu halaman.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert" role="status">{{ session('status') }}</div>
    @endif
    @if ($isLowBalance)
        <div class="insight warning" role="status" style="margin-bottom:18px">
            Saldo menipis: sisa uangmu Rp {{ number_format($balance, 0, ',', '.') }} sudah mencapai atau di bawah {{ auth()->user()->low_balance_percent }}% target mingguan (Rp {{ number_format($lowBalanceThreshold, 0, ',', '.') }}).
        </div>
    @endif

    <section class="card daily-budget-card daily-budget-{{ $dailyStatus }}" aria-labelledby="dailyBudgetTitle">
        <div class="daily-budget-heading">
            <div>
                <span class="daily-budget-eyebrow">PERINGATAN DARURAT</span>
                <h2 id="dailyBudgetTitle">Batas Aman Pengeluaran</h2>
            </div>
            <span class="daily-budget-status">
                @if ($dailyStatus === 'danger')
                    Batas terlampaui
                @elseif ($dailyStatus === 'warning')
                    Hampir mencapai batas
                @else
                    Masih aman
                @endif
            </span>
        </div>
        <div class="daily-budget-grid">
            <div class="daily-budget-stat">
                <span>Kiriman berikutnya</span>
                <strong>{{ $daysUntilPayday }} {{ auth()->user()->payday_interval === 'workdays' ? 'hari kerja' : 'hari' }} lagi</strong>
                <small>{{ $nextPayday->format(auth()->user()->date_format ?? 'd/m/Y') }}</small>
                <small>
                    @switch(auth()->user()->payday_interval)
                        @case('weekly') Setiap minggu @break
                        @case('bimonthly') Setiap 2 bulan @break
                        @case('workdays') Setiap {{ auth()->user()->payday_workday_interval }} hari kerja @break
                        @default Setiap bulan
                    @endswitch
                </small>
            </div>
            <div class="daily-budget-stat">
                <span>Rekomendasi maksimal per hari</span>
                <strong>Rp {{ number_format($dailyAllowance, 0, ',', '.') }}</strong>
                <small>Dihitung dari sisa saldo dibagi sisa {{ auth()->user()->payday_interval === 'workdays' ? 'hari kerja' : 'hari' }}</small>
            </div>
            <div class="daily-budget-stat">
                <span>Pengeluaran hari ini</span>
                <strong>Rp {{ number_format($todayExpense, 0, ',', '.') }}</strong>
                <small>Sisa batas hari ini: Rp {{ number_format($dailyAllowanceRemaining, 0, ',', '.') }}</small>
            </div>
        </div>
    </section>

    <section class="overview-grid" aria-label="Ringkasan keuangan">
        <article class="card card-saldo">
            <span class="balance-label">Sisa Uang Saku</span>
            <h2>Rp {{ number_format($balance, 0, ',', '.') }}</h2>
            <div class="balance-meta">
                <span>Target saku: <strong>Rp {{ number_format(auth()->user()->weekly_budget, 0, ',', '.') }}</strong></span>
                <span>Semua pemasukan: <strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong></span>
                <span>Semua pengeluaran: <strong>Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong></span>
            </div>
        </article>
        <article class="card metric-card">
            <div class="metric-top"><span>Total Pemasukan</span><span class="metric-icon">↙</span></div>
            <div class="metric-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        </article>
        <article class="card metric-card">
            <div class="metric-top"><span>Total Pengeluaran</span><span class="metric-icon">↗</span></div>
            <div class="metric-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        </article>
    </section>

    <section class="card" id="transactions">
        <div class="section-heading">
            <div>
                <h2>Catatan Keuangan Terbaru</h2>
                <p>Transaksi pemasukan dan pengeluaran terakhir</p>
            </div>
            <a class="button btn-main" href="{{ route('transactions.index') }}">Buka pencatatan</a>
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
            <p class="pesan-kosong">Belum ada catatan keuangan.</p>
        @endforelse
        @if ($transactions->isNotEmpty())
            <a class="text-link" href="{{ route('transactions.index') }}">Lihat semua catatan →</a>
        @endif
    </section>
@endsection
