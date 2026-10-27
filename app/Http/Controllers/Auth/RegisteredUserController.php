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
        $isLegacyUsername = $request->filled('username') && !$request->filled('nomor_kk');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::min(8)->letters()->numbers(),
            ],
        ];

        if ($isLegacyUsername) {
            $rules['username'] = [
                'required',
                'string',
                'alpha_dash',
                'min:3',
                'max:30',
                'unique:'.User::class,
            ];
            $rules['email'] = ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class];
        } else {
            $rules['nomor_kk'] = [
                'required',
                'string',
                'digits:16',
                'unique:users,username',
            ];
            $rules['no_whatsapp'] = [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'max:13',
            ];
        }

        $request->validate($rules, [
            'name.required' => 'Nama kepala keluarga wajib diisi.',
            'nomor_kk.required' => 'Nomor KK wajib diisi.',
            'nomor_kk.digits' => 'Nomor KK wajib berjumlah tepat 16 digit angka.',
            'nomor_kk.regex' => 'Nomor KK hanya boleh berisi angka.',
            'nomor_kk.max' => 'Nomor KK maksimal 16 digit angka.',
            'nomor_kk.unique' => 'Nomor KK ini sudah terdaftar, silakan gunakan akun yang sudah ada.',
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'no_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka.',
            'no_whatsapp.max' => 'Nomor WhatsApp maksimal 13 digit angka.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi karakter huruf, angka, dan garis bawah tanpa spasi.',
            'username.min' => 'Username minimal 3 karakter.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Email sudah terdaftar dalam sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password' => 'Password wajib berupa kombinasi huruf dan angka, minimal 8 karakter.',
        ]);

        $username = $isLegacyUsername ? strtolower($request->username) : trim($request->nomor_kk);

        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email ?: null,
            'password' => Hash::make($request->password),
            'role' => 'warga',
        ]);

        if (! $isLegacyUsername) {
            $nomorKk = trim($request->nomor_kk);
            $noWhatsapp = trim($request->no_whatsapp);

            $warga = \App\Models\Warga::where('no_kk', $nomorKk)
                ->orWhere('nama_lengkap', $request->name)
                ->first();

            if ($warga) {
                $warga->update([
                    'no_kk' => $nomorKk,
                    'no_hp' => $noWhatsapp,
                ]);
            } else {
                $warga = \App\Models\Warga::create([
                    'nama_lengkap' => $request->name,
                    'no_kk' => $nomorKk,
                    'no_hp' => $noWhatsapp,
                    'nomor_rumah' => '-',
                    'status_keluarga' => 'Kepala Keluarga',
                    'status_tinggal' => 'tetap',
                ]);
            }

            $kk = \App\Models\KartuKeluarga::where('nomor_kk', $nomorKk)
                ->orWhere('kepala_keluarga', $request->name)
                ->first();

            if ($kk) {
                $kk->update([
                    'user_id' => $user->id,
                    'warga_id' => $kk->warga_id ?: $warga->id,
                    'nomor_kk' => $nomorKk,
                    'no_hp' => $noWhatsapp,
                ]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('warga.dashboard'));
    }
}
