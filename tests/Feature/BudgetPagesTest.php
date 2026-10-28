<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BudgetPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_restrict_scripts_and_prevent_embedding(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('budget.index'));

        $response->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'no-referrer');

        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/<script nonce="([^"]+)">/', $content);
        preg_match('/<script nonce="([^"]+)">/', $content, $matches);

        $contentSecurityPolicy = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("'nonce-{$matches[1]}'", $contentSecurityPolicy);
        $this->assertStringContainsString("frame-ancestors 'none'", $contentSecurityPolicy);
        $this->assertStringNotContainsString("script-src 'self' 'unsafe-inline'", $contentSecurityPolicy);
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('login.store'), [
                'email' => 'unknown@example.test',
                'password' => 'invalid-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'email' => 'unknown@example.test',
            'password' => 'invalid-password',
        ])->assertTooManyRequests();
    }

    public function test_dashboard_only_shows_financial_summary_and_recent_transactions(): void
    {
        $user = User::factory()->create(['weekly_budget' => 300000]);
        $user->transactions()->create([
            'type' => 'income',
            'name' => 'Kiriman bulanan',
            'amount' => 500000,
            'category' => 'Saldo Masuk',
            'transaction_date' => now()->toDateString(),
        ]);
        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Makan siang',
            'amount' => 25000,
            'category' => 'Makan',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('budget.index'))
            ->assertOk()
            ->assertSee('Dashboard Keuangan')
            ->assertSee('Sisa Uang Saku')
            ->assertSee('Total Pemasukan')
            ->assertSee('Total Pengeluaran')
            ->assertSee('Makan siang')
            ->assertDontSee('Grafik Arus Kas')
            ->assertDontSee('Tambah Saldo Masuk')
            ->assertDontSee('Catat Pengeluaran');
    }

    public function test_newly_authenticated_user_must_set_payday_once_before_using_the_app(): void
    {
        $user = User::factory()->create([
            'payday_interval' => null,
            'payday_anchor_date' => null,
        ]);

        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertRedirect(route('budget.schedule.setup'));

        $this->get(route('budget.schedule.setup'))
            ->assertOk()
            ->assertSee('Atur Jadwal Kiriman')
            ->assertSee('Setiap minggu')
            ->assertSee('Setiap 2 bulan')
            ->assertSee('Setiap beberapa hari kerja')
            ->assertSee('payday_workday_interval', false)
            ->assertSee('Simpan dan Lanjutkan');

        $nextPayday = now()->next(Carbon::FRIDAY)->toDateString();
        $this->put(route('budget.schedule.setup.update'), [
            'payday_interval' => 'workdays',
            'payday_workday_interval' => 5,
            'next_payday' => $nextPayday,
        ])
            ->assertRedirect(route('budget.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'payday_interval' => 'workdays',
            'payday_workday_interval' => 5,
        ]);
        $this->assertSame($nextPayday, $user->fresh()->payday_anchor_date->toDateString());

        $this->get(route('budget.index'))
            ->assertOk()
            ->assertSee('Batas Aman Pengeluaran');
    }

    public function test_payday_schedule_can_be_updated_to_every_two_months_in_budget_settings(): void
    {
        $user = User::factory()->create([
            'payday_interval' => 'monthly',
            'payday_anchor_date' => now()->addDays(10)->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('budget.settings'))
            ->assertOk()
            ->assertSee('Jadwal Kiriman Uang Saku')
            ->assertSee('name="payday_interval"', false)
            ->assertSee('name="payday_workday_interval"', false)
            ->assertSee('name="next_payday"', false);

        $nextPayday = now()->addDays(20)->toDateString();
        $this->put(route('budget.schedule.setup.update'), [
            'payday_interval' => 'bimonthly',
            'next_payday' => $nextPayday,
        ])
            ->assertRedirect(route('budget.settings'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'payday_interval' => 'bimonthly',
        ]);
        $this->assertSame($nextPayday, $user->fresh()->payday_anchor_date->toDateString());
    }

    public function test_dashboard_shows_safe_daily_allowance_and_countdown(): void
    {
        Carbon::setTestNow('2026-10-22 12:00:00');
        try {
            $user = User::factory()->create([
                'weekly_budget' => 300000,
                'payday_interval' => 'monthly',
                'payday_anchor_date' => '2026-10-01',
            ]);

            $this->actingAs($user)
                ->get(route('budget.index'))
                ->assertOk()
                ->assertSee('10 hari lagi')
                ->assertSee('Rp 30.000')
                ->assertSee('Setiap bulan')
                ->assertSee('Masih aman');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_daily_budget_indicator_warns_and_turns_red_when_daily_limit_is_exceeded(): void
    {
        Carbon::setTestNow('2026-10-22 12:00:00');
        try {
            $user = User::factory()->create([
                'weekly_budget' => 300000,
                'payday_interval' => 'monthly',
                'payday_anchor_date' => '2026-10-01',
            ]);
            $user->transactions()->create([
                'type' => 'expense',
                'name' => 'Belanja hari ini',
                'amount' => 25000,
                'category' => 'Makan',
                'transaction_date' => '2026-10-22',
            ]);

            $this->actingAs($user)
                ->get(route('budget.index'))
                ->assertOk()
                ->assertSee('Hampir mencapai batas');

            $user->transactions()->create([
                'type' => 'expense',
                'name' => 'Belanja tambahan',
                'amount' => 5000,
                'category' => 'Makan',
                'transaction_date' => '2026-10-22',
            ]);

            $this->get(route('budget.index'))
                ->assertOk()
                ->assertSee('Batas terlampaui');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_payday_countdown_handles_a_day_missing_from_the_next_month(): void
    {
        Carbon::setTestNow('2026-01-31 12:00:00');
        try {
            $user = User::factory()->create([
                'weekly_budget' => 280000,
                'payday_interval' => 'monthly',
                'payday_anchor_date' => '2026-01-31',
            ]);

            $this->actingAs($user)
                ->get(route('budget.index'))
                ->assertOk()
                ->assertSee('28 hari lagi')
                ->assertSee('28/02/2026');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_weekly_schedule_counts_down_to_the_next_weekly_payment(): void
    {
        Carbon::setTestNow('2026-10-22 12:00:00');
        try {
            $user = User::factory()->create([
                'weekly_budget' => 70000,
                'payday_interval' => 'weekly',
                'payday_anchor_date' => '2026-10-19',
            ]);

            $this->actingAs($user)
                ->get(route('budget.index'))
                ->assertOk()
                ->assertSee('4 hari lagi')
                ->assertSee('Setiap minggu')
                ->assertSee('Rp 70.000');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_five_workday_schedule_skips_saturday_and_sunday(): void
    {
        Carbon::setTestNow('2026-10-23 12:00:00');
        try {
            $user = User::factory()->create([
                'weekly_budget' => 500000,
                'payday_interval' => 'workdays',
                'payday_workday_interval' => 5,
                'payday_anchor_date' => '2026-10-23',
            ]);

            $this->actingAs($user)
                ->get(route('budget.index'))
                ->assertOk()
                ->assertSee('5 hari kerja lagi')
                ->assertSee('30/10/2026')
                ->assertSee('Setiap 5 hari kerja')
                ->assertSee('Rp 100.000')
                ->assertSee('sisa hari kerja');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_two_month_schedule_keeps_its_anchor_when_day_is_missing_from_month(): void
    {
        Carbon::setTestNow('2026-02-01 12:00:00');
        try {
            $user = User::factory()->create([
                'weekly_budget' => 620000,
                'payday_interval' => 'bimonthly',
                'payday_anchor_date' => '2026-01-31',
            ]);

            $this->actingAs($user)
                ->get(route('budget.index'))
                ->assertOk()
                ->assertSee('58 hari lagi')
                ->assertSee('31/03/2026')
                ->assertSee('Setiap 2 bulan');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_schedule_rejects_invalid_interval_and_non_future_payday(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('budget.schedule.setup.update'), [
                'payday_interval' => 'yearly',
                'next_payday' => now()->toDateString(),
            ])
            ->assertSessionHasErrors(['payday_interval', 'next_payday']);
    }

    public function test_workday_schedule_requires_a_valid_interval_and_weekday_transfer_date(): void
    {
        Carbon::setTestNow('2026-10-22 12:00:00');
        try {
            $user = User::factory()->create();

            $this->actingAs($user)
                ->put(route('budget.schedule.setup.update'), [
                    'payday_interval' => 'workdays',
                    'payday_workday_interval' => 0,
                    'next_payday' => '2026-10-24',
                ])
                    ->assertSessionHasErrors('payday_workday_interval');

            $this->put(route('budget.schedule.setup.update'), [
                'payday_interval' => 'workdays',
                'payday_workday_interval' => 5,
                'next_payday' => '2026-10-24',
            ])->assertSessionHasErrors('next_payday');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_financial_tools_are_available_on_their_separate_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Tambah Saldo Masuk')
            ->assertSee('Catat Pengeluaran');

        $this->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Grafik Arus Kas')
            ->assertSee('Pengeluaran per Kategori');

        $this->get(route('budget.settings'))
            ->assertOk()
            ->assertSee('Target Uang Saku Mingguan');
    }

    public function test_profile_can_update_the_account_name_and_email(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($user->email);

        $this->put(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'nama.baru@example.test',
        ])->assertRedirect(route('profile.edit'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru',
            'email' => 'nama.baru@example.test',
        ]);
    }

    public function test_profile_photo_can_be_uploaded(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'photo' => UploadedFile::fake()->create('profile.png', 100, 'image/png'),
            ])
            ->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_profile_cannot_change_email_to_another_users_email(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $otherUser->email,
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_account_menu_only_contains_profile_settings_and_separate_logout_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('budget.index'))
            ->assertOk()
            ->assertSee('Menu akun')
            ->assertSee('Profil')
            ->assertSee('Pengaturan')
            ->assertSee('Keluar')
            ->assertSee(route('logout'))
            ->assertDontSee('Pencatatan transaksi</a>')
            ->assertDontSee('Laporan &amp; grafik</a>');
    }

    public function test_logout_from_profile_menu_ends_the_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_expense_is_saved_from_category_and_formatted_amount(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('transactions.expense'), [
                'category' => 'Makan',
                'amount' => '8.000',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'expense',
            'name' => 'Makan',
            'category' => 'Makan',
            'amount' => 8000,
        ]);
    }

    public function test_other_expense_requires_a_custom_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('transactions.expense'), [
                'category' => 'Lainnya',
                'amount' => '12.500',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('other_name');

        $this->post(route('transactions.expense'), [
            'category' => 'Lainnya',
            'other_name' => 'Obat',
            'amount' => '12.500',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'expense',
            'name' => 'Obat',
            'category' => 'Lainnya',
            'amount' => 12500,
        ]);
    }

    public function test_income_and_weekly_budget_accept_thousands_separators(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('transactions.income'), [
                'source' => 'Ayah',
                'name' => 'Kiriman orang tua',
                'amount' => '1.250.000',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('transactions.index'));

        $this->put(route('budget.update'), [
            'weekly_budget' => '2.000.000',
        ])->assertRedirect(route('budget.settings'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'name' => 'Kiriman orang tua',
            'amount' => 1250000,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'weekly_budget' => 2000000,
        ]);
    }

    public function test_currency_amount_rejects_incorrect_thousands_grouping(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('transactions.expense'), [
                'category' => 'Makan',
                'amount' => '8.00',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_transaction_forms_include_a_date_and_store_the_selected_date(): void
    {
        $user = User::factory()->create();
        $date = '2020-04-16';

        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSee('Tanggal pemasukan')
            ->assertSee('Tanggal pengeluaran');

        $this->post(route('transactions.expense'), [
            'category' => 'Transport',
            'amount' => '8.000',
            'transaction_date' => $date,
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'name' => 'Transport',
            'transaction_date' => $date.' 00:00:00',
            'amount' => 8000,
        ]);
    }

    public function test_transaction_history_can_be_filtered_by_a_specific_date(): void
    {
        $user = User::factory()->create();
        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Belanja pada tanggal terpilih',
            'amount' => 12000,
            'category' => 'Makan',
            'transaction_date' => '2024-05-10',
        ]);
        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Belanja pada tanggal lain',
            'amount' => 9000,
            'category' => 'Transport',
            'transaction_date' => '2024-05-11',
        ]);

        $this->actingAs($user)
            ->get(route('transactions.index', ['date' => '2024-05-10']))
            ->assertOk()
            ->assertSee('Belanja pada tanggal terpilih')
            ->assertDontSee('Belanja pada tanggal lain')
            ->assertSee('10/05/2024');
    }

    public function test_income_is_recorded_with_family_source_and_purpose(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('transactions.income'), [
                'source' => 'Ibu',
                'name' => 'Uang saku bulan ini',
                'amount' => '500.000',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'income',
            'category' => 'Ibu',
            'name' => 'Uang saku bulan ini',
            'amount' => 500000,
        ]);
    }

    public function test_income_source_must_be_a_family_member(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('transactions.income'), [
                'source' => 'Teman',
                'name' => 'Titipan',
                'amount' => '10.000',
                'transaction_date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('source');
    }

    public function test_report_can_filter_historical_transactions_by_month(): void
    {
        $user = User::factory()->create();
        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Pengeluaran bulan lalu',
            'amount' => 25000,
            'category' => 'Makan',
            'transaction_date' => now()->subMonth()->startOfMonth()->addDays(4)->toDateString(),
        ]);

        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Pengeluaran bulan ini',
            'amount' => 10000,
            'category' => 'Transport',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('reports.index', ['filter' => 'month', 'month' => now()->subMonth()->format('Y-m')]))
            ->assertOk()
            ->assertSee('Pengeluaran bulan lalu')
            ->assertDontSee('Pengeluaran bulan ini')
            ->assertSee('25.000');
    }

    public function test_report_can_filter_transactions_by_custom_date_range(): void
    {
        $user = User::factory()->create();
        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Transaksi sesuai rentang',
            'amount' => 8000,
            'category' => 'Makan',
            'transaction_date' => '2021-02-10',
        ]);

        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Transaksi di luar rentang',
            'amount' => 12000,
            'category' => 'Transport',
            'transaction_date' => '2021-01-20',
        ]);

        $this->actingAs($user)
            ->get(route('reports.index', [
                'filter' => 'dates',
                'start_date' => '2021-02-09',
                'end_date' => '2021-02-12',
            ]))
            ->assertOk()
            ->assertSee('Transaksi sesuai rentang')
            ->assertDontSee('Transaksi di luar rentang')
            ->assertSee('8.000');
    }

    public function test_report_rejects_a_date_range_that_ends_before_it_starts(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('reports.index', [
                'filter' => 'dates',
                'start_date' => '2026-10-20',
                'end_date' => '2026-10-01',
            ]))
            ->assertSessionHasErrors('end_date');
    }

    public function test_preferences_save_date_format_low_balance_threshold_and_dark_mode(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('preferences.update'), [
                'date_format' => 'Y-m-d',
                'low_balance_percent' => 30,
                'dark_mode' => '1',
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'date_format' => 'Y-m-d',
            'low_balance_percent' => 30,
            'dark_mode' => 1,
        ]);
    }

    public function test_floating_theme_button_toggles_saved_dark_mode_preference(): void
    {
        $user = User::factory()->create(['dark_mode' => false]);

        $this->actingAs($user)
            ->get(route('budget.index'))
            ->assertOk()
            ->assertSee('class="theme-fab"', false)
            ->assertSee('Ganti ke mode gelap');

        $this->put(route('theme.update'), ['dark_mode' => '1'])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'dark_mode' => 1,
        ]);

        $this->get(route('budget.index'))
            ->assertOk()
            ->assertSee('dark-mode', false)
            ->assertSee('Ganti ke mode terang');
    }

    public function test_dashboard_shows_low_balance_alert_at_configured_threshold(): void
    {
        $user = User::factory()->create([
            'weekly_budget' => 100000,
            'low_balance_percent' => 20,
        ]);
        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Belanja',
            'amount' => 80000,
            'category' => 'Makan',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('budget.index'))
            ->assertOk()
            ->assertSee('Saldo menipis');
    }

    public function test_password_can_be_changed_after_current_password_is_verified(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword123!']);

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('NewPassword456!', $user->fresh()->password));
    }

    public function test_password_change_rejects_an_incorrect_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'incorrect-password',
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ])
            ->assertSessionHasErrors('current_password');
    }

    public function test_google_login_is_removed_and_login_page_has_password_preview_control(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('Masuk dengan Google')
            ->assertSee('data-password-toggle', false);

        $this->assertFalse(Route::has('google.redirect'));
        $this->assertFalse(Route::has('google.callback'));
        $this->get('/auth/google/redirect')->assertNotFound();
    }

    public function test_legacy_google_linked_user_must_provide_current_password_to_change_it(): void
    {
        $user = User::factory()->create([
            'google_id' => 'legacy-google-profile',
            'password' => 'OldPassword123!',
        ]);

        $this->actingAs($user)
            ->put(route('password.update'), [
                'password' => 'NewPassword456!',
                'password_confirmation' => 'NewPassword456!',
            ])
            ->assertSessionHasErrors('current_password');

        $this->put(route('password.update'), [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword456!',
            'password_confirmation' => 'NewPassword456!',
        ])
            ->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('NewPassword456!', $user->fresh()->password));
    }

    public function test_all_transactions_can_only_be_reset_after_typed_confirmation(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->transactions()->create([
            'type' => 'expense',
            'name' => 'Transaksi milik saya',
            'amount' => 5000,
            'category' => 'Makan',
            'transaction_date' => now()->toDateString(),
        ]);
        $otherUser->transactions()->create([
            'type' => 'expense',
            'name' => 'Transaksi pengguna lain',
            'amount' => 7000,
            'category' => 'Transport',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->delete(route('transactions.destroy-all'), ['confirmation' => 'SALAH'])
            ->assertSessionHasErrors('confirmation');
        $this->assertDatabaseHas('transactions', ['name' => 'Transaksi milik saya']);

        $this->delete(route('transactions.destroy-all'), ['confirmation' => 'HAPUS'])
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseMissing('transactions', ['name' => 'Transaksi milik saya']);
        $this->assertDatabaseHas('transactions', ['name' => 'Transaksi pengguna lain']);
    }

    public function test_report_can_be_exported_as_csv_for_the_selected_month(): void
    {
        $user = User::factory()->create();
        $user->transactions()->create([
            'type' => 'expense',
            'name' => '=SUM(A1:A2)',
            'amount' => 8000,
            'category' => 'Makan',
            'transaction_date' => '2021-02-10',
        ]);

        $response = $this->actingAs($user)
            ->get(route('reports.export', ['filter' => 'month', 'month' => '2021-02']))
            ->assertDownload('laporan-keuangan-2021-02.csv');

        $this->assertStringContainsString("'=SUM(A1:A2)", $response->streamedContent());
        $this->assertStringContainsString(',8000', $response->streamedContent());
    }
}
