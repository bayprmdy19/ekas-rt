<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-KAS RT - Transparansi Keuangan & Layanan Warga</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="flex flex-col min-h-full text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Navbar Header -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black text-lg shadow-md shadow-blue-200">
                    RT
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-tight">E-KAS RT</h1>
                    <p class="text-[11px] font-medium text-slate-500">Transparansi & Layanan Publik Warga</p>
                </div>
            </div>

            <div>
                @if (Route::has('login'))
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition shadow-sm">
                                Dashboard Pengurus
                            </a>
                        @else
                            <a href="{{ route('warga.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition shadow-sm">
                                Portal Warga
                            </a>
                        @endif
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition border border-slate-200">
                                Masuk Portal
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition shadow-sm">
                                    Daftar Warga
                                </a>
                            @endif
                        </div>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">

        <!-- Hero Card Transparansi Kas -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 text-white rounded-3xl p-8 sm:p-10 shadow-xl border border-blue-700/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="max-w-2xl space-y-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-200 border border-blue-400/30">
                    Portal Keuangan & Layanan Publik RT
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-snug">
                    Transparansi Kas Lingkungan & Layanan Warga
                </h2>
                <p class="text-sm text-blue-200/80 leading-relaxed">
                    Pantau arus kas masuk-keluar secara terbuka dan ajukan administrasi surat atau pengaduan lingkungan secara langsung.
                </p>
            </div>
            <a href="#layanan-warga" class="px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl text-xs transition shadow-lg whitespace-nowrap">
                Layanan Warga ↓
            </a>
        </div>

        <!-- Cards Ringkasan Kas -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pemasukan</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalMasuk ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">↓</div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-rose-600 mt-1">Rp {{ number_format($totalKeluar ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xl">↑</div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Saldo Kas RT</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl">💰</div>
            </div>
        </div>



        <!-- SECTION LAYANAN & PENGADUAN -->
        <section id="layanan-warga" class="pt-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                
                <!-- KOLOM KIRI: Informasi & Alur Layanan RT -->
                <div class="lg:col-span-5 space-y-6">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-2">Pelayanan Warga</p>
                        <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight leading-snug">
                            Layanan Administrasi & Pengaduan Warga
                        </h3>
                        <p class="text-sm text-slate-600 mt-2.5 leading-relaxed">
                            Formulir daring untuk mempermudah warga mengurus surat pengantar RT atau melaporkan kendala fasilitas di lingkungan kita.
                        </p>
                    </div>

                    <!-- Alur Pelayanan (3 Langkah Praktis) -->
                    <div class="space-y-3.5 bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700">Alur Pengurusan:</h4>
                        
                        <div class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200">1</span>
                            <div>
                                <p class="text-xs font-semibold text-slate-800">Isi Formulir</p>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Pilih kategori layanan, cantumkan nomor rumah/blok, dan tuliskan keperluan dengan jelas.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200">2</span>
                            <div>
                                <p class="text-xs font-semibold text-slate-800">Verifikasi Pengurus</p>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Pengurus RT akan memeriksa kelengkapan data atau meninjau lokasi jika berupa aduan fasilitas.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 font-bold text-xs border border-blue-200">3</span>
                            <div>
                                <p class="text-xs font-semibold text-slate-800">Penyelesaian</p>
                                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Surat pengantar dapat diambil di kediaman Ketua/Sekretaris RT, atau aduan ditindaklanjuti ke pihak terkait.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Operasional Pengurus -->
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-1.5 text-xs text-slate-600">
                        <p class="font-semibold text-slate-800">Catatan Warga:</p>
                        <ul class="list-disc list-inside space-y-1 text-slate-500 text-[11px] leading-relaxed">
                            <li>Untuk surat pengantar kependudukan (KTP/KK), mohon siapkan fotokopi berkas terkait saat pengambilan.</li>
                            <li>Aduan fasilitas/lingkungan dapat dikirim tanpa nama (anonim) jika diinginkan.</li>
                            <li>Waktu respon pengurus berkisar pukul 08.00 – 21.00 WIB.</li>
                        </ul>
                    </div>
                </div>

                <!-- KOLOM KANAN: Form Layanan Publik Card -->
                <div class="lg:col-span-7 bg-white p-7 sm:p-8 rounded-3xl border border-slate-200/90 shadow-sm space-y-6">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Formulir Pengajuan</p>
                        <h4 class="text-xl sm:text-2xl font-bold text-slate-900">Kirim Permohonan Layanan</h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Pilih kategori layanan yang dibutuhkan dan isi data secara lengkap.
                        </p>
                    </div>

                    <!-- Alert Session / Notifikasi Sukses Inline -->
                    @if(session('success'))
                        <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-xs font-semibold border border-emerald-200 flex items-start gap-3 shadow-xs">
                            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-sm font-bold">
                                ✓
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-emerald-900 text-[13px]">Pengajuan Berhasil Terkirim!</p>
                                <p class="mt-0.5 text-emerald-700 leading-relaxed">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Alert Validasi Error -->
                    @if($errors->any())
                        <div class="p-4 bg-rose-50 text-rose-800 rounded-2xl text-xs font-semibold border border-rose-200 shadow-xs">
                            <div class="flex items-center gap-2 mb-1.5 font-bold text-rose-900">
                                <span>⚠️</span> Mohon lengkapi formulir dengan benar:
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-rose-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Form Terhubung ke Route layanan.warga -->
                    <form action="{{ route('layanan.warga') }}" method="POST" class="space-y-5" x-data="{ submitting: false, kategoriLayanan: '{{ old('kategori_layanan', '') }}' }" @submit="submitting = true">
                        @csrf

                        <!-- Pilihan Jenis Layanan (Kategori Utama) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kategori Layanan <span class="text-rose-500">*</span></label>
                            <select name="kategori_layanan" x-model="kategoriLayanan" class="w-full text-xs font-semibold rounded-2xl border-slate-200 p-3.5 focus:border-blue-600 focus:ring-blue-600 bg-slate-50 text-slate-800" required>
                                <option value="">-- Pilih Jenis Layanan --</option>
                                <optgroup label="── 📣 PENGADUAN LINGKUNGAN">
                                    <option value="Lapor Fasilitas Publik / Lampu Jalan Rusak" {{ old('kategori_layanan') == 'Lapor Fasilitas Publik / Lampu Jalan Rusak' ? 'selected' : '' }}>Lapor Fasilitas Publik / Lampu Jalan Rusak</option>
                                    <option value="Lapor Masalah Kebersihan & Sampah" {{ old('kategori_layanan') == 'Lapor Masalah Kebersihan & Sampah' ? 'selected' : '' }}>Lapor Masalah Kebersihan & Sampah</option>
                                    <option value="Lapor Gangguan Keamanan & Ketertiban" {{ old('kategori_layanan') == 'Lapor Gangguan Keamanan & Ketertiban' ? 'selected' : '' }}>Lapor Gangguan Keamanan & Ketertiban</option>
                                </optgroup>
                                <optgroup label="── 📄 ADMINISTRASI SURAT RT">
                                    <option value="Surat Pengantar KTP / Kartu Keluarga" {{ old('kategori_layanan') == 'Surat Pengantar KTP / Kartu Keluarga' ? 'selected' : '' }}>Surat Pengantar KTP / Kartu Keluarga</option>
                                    <option value="Surat Keterangan Domisili" {{ old('kategori_layanan') == 'Surat Keterangan Domisili' ? 'selected' : '' }}>Surat Keterangan Domisili</option>
                                    <option value="Surat Keterangan Usaha (SKU)" {{ old('kategori_layanan') == 'Surat Keterangan Usaha (SKU)' ? 'selected' : '' }}>Surat Keterangan Usaha (SKU)</option>
                                    <option value="Surat Pengantar Nikah" {{ old('kategori_layanan') == 'Surat Pengantar Nikah' ? 'selected' : '' }}>Surat Pengantar Nikah</option>
                                    <option value="Lain-lain" {{ old('kategori_layanan') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain (Kebutuhan Khusus / Lainnya)</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Form Dinamis Khusus Opsi 'Lain-lain' (Tujuan Surat & Keterangan Detail) -->
                        <div x-show="kategoriLayanan === 'Lain-lain'" x-cloak class="space-y-4 p-4.5 rounded-2xl bg-amber-50/80 border border-amber-200/90 transition">
                            <div class="flex items-center gap-2 text-amber-800 text-xs font-bold">
                                <span>📌</span>
                                <span>Detail Pengajuan Surat Khusus (Lain-lain)</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Tujuan Surat <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="tujuan_surat" :required="kategoriLayanan === 'Lain-lain'" value="{{ old('tujuan_surat') }}" placeholder="Contoh: Ke Bank BCA / Pengajuan KPR / Beasiswa" class="w-full text-xs rounded-2xl border-slate-200 p-3 focus:border-amber-600 focus:ring-amber-600 bg-white">
                                <span class="text-[11px] text-slate-500 mt-1 block">Tuliskan instansi, lembaga, atau pihak tujuan penyerahan surat.</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Keterangan Detail <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="keterangan_detail" :required="kategoriLayanan === 'Lain-lain'" rows="3" placeholder="Tuliskan alasan lengkap atau rincian spesifik surat yang Anda butuhkan..." class="w-full text-xs rounded-2xl border-slate-200 p-3.5 focus:border-amber-600 focus:ring-amber-600 bg-white">{{ old('keterangan_detail') }}</textarea>
                            </div>
                        </div>

                        <!-- Data Diri Pemohon (Kondisi Auth vs Guest) -->
                        @auth
                            @php
                                $currentUser = auth()->user();
                                $currentWarga = $currentUser->warga ?: \App\Models\Warga::where('user_id', $currentUser->id)->orWhere('no_hp', $currentUser->no_hp)->orWhere('nama_lengkap', $currentUser->name)->first();
                                $namaAuto = $currentWarga?->nama_lengkap ?? $currentUser->name;
                                $rumahAuto = $currentWarga?->nomor_rumah ?? ($currentUser->kartuKeluarga?->nomor_rumah ?? '-');
                                $waAuto = $currentUser->no_hp ?? ($currentWarga?->no_hp ?? $currentUser->username);
                            @endphp

                            <div class="p-3 bg-blue-50 border border-blue-200 rounded-2xl flex items-center justify-between text-xs text-blue-800">
                                <div class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Akun Terverifikasi: <strong>{{ $namaAuto }}</strong> (Data diri otomatis terisi)</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Warga Login</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Pemohon / Pelapor</label>
                                    <input type="text" name="nama_pemohon" value="{{ $namaAuto }}" readonly class="w-full text-xs rounded-2xl border-slate-200 p-3 bg-slate-100 text-slate-600 font-semibold cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rumah / Blok</label>
                                    <input type="text" name="nomor_rumah" value="{{ $rumahAuto }}" readonly class="w-full text-xs rounded-2xl border-slate-200 p-3 bg-slate-100 text-slate-600 font-semibold cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor WhatsApp</label>
                                    <input type="text" name="nomor_whatsapp" value="{{ $waAuto }}" readonly class="w-full text-xs rounded-2xl border-slate-200 p-3 bg-slate-100 text-slate-600 font-semibold cursor-not-allowed">
                                </div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Pemohon / Pelapor <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon') }}" placeholder="Nama lengkap Anda" class="w-full text-xs rounded-2xl border-slate-200 p-3 focus:border-blue-600 focus:ring-blue-600 bg-slate-50" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Rumah / Blok <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nomor_rumah" value="{{ old('nomor_rumah') }}" placeholder="Contoh: Blok A No. 12" class="w-full text-xs rounded-2xl border-slate-200 p-3 focus:border-blue-600 focus:ring-blue-600 bg-slate-50" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                                    <input type="tel" name="nomor_whatsapp" value="{{ old('nomor_whatsapp') }}" inputmode="numeric" maxlength="15" placeholder="Contoh: 081234567890" class="w-full text-xs rounded-2xl border-slate-200 p-3 focus:border-blue-600 focus:ring-blue-600 bg-slate-50" required>
                                </div>
                            </div>
                        @endauth

                        <!-- Deskripsi Laporan / Keperluan (Untuk selain Lain-lain) -->
                        <div x-show="kategoriLayanan !== 'Lain-lain'">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Laporan / Keperluan <span class="text-rose-500">*</span></label>
                            <textarea name="deskripsi" :required="kategoriLayanan !== 'Lain-lain'" rows="4" placeholder="Tuliskan situasi atau detail pengajuan Anda secara jelas agar pengurus RT bisa membantu dengan lebih tepat." class="w-full text-xs rounded-2xl border-slate-200 p-3.5 focus:border-blue-600 focus:ring-blue-600 bg-slate-50">{{ old('deskripsi') }}</textarea>
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" :disabled="submitting" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold rounded-2xl transition text-xs shadow-md shadow-blue-200 flex items-center justify-center gap-2">
                            <span x-show="!submitting">Kirim Permohonan Layanan</span>
                            <span x-show="submitting" style="display: none;">Mengirim Pengajuan...</span>
                        </button>
                    </form>
                </div>

            </div>
        </section>

        <!-- Modal Notifikasi Pengajuan Berhasil (Muncul Otomatis Saat Ada session('success')) -->
        @if(session('success'))
            <div x-data="{ open: true }" 
                 x-show="open" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                
                <div @click.away="open = false" 
                     x-show="open"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-100 text-center space-y-4 my-8">
                    
                    <!-- Animated Check Icon -->
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto text-3xl shadow-sm ring-8 ring-emerald-50/60 font-black">
                        ✓
                    </div>

                    <div>
                        <h3 class="text-lg sm:text-xl font-extrabold text-slate-800 tracking-tight">Pengajuan Berhasil Terkirim!</h3>
                        <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                            {{ session('success') }}
                        </p>
                    </div>

                    <!-- Kartu Ringkasan Informasi -->
                    <div class="bg-blue-50/60 p-4 rounded-2xl border border-blue-100 text-left text-xs text-slate-600 space-y-1.5">
                        <p class="font-bold text-blue-900 flex items-center gap-1.5">
                            <span>📋</span> Informasi Pengajuan:
                        </p>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Pengajuan / laporan Anda telah otomatis tercatat ke <strong>Halaman Pengaduan Pengurus RT</strong>. Pengurus RT akan meninjau dan menindaklanjuti permohonan Anda segera.
                        </p>
                    </div>

                    <button type="button" @click="open = false" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-md shadow-blue-200">
                        Oke, Saya Mengerti
                    </button>
                </div>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} E-KAS RT. Transparansi Keuangan & Layanan Publik Warga.
    </footer>

</body>
</html>