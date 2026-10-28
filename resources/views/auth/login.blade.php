<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Portal - E-KAS RT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        input::placeholder {
            color: rgba(100, 116, 139, 0.55);
        }
    </style>
</head>
<body class="bg-slate-100 font-sans antialiased min-h-screen text-slate-800">

    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">
        
        <!-- SISI KIRI: HERO BRANDING -->
        <div class="bg-blue-600 text-white p-8 sm:p-12 lg:p-16 flex flex-col justify-between relative overflow-hidden">
            <!-- Pattern Dot Hiasan Latar Belakang -->
            <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>

            <!-- Logo & Brand Top -->
            <div class="relative z-10 flex items-center gap-3">
                <div class="h-10 w-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center text-white font-black text-xl border border-white/30 shadow-sm">
                    RT
                </div>
                <div>
                    <h1 class="text-lg font-black tracking-wide leading-none uppercase">E-KAS RT</h1>
                    <span class="text-[11px] text-blue-100 font-medium tracking-wider uppercase">Portal Admin & Layanan Warga</span>
                </div>
            </div>

            <!-- Hero Text Center -->
            <div class="relative z-10 my-10 lg:my-0 space-y-4 max-w-lg">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md text-blue-100 border border-white/20 tracking-wider uppercase">
                    Portal Terpadu RT
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    Satu portal untuk pengurus dan warga lingkungan.
                </h2>
                <p class="text-sm sm:text-base text-blue-100 leading-relaxed font-normal">
                    Pengurus dapat memantau kas, iuran, dan permohonan surat. Warga dapat memantau transparansi keuangan dan mengajukan layanan secara mandiri.
                </p>
                <div class="pt-2 flex flex-wrap gap-2 text-xs text-blue-100">
                    <span class="bg-blue-700/60 px-3 py-1.5 rounded-lg border border-blue-400/30">Pengurus: /admin/dashboard</span>
                    <span class="bg-blue-700/60 px-3 py-1.5 rounded-lg border border-blue-400/30">Warga: /warga/dashboard</span>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="relative z-10 text-xs text-blue-200 font-semibold tracking-wider uppercase">
                &copy; {{ date('Y') }} E-KAS RT. Transparansi & Pelayanan Warga.
            </div>
        </div>

        <!-- SISI KANAN: FORM LOGIN -->
        <div class="flex items-center justify-center p-6 sm:p-12 lg:p-16 bg-slate-50">
            <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl space-y-6">
                
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Masuk Portal</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Gunakan Nomor HP yang telah terdaftar untuk melanjutkan.
                    </p>
                </div>

                <!-- Status Session (Lupa Password / Logout) -->
                @if (session('status'))
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-700 font-medium">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Field Nomor HP / Nomor KK -->
                    <div>
                        <label for="login" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nomor HP atau Nomor KK</label>
                        <input id="login" type="text" name="login" value="{{ old('login') ?? old('email') }}" required autofocus autocomplete="username"
                            placeholder="Contoh: 081234567890 atau 16 digit No. KK"
                            class="w-full text-xs border border-slate-200 rounded-xl p-3.5 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition @if($errors->has('login') || $errors->has('email')) border-rose-500 bg-rose-50 @endif">
                        @if ($errors->has('login'))
                            <span class="text-[11px] text-rose-500 mt-1.5 block font-medium">{{ $errors->first('login') }}</span>
                        @elseif ($errors->has('email'))
                            <span class="text-[11px] text-rose-500 mt-1.5 block font-medium">{{ $errors->first('email') }}</span>
                        @endif
                    </div>

                    <!-- Field Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[11px] text-blue-600 hover:text-blue-700 hover:underline font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500 rounded">
                                    Lupa password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                placeholder="Masukkan password Anda"
                                class="w-full text-xs border border-slate-200 rounded-xl p-3.5 pr-11 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('password') border-rose-500 bg-rose-50 @enderror">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" aria-label="Tampilkan atau sembunyikan password"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none">
                                <!-- Eye Icon (when hidden) -->
                                <svg class="w-5 h-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <!-- Eye Slash Icon (when shown) -->
                                <svg class="w-5 h-5 eye-slash-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="text-[11px] text-rose-500 mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <label for="remember_me" class="ml-2 text-xs text-slate-600 font-medium cursor-pointer">Ingat saya pada perangkat ini</label>
                    </div>

                    <!-- Tombol Submit -->
                    <button type="submit" class="w-full min-h-[44px] py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Masuk ke Akun
                    </button>
                </form>

                <!-- Link Daftar Baru -->
                <div class="pt-2 text-center text-xs text-slate-600">
                    Belum memiliki akun warga?
                    <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline ml-1">
                        Daftar Akun Baru
                    </a>
                </div>

                <!-- Link Kembali -->
                <div class="pt-2 text-center border-t border-slate-100">
                    <a href="/" class="text-xs text-slate-500 hover:text-slate-800 font-semibold transition inline-flex items-center gap-1">
                        &larr; Kembali ke Landing Page Publik
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