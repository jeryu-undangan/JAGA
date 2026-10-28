<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_account_and_signs_user_in_without_email_verification(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.test',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertRedirect(route('application-mode.choose'));

        $user = User::where('email', 'baru@example.test')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->email_verified_at);
        $this->assertNull($user->application_mode);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('transactions', 0);
        $this->get(route('application-mode.choose'))
            ->assertOk()
            ->assertSee('Pekerja')
            ->assertSee('Anak Kos')
            ->assertSee('mode-choice-card', false);
        $this->assertFalse(Route::has('verification.notice'));
        $this->assertFalse(Route::has('verification.verify'));
        $this->assertFalse(Route::has('verification.resend'));
    }

    public function test_registration_requires_eight_characters_an_uppercase_letter_and_a_number(): void
    {
        foreach (['abcdefgh', 'abc def12', 'Short1'] as $password) {
            $this->post(route('register.store'), [
                'name' => 'Pengguna Baru',
                'email' => 'baru@example.test',
                'password' => $password,
                'password_confirmation' => $password,
            ])->assertSessionHasErrors('password');
        }

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_form_displays_live_email_password_and_confirmation_checks(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Format email sesuai.', false)
            ->assertSee('Mengandung 8 karakter atau lebih')
            ->assertSee('Mengandung huruf kapital (A-Z)')
            ->assertSee('Mengandung angka (0-9)')
            ->assertSee('Password cocok.')
            ->assertSee('data-password-toggle', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('nonce=', false);
    }

    public function test_password_change_uses_the_same_password_requirements(): void
    {
        $user = User::factory()->create(['password' => 'OldPassword123!']);

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'lowercase123',
                'password_confirmation' => 'lowercase123',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_registration_allows_spaces_when_other_password_requirements_are_met(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.test',
            'password' => 'Pass word123',
            'password_confirmation' => 'Pass word123',
        ])->assertRedirect(route('application-mode.choose'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'baru@example.test']);
    }

    public function test_user_can_log_in_without_email_verification(): void
    {
        $user = User::factory()->unverified()->create(['password' => 'Password123']);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'Password123',
        ])->assertRedirect(route('budget.index'));

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_new_account_can_choose_salary_mode_and_gets_a_separate_landing_page(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.test',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])->assertRedirect(route('application-mode.choose'));

        $user = User::where('email', 'baru@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('Password123', $user->password));

        $this->put(route('application-mode.update'), ['application_mode' => 'salary'])
            ->assertRedirect(route('salary.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'application_mode' => 'salary',
        ]);

        $this->get(route('salary.index'))
            ->assertOk()
            ->assertSee('Dashboard Gaji Bulanan')
            ->assertDontSee('Pencatatan transaksi')
            ->assertDontSee('Laporan &amp; grafik');
        $this->get(route('budget.index'))->assertRedirect(route('salary.index'));
        $this->get(route('budget.settings'))
            ->assertOk()
            ->assertSee('Jenis Pencatatan')
            ->assertSee('value="salary"', false)
            ->assertSee('value="kos"', false)
            ->assertSee('Mode akan langsung disimpan saat kamu memilih.')
            ->assertDontSee('<select id="application_mode"', false)
            ->assertSee('Mode Gaji Bulanan')
            ->assertDontSee('Target Uang Saku Mingguan');
    }

    public function test_user_can_switch_from_salary_back_to_the_existing_kos_app(): void
    {
        $user = User::factory()->create([
            'application_mode' => 'salary',
            'payday_interval' => 'weekly',
            'payday_anchor_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($user)
            ->put(route('application-mode.update'), ['application_mode' => 'kos'])
            ->assertRedirect(route('budget.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'application_mode' => 'kos',
        ]);
        $this->get(route('budget.index'))->assertOk()->assertSee('Dashboard Keuangan');
        $this->get(route('application-mode.choose'))->assertRedirect(route('budget.index'));
    }

    public function test_saving_kos_mode_for_the_first_time_continues_to_payday_setup(): void
    {
        $user = User::factory()->create([
            'application_mode' => null,
            'payday_interval' => null,
            'payday_anchor_date' => null,
        ]);

        $this->actingAs($user)
            ->put(route('application-mode.update'), ['application_mode' => 'kos'])
            ->assertRedirect(route('budget.schedule.setup'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'application_mode' => 'kos',
        ]);
        $this->get(route('budget.schedule.setup'))->assertOk();
    }
}
