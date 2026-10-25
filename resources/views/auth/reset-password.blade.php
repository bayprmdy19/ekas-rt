<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password - E-KAS RT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased min-h-screen text-slate-800">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        
        <!-- SISI KIRI: HERO BRANDING -->
        <div class="bg-blue-600 text-white p-8 sm:p-12 lg:p-16 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>

            <div class="relative z-10 flex items-center gap-3">
                <div class="h-10 w-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white font-black text-xl border border-white/30 shadow-sm">
                    RT
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-wide leading-none uppercase">E-KAS RT</h1>
                    <span class="text-[11px] text-blue-100 font-medium tracking-wider uppercase">Keamanan Akun Warga</span>
                </div>
            </div>

            <div class="relative z-10 my-10 lg:my-0 space-y-4 max-w-lg">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md text-blue-100 border border-white/20 tracking-wider uppercase">
                    Pembaruan Keamanan
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    Buat password baru yang kuat dan aman.
                </h2>
                <p class="text-sm sm:text-base text-blue-100 leading-relaxed font-normal">
                    Pastikan password baru Anda terdiri dari minimal 8 karakter serta mengombinasikan huruf dan angka demi menjaga keamanan akses data kas dan informasi warga.
                </p>
            </div>

            <div class="relative z-10 text-xs text-blue-200 font-semibold tracking-wider uppercase">
                &copy; {{ date('Y') }} E-KAS RT. Transparansi & Pelayanan Warga.
            </div>
        </div>

        <!-- SISI KANAN: FORM RESET PASSWORD -->
        <div class="flex items-center justify-center p-6 sm:p-12 lg:p-16 bg-slate-50">
            <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl space-y-5">
                
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Atur Ulang Password</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Silakan buat kata sandi baru untuk akun Anda.
                    </p>
                </div>

                <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Alamat Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                            placeholder="nama@email.com"
                            class="w-full text-xs border border-slate-200 rounded-xl p-3.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email') border-rose-500 bg-rose-50 @enderror">
                        @error('email')
                            <span class="text-[11px] text-rose-500 mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Password Baru</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="new-password"
                                placeholder="Minimal 8 karakter (huruf & angka)"
                                class="w-full text-xs border border-slate-200 rounded-xl p-3.5 pr-11 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('password') border-rose-500 bg-rose-50 @enderror">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" aria-label="Tampilkan atau sembunyikan password"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none">
                                <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="w-5 h-5 eye-slash-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Wajib minimal 8 karakter kombinasi huruf dan angka.</p>
                        @error('password')
                            <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                                placeholder="Ulangi password baru"
                                class="w-full text-xs border border-slate-200 rounded-xl p-3.5 pr-11 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('password_confirmation') border-rose-500 bg-rose-50 @enderror">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Tampilkan atau sembunyikan konfirmasi password"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none">
                                <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="w-5 h-5 eye-slash-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <span class="text-[11px] text-rose-500 mt-1 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tombol Reset -->
                    <button type="submit" class="w-full min-h-[44px] py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Simpan Password Baru
                    </button>
                </form>

                <div class="pt-2 text-center text-xs text-slate-600">
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline">
                        &larr; Batalkan dan Kembali ke Login
                    </a>
                </div>

            </div>
        </div>

    </div>

    <!-- Script Interaktif Show / Hide Password -->
    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            const eyeIcon = btn.querySelector('.eye-icon');
            const eyeSlashIcon = btn.querySelector('.eye-slash-icon');

            if (eyeIcon && eyeSlashIcon) {
                eyeIcon.classList.toggle('hidden', isPassword);
                eyeSlashIcon.classList.toggle('hidden', !isPassword);
            }
        }
    </script>
</body>
</html>
