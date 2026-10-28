<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SalaryController extends Controller
{
    private const EXPENSE_CATEGORIES = [
        'Makanan',
        'Tagihan',
        'Transportasi',
        'Hiburan',
        'Tabungan',
        'Lainnya',
    ];

    private const INCOME_CATEGORIES = [
        'Bonus',
        'THR',
        'Freelance',
        'Lainnya',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();
        $month = now()->format('Y-m');
        $monthTransactions = $user->transactions()
            ->where('application_mode', 'salary')
            ->whereBetween('transaction_date', [
                now()->copy()->startOfMonth()->toDateString(),
                now()->copy()->endOfMonth()->toDateString(),
            ]);
        $monthlyIncome = $user->base_salary
            + (clone $monthTransactions)->where('type', 'income')->sum('amount');
        $monthlyExpense = (int) (clone $monthTransactions)->where('type', 'expense')->sum('amount');
        $categoryExpenses = (clone $monthTransactions)
            ->where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');
        $budgets = $user->salaryBudgets()
            ->where('budget_month', $month)
            ->get()
            ->keyBy('category');

        $budgetStatuses = collect(self::EXPENSE_CATEGORIES)->map(function (string $category) use ($categoryExpenses, $budgets): array {
            $spent = (int) $categoryExpenses->get($category, 0);
            $budget = $budgets->get($category);
            $limit = $budget?->amount;
            $percent = $limit ? (int) min(100, floor(($spent / $limit) * 100)) : 0;

            return [
                'category' => $category,
                'spent' => $spent,
                'limit' => $limit,
                'percent' => $percent,
                'status' => $limit && $spent >= $limit ? 'danger' : ($limit && $spent >= $limit * 0.8 ? 'warning' : 'safe'),
            ];
        });

        $totalCategoryExpense = max(1, $categoryExpenses->sum());
        $chartCategories = $categoryExpenses->map(fn ($amount, $category): array => [
            'category' => $category,
            'amount' => (int) $amount,
            'percent' => (int) round(($amount / $totalCategoryExpense) * 100),
        ])->values();

        return view('budget.salary', [
            'month' => now()->locale('id')->translatedFormat('F Y'),
            'monthlyIncome' => $monthlyIncome,
            'monthlyExpense' => $monthlyExpense,
            'availableBalance' => $monthlyIncome - $monthlyExpense,
            'transactions' => (clone $monthTransactions)
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->limit(10)
                ->get(),
            'budgetStatuses' => $budgetStatuses,
            'chartCategories' => $chartCategories,
            'expenseCategories' => self::EXPENSE_CATEGORIES,
            'nextPayday' => $this->nextPayday($user->salary_payday_day),
        ]);
    }

    public function transactions(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'type' => ['sometimes', 'nullable', 'in:income,expense'],
        ]);
        $query = $request->user()->transactions()->where('application_mode', 'salary');

        if (! empty($validated['date'])) {
            $query->whereDate('transaction_date', $validated['date']);
        }

        if (! empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        return view('budget.salary-transactions', [
            'transactions' => $query
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString(),
            'date' => $validated['date'] ?? null,
            'type' => $validated['type'] ?? null,
            'expenseCategories' => self::EXPENSE_CATEGORIES,
            'incomeCategories' => self::INCOME_CATEGORIES,
        ]);
    }

    public function reports(Request $request): View
    {
        $validated = $request->validate([
            'filter' => ['sometimes', 'in:month,dates'],
            'month' => ['sometimes', 'date_format:Y-m'],
            'start_date' => ['required_if:filter,dates', 'nullable', 'date_format:Y-m-d'],
            'end_date' => ['required_if:filter,dates', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        $filter = $validated['filter'] ?? 'month';

        if ($filter === 'dates') {
            $start = CarbonImmutable::createFromFormat('Y-m-d', $validated['start_date'])->startOfDay();
            $end = CarbonImmutable::createFromFormat('Y-m-d', $validated['end_date'])->endOfDay();
            $month = $start->format('Y-m');
        } else {
            $month = $validated['month'] ?? now()->format('Y-m');
            $start = CarbonImmutable::createFromFormat('Y-m', $month)->startOfMonth();
            $end = $start->endOfMonth();
        }

        $transactions = $request->user()->transactions()
            ->where('application_mode', 'salary')
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()]);
        $rangeDays = $start->diffInDays($end);
        $bucketUnit = $rangeDays > 10950 ? 'year' : ($rangeDays > 90 ? 'month' : 'day');
        $bucketFormat = match ($bucketUnit) {
            'year' => 'Y',
            'month' => 'Y-m',
            default => 'Y-m-d',
        };
        $cursor = match ($bucketUnit) {
            'year' => $start->startOfYear(),
            'month' => $start->startOfMonth(),
            default => $start,
        };
        $buckets = [];

        while ($cursor->lessThanOrEqualTo($end) && count($buckets) < 400) {
            $buckets[$cursor->format($bucketFormat)] = ['income' => 0, 'expense' => 0];
            $cursor = match ($bucketUnit) {
                'year' => $cursor->addYear(),
                'month' => $cursor->addMonth(),
                default => $cursor->addDay(),
            };
        }

        foreach ((clone $transactions)->cursor(['type', 'amount', 'transaction_date']) as $transaction) {
            $key = $transaction->transaction_date->format($bucketFormat);
            if (isset($buckets[$key])) {
                $buckets[$key][$transaction->type] += $transaction->amount;
            }
        }

        $baseSalary = (int) $request->user()->base_salary;
        $salaryPaydayDay = $request->user()->salary_payday_day;
        $baseSalaryCount = 0;
        $salaryEntries = [];

        if ($baseSalary > 0 && $salaryPaydayDay) {
            $payMonth = $start->startOfMonth();
            $lastMonth = $end->startOfMonth();

            while ($payMonth->lessThanOrEqualTo($lastMonth)) {
                $payday = $payMonth->setDay(min($salaryPaydayDay, $payMonth->daysInMonth));
                if ($payday->betweenIncluded($start, $end)) {
                    $baseSalaryCount++;
                    $salaryEntries[] = ['date' => $payday, 'amount' => $baseSalary];
                    $key = $payday->format($bucketFormat);
                    if (isset($buckets[$key])) {
                        $buckets[$key]['income'] += $baseSalary;
                    }
                }
                $payMonth = $payMonth->addMonth();
            }
        }

        $categoryTotals = (clone $transactions)
            ->where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');
        $chartLabels = [];
        foreach (array_keys($buckets) as $key) {
            $chartLabels[] = Carbon::createFromFormat($bucketFormat, $key)
                ->locale('id')
                ->translatedFormat(match ($bucketUnit) {
                    'year' => 'Y',
                    'month' => 'M Y',
                    default => 'd M',
                });
        }

        return view('budget.salary-reports', [
            'filter' => $filter,
            'month' => $month,
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'periodIncome' => (int) (clone $transactions)->where('type', 'income')->sum('amount') + ($baseSalary * $baseSalaryCount),
            'periodExpense' => (int) (clone $transactions)->where('type', 'expense')->sum('amount'),
            'transactions' => (clone $transactions)
                ->orderByDesc('transaction_date')
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString(),
            'salaryEntries' => $salaryEntries,
            'chartLabels' => $chartLabels,
            'incomeSeries' => array_column($buckets, 'income'),
            'expenseSeries' => array_column($buckets, 'expense'),
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
            $start = CarbonImmutable::createFromFormat('Y-m-d', $validated['start_date']);
            $end = CarbonImmutable::createFromFormat('Y-m-d', $validated['end_date']);
            $fileName = "laporan-gaji-{$validated['start_date']}-{$validated['end_date']}.csv";
        } else {
            $month = $validated['month'] ?? now()->format('Y-m');
            $start = CarbonImmutable::createFromFormat('Y-m', $month)->startOfMonth();
            $end = $start->endOfMonth();
            $fileName = "laporan-gaji-{$month}.csv";
        }

        $transactions = $request->user()->transactions()
            ->where('application_mode', 'salary')
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('transaction_date')
            ->orderBy('id');
        $salary = (int) $request->user()->base_salary;
        $paydayDay = $request->user()->salary_payday_day;

        return response()->streamDownload(function () use ($transactions, $salary, $paydayDay, $start, $end): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('Tidak dapat membuat file CSV laporan gaji.');
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

            if ($salary > 0 && $paydayDay) {
                $payMonth = $start->startOfMonth();
                $lastMonth = $end->startOfMonth();
                while ($payMonth->lessThanOrEqualTo($lastMonth)) {
                    $payday = $payMonth->setDay(min($paydayDay, $payMonth->daysInMonth));
                    if ($payday->betweenIncluded($start, $end)) {
                        fputcsv($output, [
                            $payday->toDateString(),
                            'Pemasukan',
                            'Gaji pokok',
                            'Gaji pokok bulanan',
                            $salary,
                        ], ',', '"', '');
                    }
                    $payMonth = $payMonth->addMonth();
                }
            }

            fclose($output);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'base_salary' => ['required', 'string', 'regex:/\A(?:\d+|\d{1,3}(?:\.\d{3})+)\z/'],
            'salary_payday_day' => ['required', 'integer', 'between:1,31'],
        ]);
        $baseSalary = (int) str_replace('.', '', $validated['base_salary']);

        if ($baseSalary < 1 || $baseSalary > 999999999999) {
            throw ValidationException::withMessages([
                'base_salary' => 'Nominal gaji pokok harus lebih dari 0 dan tidak melebihi Rp 999.999.999.999.',
            ]);
        }

        $request->user()->update([
            'base_salary' => $baseSalary,
            'salary_payday_day' => $validated['salary_payday_day'],
        ]);

        return redirect()->route('salary.index')->with('status', 'Informasi gaji pokok berhasil disimpan.');
    }

    public function storeIncome(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'string', 'regex:/\A(?:\d+|\d{1,3}(?:\.\d{3})+)\z/'],
            'category' => ['required', 'in:'.implode(',', self::INCOME_CATEGORIES)],
            'name' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date_format:Y-m-d'],
        ]);

        $this->createTransaction($request, $validated, 'income');

        return redirect()->route('salary.index')->with('status', 'Pemasukan tambahan berhasil dicatat.');
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'string', 'regex:/\A(?:\d+|\d{1,3}(?:\.\d{3})+)\z/'],
            'category' => ['required', 'in:'.implode(',', self::EXPENSE_CATEGORIES)],
            'name' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date_format:Y-m-d'],
        ]);

        $this->createTransaction($request, $validated, 'expense');

        return redirect()->route('salary.index')->with('status', 'Pengeluaran berhasil dicatat.');
    }

    public function storeBudget(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'in:'.implode(',', self::EXPENSE_CATEGORIES)],
            'amount' => ['required', 'string', 'regex:/\A(?:\d+|\d{1,3}(?:\.\d{3})+)\z/'],
        ]);
        $amount = (int) str_replace('.', '', $validated['amount']);

        if ($amount < 1 || $amount > 999999999999) {
            throw ValidationException::withMessages([
                'amount' => 'Batas anggaran harus lebih dari 0 dan tidak melebihi Rp 999.999.999.999.',
            ]);
        }

        $request->user()->salaryBudgets()->updateOrCreate(
            [
                'budget_month' => now()->format('Y-m'),
                'category' => $validated['category'],
            ],
            ['amount' => $amount],
        );

        return redirect()->route('salary.index')->with('status', 'Anggaran kategori berhasil disimpan.');
    }

    public function destroyTransaction(Request $request, Transaction $transaction): RedirectResponse
    {
        $request->user()->transactions()
            ->where('application_mode', 'salary')
            ->findOrFail($transaction->getKey())
            ->delete();

        return redirect()->route('salary.index')->with('status', 'Transaksi berhasil dihapus.');
    }

    private function createTransaction(Request $request, array $validated, string $type): void
    {
        $amount = (int) str_replace('.', '', $validated['amount']);

        if ($amount < 1 || $amount > 999999999999) {
            throw ValidationException::withMessages([
                'amount' => 'Nominal harus lebih dari 0 dan tidak melebihi Rp 999.999.999.999.',
            ]);
        }

        $request->user()->transactions()->create([
            'application_mode' => 'salary',
            'type' => $type,
            'name' => $validated['name'],
            'amount' => $amount,
            'category' => $validated['category'],
            'transaction_date' => $validated['transaction_date'],
        ]);
    }

    private function safeCsvCell(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/u', $value) === 1 ? "'".$value : $value;
    }

    private function nextPayday(?int $paydayDay): ?CarbonImmutable
    {
        if (! $paydayDay) {
            return null;
        }

        $today = CarbonImmutable::today();
        $payday = $today->startOfMonth()->setDay(min($paydayDay, $today->daysInMonth));

        if ($payday->lessThan($today)) {
            $nextMonth = $today->addMonth()->startOfMonth();
            $payday = $nextMonth->setDay(min($paydayDay, $nextMonth->daysInMonth));
        }

        return $payday;
    }
}
