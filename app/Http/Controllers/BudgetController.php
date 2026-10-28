<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $totalIncome = $user->transactions()->where('application_mode', 'kos')->where('type', 'income')->sum('amount');
        $totalExpense = $user->transactions()->where('application_mode', 'kos')->where('type', 'expense')->sum('amount');
        $balance = $user->weekly_budget + $totalIncome - $totalExpense;
        $lowBalanceThreshold = (int) floor($user->weekly_budget * $user->low_balance_percent / 100);
        $today = CarbonImmutable::today();
        $nextPayday = $this->nextPayday(
            $user->payday_interval,
            CarbonImmutable::parse($user->payday_anchor_date),
            $today,
            $user->payday_workday_interval,
        );
        $daysUntilPayday = $user->payday_interval === 'workdays'
            ? $this->countWorkdaysUntil($today, $nextPayday)
            : (int) $today->startOfDay()->diff($nextPayday->startOfDay())->days;
        $dailyAllowance = intdiv(max(0, $balance), max(1, $daysUntilPayday));
        $todayExpense = (int) $user->transactions()
            ->where('application_mode', 'kos')
            ->where('type', 'expense')
            ->whereDate('transaction_date', $today->toDateString())
            ->sum('amount');
        $dailyStatus = $todayExpense > $dailyAllowance
            ? 'danger'
            : ($dailyAllowance > 0 && $todayExpense >= $dailyAllowance * 0.8 ? 'warning' : 'safe');

        return view('budget.index', [
            'transactions' => $user->transactions()
                ->where('application_mode', 'kos')
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->limit(6)
                ->get(),
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'lowBalanceThreshold' => $lowBalanceThreshold,
            'isLowBalance' => $user->weekly_budget > 0 && $balance <= $lowBalanceThreshold,
            'nextPayday' => $nextPayday,
            'daysUntilPayday' => $daysUntilPayday,
            'dailyAllowance' => $dailyAllowance,
            'todayExpense' => $todayExpense,
            'dailyAllowanceRemaining' => max(0, $dailyAllowance - $todayExpense),
            'dailyStatus' => $dailyStatus,
        ]);
    }

    public function transactions(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
        ]);
        $date = $validated['date'] ?? null;
        $transactionQuery = $request->user()->transactions()->where('application_mode', 'kos');

        if ($date) {
            $transactionQuery->whereDate('transaction_date', $date);
        }

        return view('budget.transactions', [
            'transactions' => $transactionQuery
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString(),
            'date' => $date,
        ]);
    }

    public function settings(Request $request): View
    {
        $user = $request->user();

        return view('budget.settings', [
            'nextPayday' => $user->application_mode === 'kos'
                && $user->payday_interval
                && $user->payday_anchor_date
                ? $this->nextPayday(
                    $user->payday_interval,
                    CarbonImmutable::parse($user->payday_anchor_date),
                    CarbonImmutable::today(),
                    $user->payday_workday_interval,
                )
                : null,
        ]);
    }

    public function chooseApplicationMode(): View
    {
        return view('budget.choose-application-mode');
    }

    public function updateApplicationMode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application_mode' => ['required', 'in:kos,salary'],
        ]);

        $request->user()->update($validated);

        if ($validated['application_mode'] === 'salary') {
            return redirect()->route('salary.index')->with('status', 'Mode Gaji Bulanan berhasil dipilih.');
        }

        if (! $request->user()->payday_interval || ! $request->user()->payday_anchor_date) {
            return redirect()->route('budget.schedule.setup');
        }

        return redirect()->route('budget.index')->with('status', 'Mode Pencatatan Uang Kos berhasil dipilih.');
    }

    public function setupSchedule(): View
    {
        return view('budget.setup-schedule');
    }

    public function updatePaydaySchedule(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payday_interval' => ['required', 'in:weekly,monthly,bimonthly,workdays'],
            'payday_workday_interval' => ['required_if:payday_interval,workdays', 'nullable', 'integer', 'between:1,90'],
            'next_payday' => ['required', 'date_format:Y-m-d', 'after:today'],
        ]);

        if (
            $validated['payday_interval'] === 'workdays'
            && ! CarbonImmutable::parse($validated['next_payday'])->isWeekday()
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'next_payday' => 'Tanggal kiriman untuk jadwal hari kerja harus jatuh pada hari Senin sampai Jumat.',
            ]);
        }

        $isInitialSetup = ! $request->user()->payday_interval || ! $request->user()->payday_anchor_date;
        $request->user()->update([
            'payday_interval' => $validated['payday_interval'],
            'payday_workday_interval' => $validated['payday_interval'] === 'workdays'
                ? $validated['payday_workday_interval']
                : null,
            'payday_anchor_date' => $validated['next_payday'],
        ]);

        return redirect()->route($isInitialSetup ? 'budget.index' : 'budget.settings')
            ->with('status', 'Jadwal kiriman berhasil disimpan.');
    }

    private function nextPayday(
        string $interval,
        CarbonImmutable $anchor,
        CarbonImmutable $today,
        ?int $workdayInterval = null,
    ): CarbonImmutable
    {
        if ($anchor->greaterThan($today)) {
            return $anchor;
        }

        if ($interval === 'workdays') {
            if (! $workdayInterval || $workdayInterval < 1) {
                throw new \LogicException('Jadwal transfer hari kerja harus memiliki interval yang valid.');
            }

            do {
                $anchor = $this->addWorkdays($anchor, $workdayInterval);
            } while ($anchor->lessThanOrEqualTo($today));

            return $anchor;
        }

        if ($interval === 'weekly') {
            $daysSinceAnchor = (int) $anchor->diffInDays($today);
            $weeksUntilNextPayday = intdiv($daysSinceAnchor, 7) + 1;

            return $anchor->addWeeks($weeksUntilNextPayday);
        }

        $intervalMonths = $interval === 'bimonthly' ? 2 : 1;
        $monthDifference = (($today->year - $anchor->year) * 12) + $today->month - $anchor->month;
        $elapsedPeriods = intdiv(max(0, $monthDifference), $intervalMonths);
        $anchorDay = $anchor->day;
        $candidateMonth = $anchor->startOfMonth()->addMonths($elapsedPeriods * $intervalMonths);
        $candidate = $candidateMonth->setDay(min($anchorDay, $candidateMonth->daysInMonth));

        if ($candidate->lessThanOrEqualTo($today)) {
            $candidateMonth = $candidateMonth->addMonths($intervalMonths);
            $candidate = $candidateMonth->setDay(min($anchorDay, $candidateMonth->daysInMonth));
        }

        return $candidate;
    }

    private function addWorkdays(CarbonImmutable $date, int $workdays): CarbonImmutable
    {
        while ($workdays > 0) {
            $date = $date->addDay();

            if ($date->isWeekday()) {
                $workdays--;
            }
        }

        return $date;
    }

    private function countWorkdaysUntil(CarbonImmutable $today, CarbonImmutable $payday): int
    {
        $workdays = 0;
        $date = $today;

        while ($date->lessThan($payday)) {
            if ($date->isWeekday()) {
                $workdays++;
            }

            $date = $date->addDay();
        }

        return $workdays;
    }

    public function reports(Request $request): View
    {
        $user = $request->user();
        $validated = $request->validate([
            'filter' => ['sometimes', 'in:month,dates'],
            'month' => ['sometimes', 'date_format:Y-m'],
            'start_date' => ['required_if:filter,dates', 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['required_if:filter,dates', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        $filter = $validated['filter'] ?? 'month';
        $month = $validated['month'] ?? now()->format('Y-m');

        if ($filter === 'dates') {
            $start = Carbon::createFromFormat('Y-m-d', $validated['start_date'])->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $validated['end_date'])->endOfDay();
        } else {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end = $start->copy()->endOfMonth();
        }

        $reportQuery = $user->transactions()
            ->where('application_mode', 'kos')
            ->whereDate('transaction_date', '>=', $start->toDateString())
            ->whereDate('transaction_date', '<=', $end->toDateString());
        $rangeDays = (int) $start->diffInDays($end);
        $bucketUnit = $rangeDays > 10950 ? 'year' : ($rangeDays > 90 ? 'month' : 'day');
        $bucketFormat = match ($bucketUnit) {
            'year' => 'Y',
            'month' => 'Y-m',
            default => 'Y-m-d',
        };
        $chartBuckets = [];
        $cursor = $start->copy();
        match ($bucketUnit) {
            'year' => $cursor->startOfYear(),
            'month' => $cursor->startOfMonth(),
            default => $cursor,
        };

        while ($cursor->lte($end) && count($chartBuckets) < 400) {
            $key = $cursor->format($bucketFormat);
            $chartBuckets[$key] = ['income' => 0, 'expense' => 0];
            match ($bucketUnit) {
                'year' => $cursor->addYear(),
                'month' => $cursor->addMonth(),
                default => $cursor->addDay(),
            };
        }

        foreach ((clone $reportQuery)->cursor(['type', 'amount', 'transaction_date']) as $transaction) {
            $key = $transaction->transaction_date->format($bucketFormat);
            if (isset($chartBuckets[$key])) {
                $chartBuckets[$key][$transaction->type] += $transaction->amount;
            }
        }

        $categoryTotals = (clone $reportQuery)
            ->where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $chartLabels = [];
        foreach (array_keys($chartBuckets) as $key) {
            $chartLabels[] = Carbon::createFromFormat($bucketFormat, $key)
                ->locale('id')
                ->translatedFormat(match ($bucketUnit) {
                    'year' => 'Y',
                    'month' => 'M Y',
                    default => 'd M',
                });
        }

        return view('budget.reports', [
            'filter' => $filter,
            'month' => $month,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'periodIncome' => (clone $reportQuery)->where('type', 'income')->sum('amount'),
            'periodExpense' => (clone $reportQuery)->where('type', 'expense')->sum('amount'),
            'transactions' => (clone $reportQuery)
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString(),
            'chartLabels' => $chartLabels,
            'incomeSeries' => array_column($chartBuckets, 'income'),
            'expenseSeries' => array_column($chartBuckets, 'expense'),
            'categoryLabels' => $categoryTotals->keys()->all(),
            'categorySeries' => $categoryTotals->values()->all(),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'filter' => ['sometimes', 'in:month,dates'],
            'month' => ['sometimes', 'date_format:Y-m'],
            'start_date' => ['required_if:filter,dates', 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['required_if:filter,dates', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        $filter = $validated['filter'] ?? 'month';

        if ($filter === 'dates') {
            $start = Carbon::createFromFormat('Y-m-d', $validated['start_date'])->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $validated['end_date'])->endOfDay();
            $fileName = "laporan-keuangan-{$validated['start_date']}-{$validated['end_date']}.csv";
        } else {
            $month = $validated['month'] ?? now()->format('Y-m');
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
            $end = $start->copy()->endOfMonth();
            $fileName = "laporan-keuangan-{$month}.csv";
        }

        $transactions = $request->user()->transactions()
            ->where('application_mode', 'kos')
            ->whereDate('transaction_date', '>=', $start->toDateString())
            ->whereDate('transaction_date', '<=', $end->toDateString())
            ->orderBy('transaction_date')
            ->orderBy('id');

        return response()->streamDownload(function () use ($transactions): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('Tidak dapat membuat file CSV laporan.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Tanggal', 'Jenis', 'Kategori / Sumber', 'Keterangan', 'Nominal (Rp)'], ',', '"', '');

            foreach ($transactions->cursor() as $transaction) {
                fputcsv($output, [
                    $transaction->transaction_date?->format('Y-m-d') ?? '',
                    $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    $this->safeCsvCell($transaction->category),
                    $this->safeCsvCell($transaction->name),
                    $transaction->amount,
                ], ',', '"', '');
            }

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function safeCsvCell(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/u', $value) === 1 ? "'".$value : $value;
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $amount = $this->validateRupiah($request, 'amount');
        $validated = $request->validate([
            'category' => ['required', 'in:Makan,Kebutuhan Kos,Transport,Nongkrong,Lainnya'],
            'other_name' => ['nullable', 'required_if:category,Lainnya', 'string', 'max:255'],
            'transaction_date' => ['required', 'date_format:Y-m-d'],
        ]);

        $request->user()->transactions()->create([
            'name' => $validated['category'] === 'Lainnya' ? $validated['other_name'] : $validated['category'],
            'amount' => $amount,
            'category' => $validated['category'],
            'transaction_date' => $validated['transaction_date'],
            'type' => 'expense',
        ]);

        return redirect()->route('transactions.index')->with('status', 'Pengeluaran berhasil dicatat.');
    }

    public function storeIncome(Request $request): RedirectResponse
    {
        $amount = $this->validateRupiah($request, 'amount');
        $validated = $request->validate([
            'source' => ['required', 'in:Ayah,Ibu,Kakak'],
            'name' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date_format:Y-m-d'],
        ]);

        $request->user()->transactions()->create([
            'name' => $validated['name'],
            'amount' => $amount,
            'category' => $validated['source'],
            'transaction_date' => $validated['transaction_date'],
            'type' => 'income',
        ]);

        return redirect()->route('transactions.index')->with('status', 'Saldo masuk berhasil dicatat.');
    }

    public function updateBudget(Request $request): RedirectResponse
    {
        $weeklyBudget = $this->validateRupiah($request, 'weekly_budget', 0);

        $request->user()->update(['weekly_budget' => $weeklyBudget]);

        return redirect()->route('budget.settings')->with('status', 'Target uang saku berhasil diperbarui.');
    }

    private function validateRupiah(Request $request, string $field, int $minimum = 1): int
    {
        $validated = $request->validate([
            $field => ['required', 'string', 'regex:/\A(?:\d+|\d{1,3}(?:\.\d{3})+)\z/'],
        ]);

        $request->merge([$field => str_replace('.', '', $validated[$field])]);

        return (int) $request->validate([
            $field => ['required', 'integer', "min:$minimum"],
        ])[$field];
    }

    public function destroy(Request $request, Transaction $transaction): RedirectResponse
    {
        $request->user()->transactions()
            ->where('application_mode', 'kos')
            ->findOrFail($transaction->getKey())
            ->delete();

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil dihapus.');
    }
}
