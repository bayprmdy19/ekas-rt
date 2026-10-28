<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $isLegacy = $request->filled('username') && !$request->filled('no_hp') && !$request->filled('no_whatsapp');

        if ($isLegacy) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'username' => [
                    'required',
                    'string',
                    'alpha_dash',
                    'min:3',
                    'max:30',
                    'unique:'.User::class,
                ],
                'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::min(8)->letters()->numbers(),
                ],
            ];
            $request->validate($rules);
            $loginId = strtolower($request->username);
            $noHp = null;
        } else {
            $noHp = trim((string) ($request->input('no_hp') ?: $request->input('no_whatsapp')));
            $request->merge(['no_hp' => $noHp]);

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'no_hp' => [
                    'required',
                    'string',
                    'regex:/^[0-9]+$/',
                    'min:9',
                    'max:15',
                    'unique:users,username',
                ],
                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::min(8)->letters()->numbers(),
                ],
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'no_hp.required' => 'Nomor HP wajib diisi.',
                'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
                'no_hp.min' => 'Nomor HP minimal 9 digit angka.',
                'no_hp.max' => 'Nomor HP maksimal 15 digit angka.',
                'no_hp.unique' => 'Nomor HP ini sudah terdaftar, silakan gunakan untuk masuk ke portal.',
                'password.required' => 'Password wajib diisi.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'password.min' => 'Password minimal 8 karakter.',
                'password' => 'Password wajib berupa kombinasi huruf dan angka, minimal 8 karakter.',
            ]);

            $loginId = $noHp;
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $loginId,
            'no_hp' => $noHp,
            'email' => $request->email ?: null,
            'password' => Hash::make($request->password),
            'role' => 'warga',
        ]);

        if ($noHp) {
            $warga = \App\Models\Warga::where('no_hp', $noHp)
                ->orWhere('nama_lengkap', $request->name)
                ->first();

            if ($warga) {
                $warga->update([
                    'nama_lengkap' => $request->name,
                    'no_hp' => $noHp,
                ]);
            } else {
                \App\Models\Warga::create([
                    'nama_lengkap' => $request->name,
                    'no_hp' => $noHp,
                    'no_kk' => null,
                    'nomor_rumah' => null,
                    'status_keluarga' => 'Kepala Keluarga',
                    'status_tinggal' => 'tetap',
                ]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('warga.onboarding');
    }
}
