@extends('layouts.app')

@section('title', 'Pencatatan Transaksi Gaji - UangKos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Pencatatan Transaksi</h1>
            <p>Kelola pemasukan tambahan dan pengeluaran mode Gaji Bulanan.</p>
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

    <div class="content-grid">
        <section class="card">
            <div class="section-heading">
                <div>
                    <h2>Riwayat Transaksi Gaji</h2>
                    <p>Hanya menampilkan transaksi yang dicatat dalam mode Gaji Bulanan.</p>
                </div>
            </div>
            <form class="report-filters salary-transaction-filters" method="GET" action="{{ route('salary.transactions.index') }}">
                <div>
                    <label for="transaction_date_filter">Tanggal</label>
                    <input id="transaction_date_filter" name="date" type="date" value="{{ $date }}">
                </div>
                <div>
                    <label for="transaction_type_filter">Jenis transaksi</label>
                    <select id="transaction_type_filter" name="type">
                        <option value="">Semua jenis</option>
                        <option value="income" @selected($type === 'income')>Pemasukan</option>
                        <option value="expense" @selected($type === 'expense')>Pengeluaran</option>
                    </select>
                </div>
                <button class="btn-main btn-small" type="submit">Tampilkan</button>
            </form>
            @if ($date || $type)
                <a class="text-link" href="{{ route('salary.transactions.index') }}">Hapus filter</a>
            @endif
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
                <p class="pesan-kosong">Belum ada transaksi yang sesuai dengan filter.</p>
            @endforelse
            {{ $transactions->links() }}
        </section>

        <aside class="side-column">
            <section class="card form-card">
                <h2>Pemasukan Tambahan</h2>
                <form method="POST" action="{{ route('salary.income.store') }}">
                    @csrf
                    <label for="income_category">Sumber pemasukan</label>
                    <select id="income_category" name="category" required>
                        <option value="">Pilih sumber</option>
                        @foreach ($incomeCategories as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <label for="income_name">Catatan</label>
                    <input id="income_name" name="name" type="text" value="{{ old('name') }}" maxlength="255" placeholder="Contoh: Bonus akhir tahun" required>
                    <label for="income_amount">Nominal (Rp)</label>
                    <input id="income_amount" name="amount" type="text" inputmode="numeric" data-currency-input autocomplete="off" value="{{ old('amount') }}" placeholder="Contoh: 750.000" required>
                    <label for="income_date">Tanggal pemasukan</label>
                    <input id="income_date" name="transaction_date" type="date" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                    <button class="btn-main" type="submit">Simpan Pemasukan</button>
                </form>
            </section>

            <section class="card form-card">
                <h2>Pengeluaran Harian</h2>
                <form method="POST" action="{{ route('salary.expense.store') }}">
                    @csrf
                    <label for="expense_category">Kategori</label>
                    <select id="expense_category" name="category" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($expenseCategories as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    <label for="expense_name">Catatan singkat</label>
                    <input id="expense_name" name="name" type="text" value="{{ old('name') }}" maxlength="255" placeholder="Contoh: Makan siang" required>
                    <label for="expense_amount">Nominal (Rp)</label>
                    <input id="expense_amount" name="amount" type="text" inputmode="numeric" data-currency-input autocomplete="off" value="{{ old('amount') }}" placeholder="Contoh: 35.000" required>
                    <label for="expense_date">Tanggal pengeluaran</label>
                    <input id="expense_date" name="transaction_date" type="date" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                    <button class="btn-main btn-green" type="submit">Simpan Pengeluaran</button>
                </form>
            </section>
        </aside>
    </div>
@endsection
