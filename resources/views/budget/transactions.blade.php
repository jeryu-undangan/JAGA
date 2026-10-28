@extends('layouts.app')

@section('title', 'Pencatatan Transaksi - UangKos')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Pencatatan Transaksi</h1>
            <p>Catat pemasukan dan pengeluaran uang kos.</p>
        </div>
        <div class="page-heading-actions">
            <form class="date-filter" method="GET" action="{{ route('transactions.index') }}">
                <label for="history_date">Tanggal riwayat</label>
                <input id="history_date" name="date" type="date" value="{{ $date }}">
                <button class="button btn-main" type="submit">Cari</button>
            </form>
            @if ($date)
                <a class="button btn-quiet" href="{{ route('transactions.index') }}">Semua tanggal</a>
            @endif
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
        <div class="main-column">
            <section class="card">
                <div class="section-heading">
                    <div>
                        <h2>Riwayat Transaksi</h2>
                        <p>{{ $date ? 'Transaksi tanggal '.\Carbon\Carbon::parse($date)->format('d/m/Y') : 'Semua catatan keuanganmu' }}</p>
                    </div>
                </div>
                @forelse ($transactions as $transaction)
                    <article class="item-transaksi">
                        <div>
                            <span class="transaction-name">{{ $transaction->name }}</span>
                            <small class="transaction-meta">{{ $transaction->category }} · {{ $transaction->transaction_date?->format(auth()->user()->date_format ?? 'd/m/Y') ?? $transaction->created_at->format(auth()->user()->date_format ?? 'd/m/Y') }}</small>
                        </div>
                        <div class="transaction-actions">
                            <span class="{{ $transaction->type === 'income' ? 'pemasukan-text' : 'pengeluaran-text' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </span>
                            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Hapus catatan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-delete" type="submit" aria-label="Hapus transaksi {{ $transaction->name }}">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="pesan-kosong">{{ $date ? 'Tidak ada transaksi pada tanggal ini.' : 'Belum ada catatan transaksi.' }}</p>
                @endforelse
                {{ $transactions->links() }}
            </section>
        </div>

        <aside class="side-column">
            <section class="card form-card">
                <h3>+ Tambah Saldo Masuk</h3>
                <form method="POST" action="{{ route('transactions.income') }}">
                    @csrf
                    <label for="income_source">Pemasukan dari</label>
                    <select id="income_source" name="source" required>
                        <option value="">Pilih sumber pemasukan</option>
                        <option value="Ayah" @selected(old('source') === 'Ayah')>Ayah</option>
                        <option value="Ibu" @selected(old('source') === 'Ibu')>Ibu</option>
                        <option value="Kakak" @selected(old('source') === 'Kakak')>Kakak</option>
                    </select>
                    <label for="income_name">Keterangan, uangnya untuk apa?</label>
                    <input id="income_name" name="name" type="text" list="incomePurposes" value="{{ old('name') }}" placeholder="Contoh: Uang saku bulan ini" required>
                    <datalist id="incomePurposes">
                        <option value="Uang saku"></option>
                        <option value="Bayar kos"></option>
                        <option value="Biaya makan"></option>
                        <option value="Transportasi"></option>
                        <option value="Kebutuhan kuliah"></option>
                        <option value="Keperluan darurat"></option>
                    </datalist>
                    <label for="income_date">Tanggal pemasukan</label>
                    <input id="income_date" name="transaction_date" type="date" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                    <label for="income_amount">Nominal (Rp)</label>
                    <input id="income_amount" name="amount" type="text" inputmode="numeric" data-currency-input autocomplete="off" value="{{ old('amount') }}" placeholder="Contoh: 500.000" required>
                    <button class="btn-main" type="submit">Simpan Saldo Masuk</button>
                </form>
            </section>

            <section class="card form-card">
                <h3>+ Catat Pengeluaran</h3>
                <form method="POST" action="{{ route('transactions.expense') }}">
                    @csrf
                    <label for="category">Kategori</label>
                    <select id="category" name="category" required>
                        <option value="Makan" @selected(old('category') === 'Makan')>🍛 Makan &amp; Minum</option>
                        <option value="Kebutuhan Kos" @selected(old('category') === 'Kebutuhan Kos')>🏠 Kebutuhan Kos</option>
                        <option value="Transport" @selected(old('category') === 'Transport')>🛵 Transportasi</option>
                        <option value="Nongkrong" @selected(old('category') === 'Nongkrong')>☕ Nongkrong / Hiburan</option>
                        <option value="Lainnya" @selected(old('category') === 'Lainnya')>📦 Lainnya</option>
                    </select>
                    <div id="otherExpenseField" @if (old('category') !== 'Lainnya') hidden @endif>
                        <label for="other_name">Nama pengeluaran lainnya</label>
                        <input id="other_name" name="other_name" type="text" value="{{ old('other_name') }}" placeholder="Contoh: Obat atau alat tulis" @required(old('category') === 'Lainnya')>
                    </div>
                    <label for="expense_date">Tanggal pengeluaran</label>
                    <input id="expense_date" name="transaction_date" type="date" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                    <label for="expense_amount">Nominal (Rp)</label>
                    <input id="expense_amount" name="amount" type="text" inputmode="numeric" data-currency-input autocomplete="off" value="{{ old('amount') }}" placeholder="Contoh: 15.000" required>
                    <button class="btn-main btn-green" type="submit">Simpan Catatan</button>
                </form>
            </section>
        </aside>
    </div>

    <script nonce="{{ request()->attributes->get('cspNonce') }}">
        const categoryInput = document.getElementById('category');
        const otherExpenseField = document.getElementById('otherExpenseField');
        const otherNameInput = document.getElementById('other_name');

        function toggleOtherExpenseField() {
            const isOther = categoryInput.value === 'Lainnya';
            otherExpenseField.hidden = !isOther;
            otherNameInput.required = isOther;
        }

        categoryInput.addEventListener('change', toggleOtherExpenseField);
        toggleOtherExpenseField();
    </script>
@endsection
