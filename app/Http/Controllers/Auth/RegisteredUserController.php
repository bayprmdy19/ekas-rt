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
        $request->validate([
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
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
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

        $user = User::create([
            'name' => $request->name,
            'username' => strtolower($request->username),
            'email' => $request->email ?: null,
            'password' => Hash::make($request->password),
            'role' => 'warga',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('warga.dashboard'));
    }
}
