<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Kedaluwarsa (419) - E-KAS RT</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="flex flex-col min-h-full items-center justify-center p-4 text-slate-800 antialiased selection:bg-blue-600 selection:text-white bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40">

    <div class="max-w-lg w-full bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-xl text-center space-y-6">
        
        <!-- Status Code Badge & Icon -->
        <div class="w-16 h-16 rounded-3xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center mx-auto text-2xl font-black shadow-xs ring-8 ring-amber-50/50">
            ⏳
        </div>

        <div class="space-y-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                Error 419 • Sesi Kedaluwarsa
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Halaman / Sesi Telah Berakhir
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Keamanan sistem (CSRF Token) mendeteksi sesi Anda telah kedaluwarsa atau Anda baru saja <strong>berganti akun / login dengan akun lain (misal: Warga) di tab browser yang sama</strong>.
            </p>
        </div>

        <!-- Tips Penjelasan -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-left text-xs text-slate-600 space-y-2">
            <p class="font-bold text-slate-800 flex items-center gap-1.5">
                <span>💡</span> Mengapa ini terjadi?
            </p>
            <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-500 leading-relaxed">
                <li>Satu browser hanya dapat menyimpan 1 sesi akun aktif sekaligus.</li>
                <li>Jika membuka akun <strong>Admin RT</strong> dan akun <strong>Warga</strong> bersamaan, gunakan <strong>Jendela Penyamaran (Incognito / Private Window)</strong> untuk salah satunya.</li>
                <li>Sesi tidak aktif dalam waktu lama juga dapat menyebabkan halaman kedaluwarsa.</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2.5 pt-2">
            <button onclick="window.location.reload()" 
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-blue-200 flex items-center justify-center gap-2">
                <span>🔄</span> Muat Ulang (Refresh) Halaman
            </button>

            <div class="grid grid-cols-2 gap-2.5">
                <a href="{{ route('login') }}" 
                   class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition border border-slate-200">
                    Masuk Admin RT
                </a>
                <a href="{{ url('/') }}" 
                   class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition border border-slate-200">
                    Halaman Utama
                </a>
            </div>
        </div>

    </div>

</body>
</html>
