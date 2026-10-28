<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalaryFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_salary_dashboard_saves_payroll_details_income_expenses_and_monthly_category_budgets(): void
    {
        $user = User::factory()->create(['application_mode' => 'salary']);
        $today = now()->toDateString();

        $this->actingAs($user)
            ->get(route('salary.index'))
            ->assertOk()
            ->assertSee('Dashboard Gaji Bulanan')
            ->assertSee('Gaji Pokok')
            ->assertSee('Pengeluaran per Kategori')
            ->assertSee('salary-mode', false)
            ->assertSee('#059669', false)
            ->assertSee('#EF4444', false)
            ->assertSee('#F4F7F5', false)
            ->assertSee('salary-balance-card', false)
            ->assertSee('Sisa saldo bulan ini')
            ->assertSee('salary-income-card', false)
            ->assertSee('salary-expense-card', false)
            ->assertSee('Navigasi utama')
            ->assertSee('Beranda')
            ->assertSee('Transaksi')
            ->assertSee('Laporan')
            ->assertSee('Profil')
            ->assertDontSee('Kelola transaksi')
            ->assertDontSee('<div class="page-heading-actions">', false);
        $this->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Ubah Kata Sandi')
            ->assertDontSee('Reset Riwayat Transaksi');

        $this->put(route('salary.profile.update'), [
            'base_salary' => '5.000.000',
            'salary_payday_day' => 25,
        ])->assertRedirect(route('salary.index'));

        $this->post(route('salary.income.store'), [
            'amount' => '500.000',
            'category' => 'Freelance',
            'name' => 'Proyek desain',
            'transaction_date' => $today,
        ])->assertRedirect(route('salary.index'));

        $this->post(route('salary.expense.store'), [
            'amount' => '45.000',
            'category' => 'Makanan',
            'name' => 'Makan siang',
            'transaction_date' => $today,
        ])->assertRedirect(route('salary.index'));

        $this->post(route('salary.budgets.store'), [
            'category' => 'Makanan',
            'amount' => '50.000',
        ])->assertRedirect(route('salary.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'base_salary' => 5000000,
            'salary_payday_day' => 25,
        ]);
        $this->assertDatabaseHas('salary_budgets', [
            'user_id' => $user->id,
            'budget_month' => now()->format('Y-m'),
            'category' => 'Makanan',
            'amount' => 50000,
        ]);

        $this->get(route('salary.index'))
            ->assertOk()
            ->assertSee('Rp 5.500.000')
            ->assertSee('Rp 45.000')
            ->assertSee('Proyek desain')
            ->assertSee('Makan siang')
            ->assertSee('Anggaran hampir habis.')
            ->assertSee('Gajian berikutnya');

        $this->get(route('salary.transactions.index'))
            ->assertOk()
            ->assertSee('Riwayat Transaksi Gaji')
            ->assertSee('Proyek desain')
            ->assertSee('Pemasukan Tambahan')
            ->assertSee('Pengeluaran Harian');
        $this->get(route('salary.reports.index', ['month' => now()->format('Y-m')]))
            ->assertOk()
            ->assertSee('Laporan & Grafik Gaji')
            ->assertSee('Grafik Arus Kas')
            ->assertSee('Pengeluaran per Kategori')
            ->assertSee('Proyek desain')
            ->assertSee('Gaji pokok bulanan');

        $this->get(route('salary.reports.export', ['month' => now()->format('Y-m')]))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_salary_and_kos_transactions_remain_separate_when_switching_modes(): void
    {
        $user = User::factory()->create(['application_mode' => 'salary']);
        $user->transactions()->create([
            'application_mode' => 'kos',
            'type' => 'income',
            'name' => 'Uang kos lama',
            'amount' => 900000,
            'category' => 'Kiriman',
            'transaction_date' => now()->toDateString(),
        ]);
        $salaryTransaction = $user->transactions()->create([
            'application_mode' => 'salary',
            'type' => 'expense',
            'name' => 'Belanja gaji',
            'amount' => 15000,
            'category' => 'Makanan',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('salary.index'))
            ->assertOk()
            ->assertSee('Belanja gaji')
            ->assertDontSee('Uang kos lama');
        $this->get(route('salary.transactions.index'))
            ->assertOk()
            ->assertSee('Belanja gaji')
            ->assertDontSee('Uang kos lama');

        $this->put(route('application-mode.update'), ['application_mode' => 'kos'])
            ->assertRedirect(route('budget.index'));

        $this->get(route('budget.index'))
            ->assertOk()
            ->assertSee('Rp 900.000')
            ->assertDontSee('Belanja gaji');

        $this->delete(route('salary.transactions.destroy', $salaryTransaction))
            ->assertRedirect(route('budget.index'));
        $this->assertDatabaseHas('transactions', ['id' => $salaryTransaction->id]);
    }

    public function test_salary_transactions_are_user_scoped_and_salary_routes_are_mode_restricted(): void
    {
        $user = User::factory()->create(['application_mode' => 'salary']);
        $otherUser = User::factory()->create(['application_mode' => 'salary']);
        $otherTransaction = $otherUser->transactions()->create([
            'application_mode' => 'salary',
            'type' => 'expense',
            'name' => 'Private expense',
            'amount' => 10000,
            'category' => 'Hiburan',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->delete(route('salary.transactions.destroy', $otherTransaction))
            ->assertNotFound();

        $kosUser = User::factory()->create(['application_mode' => 'kos']);
        $this->actingAs($kosUser)
            ->get(route('salary.index'))
            ->assertRedirect(route('budget.index'));
    }

    public function test_salary_forms_validate_categories_and_amounts(): void
    {
        $user = User::factory()->create(['application_mode' => 'salary']);

        $this->actingAs($user)
            ->post(route('salary.expense.store'), [
                'amount' => 'abc',
                'category' => 'Unrecognized',
                'name' => 'Test',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors(['amount', 'category']);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_salary_reports_filter_transactions_to_salary_mode_and_selected_dates(): void
    {
        $user = User::factory()->create([
            'application_mode' => 'salary',
            'base_salary' => 4000000,
            'salary_payday_day' => 15,
        ]);
        $user->transactions()->create([
            'application_mode' => 'salary',
            'type' => 'expense',
            'name' => 'Dalam rentang',
            'amount' => 70000,
            'category' => 'Tagihan',
            'transaction_date' => now()->startOfMonth()->addDays(9)->toDateString(),
        ]);
        $user->transactions()->create([
            'application_mode' => 'kos',
            'type' => 'expense',
            'name' => 'Tidak boleh masuk laporan gaji',
            'amount' => 90000,
            'category' => 'Makan',
            'transaction_date' => now()->toDateString(),
        ]);

        $start = now()->startOfMonth()->toDateString();
        $end = now()->endOfMonth()->toDateString();

        $this->actingAs($user)
            ->get(route('salary.reports.index', [
                'filter' => 'dates',
                'start_date' => $start,
                'end_date' => $end,
            ]))
            ->assertOk()
            ->assertSee('Dalam rentang')
            ->assertDontSee('Tidak boleh masuk laporan gaji')
            ->assertSee('Rp 4.000.000')
            ->assertSee('Rp 70.000');
    }
}
