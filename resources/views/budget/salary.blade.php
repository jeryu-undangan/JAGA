@extends('layouts.app')

@section('title', 'Gaji Bulanan - UangKos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Dashboard Gaji Bulanan</h1>
            <p>Ringkasan keuangan {{ $month }}@if ($nextPayday) · Gajian berikutnya {{ $nextPayday->format('d/m/Y') }}@endif</p>
        </div>
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

    <div class="salary-summary-grid">
        <section class="card salary-balance-card">
            <span>Sisa saldo bulan ini</span>
            <strong>Rp {{ number_format($availableBalance, 0, ',', '.') }}</strong>
            <div class="salary-balance-note">
                Total pemasukan Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                <span aria-hidden="true"> · </span>
                Total pengeluaran Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
            </div>
        </section>
        <section class="card salary-summary-card salary-income-card">
            <span>Total pemasukan bulan ini</span>
            <strong>Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</strong>
        </section>
        <section class="card salary-summary-card salary-expense-card">
            <span>Total pengeluaran bulan ini</span>
            <strong>Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</strong>
        </section>
    </div>

    <div class="salary-content-grid">
        <div class="salary-main-column">
            <section class="card form-card">
                <div class="section-heading">
                    <div>
                        <h2>Gaji Pokok</h2>
                        <p>Atur nominal gaji bulanan dan tanggal gajian.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('salary.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="salary-form-row">
                        <div>
                            <label for="base_salary">Nominal gaji pokok (Rp)</label>
                            <input id="base_salary" name="base_salary" type="text" inputmode="numeric" data-currency-input autocomplete="off" value="{{ old('base_salary', auth()->user()->base_salary ? number_format(auth()->user()->base_salary, 0, ',', '.') : '') }}" placeholder="Contoh: 5.000.000" required>
                        </div>
                        <div>
                            <label for="salary_payday_day">Tanggal gajian setiap bulan</label>
                            <select id="salary_payday_day" name="salary_payday_day" required>
                                <option value="">Pilih tanggal</option>
                                @for ($day = 1; $day <= 31; $day++)
                                    <option value="{{ $day }}" @selected((int) old('salary_payday_day', auth()->user()->salary_payday_day) === $day)>Tanggal {{ $day }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <p class="salary-hint">Untuk bulan yang tidak memiliki tanggal tersebut, gajian dianggap pada hari terakhir bulan.</p>
                    <button class="btn-main" type="submit">Simpan Gaji Pokok</button>
                </form>
            </section>

            <section class="card salary-chart-card">
                <div class="section-heading">
                    <div>
                        <h2>Pengeluaran per Kategori</h2>
                        <p>Perbandingan pengeluaran bulan ini.</p>
                    </div>
                </div>
                @forelse ($chartCategories as $item)
                    <div class="salary-chart-row">
                        <div class="salary-chart-label">
                            <span>{{ $item['category'] }}</span>
                            <strong>Rp {{ number_format($item['amount'], 0, ',', '.') }} · {{ $item['percent'] }}%</strong>
                        </div>
                        <div class="salary-chart-track" role="img" aria-label="{{ $item['category'] }} {{ $item['percent'] }} persen">
                            <span style="width: {{ $item['percent'] }}%"></span>
                        </div>
                    </div>
                @empty
                    <p class="pesan-kosong">Belum ada pengeluaran yang dapat ditampilkan.</p>
                @endforelse
            </section>

            <section class="card salary-history-card">
                <div class="section-heading">
                    <div>
                        <h2>Transaksi Bulan Ini</h2>
                        <p>Pemasukan tambahan dan pengeluaran terbaru.</p>
                    </div>
                </div>
                @forelse ($transactions as $transaction)
                    <article class="item-transaksi">
                        <div>
                            <span class="transaction-name">{{ $transaction->name }}</span>
                            <small class="transaction-meta">{{ $transaction->category }} · {{ $transaction->transaction_date->format(auth()->user()->date_format ?? 'd/m/Y') }}</small>
                        </div>
                        <div class="transaction-actions">
                            <span class="{{ $transaction->type === 'income' ? 'pemasukan-text' : 'pengeluaran-text' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </span>
                            <form method="POST" action="{{ route('salary.transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-delete" type="submit" aria-label="Hapus transaksi {{ $transaction->name }}">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="pesan-kosong">Belum ada transaksi tambahan bulan ini.</p>
                @endforelse
            </section>
        </div>

        <aside class="salary-side-column">
            <section class="card form-card">
                <h2>Anggaran Bulanan per Kategori</h2>
                <p class="salary-hint">Peringatan muncul saat penggunaan mencapai 80% atau melewati batas.</p>
                <form method="POST" action="{{ route('salary.budgets.store') }}">
                    @csrf
                    <label for="budget_category">Kategori</label>
                    <select id="budget_category" name="category" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($expenseCategories as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <label for="budget_amount">Batas anggaran (Rp)</label>
                    <input id="budget_amount" name="amount" type="text" inputmode="numeric" data-currency-input autocomplete="off" value="{{ old('amount') }}" placeholder="Contoh: 1.000.000" required>
                    <button class="btn-main" type="submit">Simpan Anggaran</button>
                </form>
                <div class="salary-budget-list">
                    @foreach ($budgetStatuses as $budget)
                        <div class="salary-budget-item">
                            <div class="salary-budget-label">
                                <strong>{{ $budget['category'] }}</strong>
                                @if ($budget['limit'])
                                    <span>Rp {{ number_format($budget['spent'], 0, ',', '.') }} / Rp {{ number_format($budget['limit'], 0, ',', '.') }}</span>
                                @else
                                    <span>Anggaran belum diatur · Rp {{ number_format($budget['spent'], 0, ',', '.') }} terpakai</span>
                                @endif
                            </div>
                            @if ($budget['limit'])
                                <div class="salary-budget-track {{ $budget['status'] }}">
                                    <span style="width: {{ $budget['percent'] }}%"></span>
                                </div>
                                @if ($budget['status'] === 'danger')
                                    <small class="salary-budget-warning danger">Anggaran terlampaui.</small>
                                @elseif ($budget['status'] === 'warning')
                                    <small class="salary-budget-warning">Anggaran hampir habis.</small>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
@endsection
