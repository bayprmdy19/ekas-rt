<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required_without:email', 'nullable', 'string'],
            'email' => ['required_without:login', 'nullable', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'login.required_without' => 'Nomor HP, Nomor KK, atau Kredensial login wajib diisi.',
            'email.required_without' => 'Nomor HP, Nomor KK, atau Kredensial login wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim((string) ($this->input('login') ?: $this->input('email')));
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $login,
            'password' => $this->input('password'),
        ];

        $authenticated = Auth::attempt($credentials, $this->boolean('remember'));

        if (! $authenticated && $fieldType === 'username') {
            $candidateUserIds = collect();

            // 1. Cari langsung di tabel users berdasarkan no_hp, no_kk, atau username
            $usersFound = \App\Models\User::where('no_hp', $login)
                ->orWhere('no_kk', $login)
                ->orWhere('username', $login)
                ->get();

            foreach ($usersFound as $u) {
                $candidateUserIds->push($u->id);
            }

            // 2. Cari berdasarkan Nomor HP / WhatsApp atau Nomor KK pada data Kartu Keluarga
            $kks = \App\Models\KartuKeluarga::where('no_hp', $login)
                ->orWhere('nomor_kk', $login)
                ->get();

            foreach ($kks as $kk) {
                if ($kk->user_id) {
                    $candidateUserIds->push($kk->user_id);
                }
                if ($kk->nomor_kk) {
                    $u = \App\Models\User::where('username', $kk->nomor_kk)->first();
                    if ($u) {
                        $candidateUserIds->push($u->id);
                    }
                }
                if ($kk->kepala_keluarga) {
                    $u = \App\Models\User::where('name', $kk->kepala_keluarga)->first();
                    if ($u) {
                        $candidateUserIds->push($u->id);
                    }
                }
            }

            // 3. Cari berdasarkan Nomor HP / WhatsApp atau Nomor KK pada data Warga
            $wargas = \App\Models\Warga::where('no_hp', $login)
                ->orWhere('no_kk', $login)
                ->get();

            foreach ($wargas as $warga) {
                if ($warga->user_id) {
                    $candidateUserIds->push($warga->user_id);
                }
                if ($warga->no_kk) {
                    $u = \App\Models\User::where('username', $warga->no_kk)->first();
                    if ($u) {
                        $candidateUserIds->push($u->id);
                    }
                }
                if ($warga->nama_lengkap) {
                    $u = \App\Models\User::where('name', $warga->nama_lengkap)->first();
                    if ($u) {
                        $candidateUserIds->push($u->id);
                    }
                }
            }

            $candidates = \App\Models\User::whereIn('id', $candidateUserIds->unique())->get();
            foreach ($candidates as $candidate) {
                if (\Illuminate\Support\Facades\Hash::check($this->input('password'), $candidate->password)) {
                    Auth::login($candidate, $this->boolean('remember'));
                    $authenticated = true;
                    break;
                }
            }
        }

        if (! $authenticated && $fieldType === 'username') {
            $authenticated = Auth::attempt([
                'name' => $login,
                'password' => $this->input('password'),
            ], $this->boolean('remember'));
        }

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            $errorField = $this->has('login') ? 'login' : 'email';

            throw ValidationException::withMessages([
                $errorField => 'Nomor HP / Nomor KK atau Password yang Anda masukkan tidak sesuai.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $errorField = $this->has('login') ? 'login' : 'email';

        throw ValidationException::withMessages([
            $errorField => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $login = (string) ($this->input('login') ?: $this->input('email'));
        return Str::transliterate(Str::lower($login).'|'.$this->ip());
    }
}
