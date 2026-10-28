<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($validated['photo']);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->storePublicly('profile-photos', 'public');
            if (! $photoPath) {
                return back()->withErrors(['photo' => 'Foto profil gagal disimpan.'])->withInput();
            }

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->profile_photo_path = $photoPath;
        }

        $user->fill($validated)->save();

        return redirect()->route('profile.edit')->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date_format' => ['required', Rule::in(['d/m/Y', 'd M Y', 'Y-m-d'])],
            'low_balance_percent' => ['required', 'integer', Rule::in([10, 20, 30, 50])],
            'dark_mode' => ['required', 'boolean'],
        ]);

        $request->user()->update($validated);

        return redirect()->route('profile.edit')->with('status', 'Preferensi berhasil disimpan.');
    }

    public function updateTheme(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dark_mode' => ['required', 'boolean'],
        ]);

        $request->user()->update($validated);

        return back();
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $rules = [
            'password' => ['required', 'string', 'min:8', 'max:255', 'regex:/\A(?=.*[A-Z])(?=.*\d).+\z/', 'confirmed'],
            'current_password' => ['required', 'current_password'],
        ];

        $validated = $request->validate($rules, [
            'password.min' => 'Password harus mengandung 8 karakter atau lebih.',
            'password.regex' => 'Password harus mengandung huruf kapital dan angka.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('status', 'Kata sandi berhasil diubah.');
    }

    public function destroyTransactions(Request $request): RedirectResponse
    {
        $request->validate([
            'confirmation' => ['required', Rule::in(['HAPUS'])],
        ]);

        DB::transaction(fn () => $request->user()->transactions()->where('application_mode', 'kos')->delete());

        return redirect()->route('profile.edit')->with('status', 'Semua riwayat transaksi berhasil dihapus.');
    }
}
