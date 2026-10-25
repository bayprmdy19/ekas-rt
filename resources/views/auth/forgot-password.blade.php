<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - E-KAS RT</title>
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
                    <span class="text-[11px] text-blue-100 font-medium tracking-wider uppercase">Pemulihan Akses Akun</span>
                </div>
            </div>

            <div class="relative z-10 my-10 lg:my-0 space-y-4 max-w-lg">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md text-blue-100 border border-white/20 tracking-wider uppercase">
                    Bantuan Akun
                </span>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    Reset kata sandi akun Anda dengan aman.
                </h2>
                <p class="text-sm sm:text-base text-blue-100 leading-relaxed font-normal">
                    Masukkan alamat email terdaftar pada akun Anda. Kami akan mengirimkan tautan untuk membuat password baru yang aman.
                </p>
            </div>

            <div class="relative z-10 text-xs text-blue-200 font-semibold tracking-wider uppercase">
                &copy; {{ date('Y') }} E-KAS RT. Transparansi & Pelayanan Warga.
            </div>
        </div>

        <!-- SISI KANAN: FORM LUPA PASSWORD -->
        <div class="flex items-center justify-center p-6 sm:p-12 lg:p-16 bg-slate-50">
            <div class="w-full max-w-md bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl space-y-6">
                
                <div>
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Lupa Password?</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Tidak masalah. Masukkan email akun Anda untuk menerima tautan pemulihan kata sandi.
                    </p>
                </div>

                <!-- Session Status Notifikasi Sukses -->
                @if (session('status'))
                    <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 font-medium leading-relaxed">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Alamat Email Terdaftar</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@email.com"
                            class="w-full text-xs border border-slate-200 rounded-xl p-3.5 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email') border-rose-500 bg-rose-50 @enderror">
                        @error('email')
                            <span class="text-[11px] text-rose-500 mt-1.5 block font-medium">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Tombol Kirim Tautan -->
                    <button type="submit" class="w-full min-h-[44px] py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md hover:shadow-lg transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Kirim Tautan Reset Password
                    </button>
                </form>

                <div class="pt-2 text-center text-xs text-slate-600">
                    Ingat password Anda?
                    <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-700 hover:underline ml-1">
                        Kembali ke Login
                    </a>
                </div>

                <div class="pt-2 text-center border-t border-slate-100">
                    <a href="/" class="text-xs text-slate-500 hover:text-slate-800 font-semibold transition inline-flex items-center gap-1">
                        &larr; Kembali ke Landing Page Publik
                    </a>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
