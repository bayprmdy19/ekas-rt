<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lengkapi Data Diri Warga - {{ config('app.name', 'E-KAS RT') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-gradient-to-br from-slate-50 via-slate-100 to-blue-50/40 min-h-full py-8 px-4 sm:px-6 lg:px-8">

    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Top Navigation Bar: Info Akun & Tombol Logout -->
        <header class="bg-white/90 backdrop-blur border border-slate-200 rounded-3xl p-4 sm:px-6 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                    RT
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 leading-none">Portal Warga Digital</h1>
                    <p class="text-xs text-slate-500 mt-1">Akun: <span class="font-semibold text-slate-700">{{ $user->name }}</span> ({{ $user->no_hp ?: $user->username }})</p>
                </div>
            </div>

            <!-- Tombol Logout -->
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition shadow-2xs cursor-pointer">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar / Logout</span>
                </button>
            </form>
        </header>

        <!-- Flash & Alert Messages -->
        @if (session('warning'))
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-800 font-semibold flex items-center gap-3 shadow-2xs">
                <span class="text-base">⚠️</span>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 shadow-2xs space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <span>⛔</span>
                    <span>Terdapat beberapa isian yang belum sesuai:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 pl-4 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Onboarding Card Form -->
        <main class="bg-white border border-slate-200/90 rounded-3xl shadow-sm p-6 sm:p-8">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                    Onboarding Warga Baru
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-2.5">
                    Lengkapi Data Diri & Domisili
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Untuk menjamin keakuratan sensus warga dan administrasi surat-menyurat RT, mohon lengkapi formulir di bawah ini dengan data yang sebenarnya.
                </p>
            </div>

            <form action="{{ route('warga.onboarding.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="onboarding-form">
                @csrf

                <!-- SEKSI 1: DATA DASAR WARGA -->
                <section class="space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">1</div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">Data Diri Dasar & Domisili</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tempat Lahir -->
                        <div>
                            <label for="tempat_lahir" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Tempat Lahir <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" required
                                value="{{ old('tempat_lahir', $warga?->tempat_lahir) }}"
                                placeholder="Contoh: Bandung"
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="tanggal_lahir" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Tanggal Lahir <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir" required
                                value="{{ old('tanggal_lahir', $warga?->tanggal_lahir?->format('Y-m-d')) }}"
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Status Domisili -->
                        <div>
                            <label for="status_domisili" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Status Domisili <span class="text-rose-500">*</span>
                            </label>
                            <select id="status_domisili" name="status_domisili" required
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                                <option value="" disabled {{ old('status_domisili', $warga?->status_tinggal) ? '' : 'selected' }}>-- Pilih Status Domisili --</option>
                                <option value="tetap" {{ old('status_domisili', $warga?->status_tinggal) === 'tetap' ? 'selected' : '' }}>Tetap (Warga Permanen)</option>
                                <option value="kontrak" {{ old('status_domisili', $warga?->status_tinggal) === 'kontrak' ? 'selected' : '' }}>Kontrak / Kos</option>
                            </select>
                        </div>

                        <!-- Blok / No. Rumah -->
                        <div>
                            <label for="nomor_rumah" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Blok / No. Rumah <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nomor_rumah" name="nomor_rumah" required
                                value="{{ old('nomor_rumah', $warga?->nomor_rumah) }}"
                                placeholder="Contoh: Blok A No. 12"
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" />
                        </div>
                    </div>

                    <!-- Pertanyaan: Apakah Anda Berkeluarga? -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Apakah Anda Berkeluarga? <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3 max-w-sm">
                            <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40">
                                <input type="radio" name="is_berkeluarga" value="1" id="berkeluarga_ya"
                                    {{ old('is_berkeluarga', $warga?->is_berkeluarga) === '1' || old('is_berkeluarga', $warga?->is_berkeluarga) === 1 || old('is_berkeluarga', $warga?->is_berkeluarga) === true ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500" />
                                <span class="text-xs font-bold text-slate-800">Ya, Berkeluarga</span>
                            </label>
                            <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition border-slate-200 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40">
                                <input type="radio" name="is_berkeluarga" value="0" id="berkeluarga_tidak"
                                    {{ (old('is_berkeluarga', $warga?->is_berkeluarga) === '0' || old('is_berkeluarga', $warga?->is_berkeluarga) === 0 || (!old('is_berkeluarga') && !$warga?->is_berkeluarga)) ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500" />
                                <span class="text-xs font-bold text-slate-800">Tidak (Lajang/Sendiri)</span>
                            </label>
                        </div>
                        <p class="text-2xs text-slate-500 mt-1.5">Pilih "Ya" jika Anda telah berkeluarga dan memiliki Nomor KK untuk pencatatan kepala keluarga.</p>
                    </div>
                </section>

                <!-- SEKSI 2: DATA KELUARGA (DINAMIS via JAVASCRIPT) -->
                <section id="section-keluarga" class="space-y-4 pt-4 border-t border-slate-100 hidden">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center">2</div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">Identitas Kartu Keluarga & Dokumen</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- NIK (16 Digit) -->
                        <div>
                            <label for="nik" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nik" name="nik" inputmode="numeric" maxlength="16"
                                value="{{ old('nik', $warga?->nik ?: $kk?->nik_kepala_keluarga) }}"
                                placeholder="16 digit angka NIK KTP Anda"
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition" />
                            <p class="text-2xs text-slate-500 mt-1">Wajib 16 digit angka sesuai e-KTP.</p>
                        </div>

                        <!-- Nomor KK (Wajib 16 Digit) -->
                        <div>
                            <label for="no_kk" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor Kartu Keluarga (KK) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="no_kk" name="no_kk" inputmode="numeric" maxlength="16"
                                value="{{ old('no_kk', $warga?->no_kk ?: $kk?->nomor_kk) }}"
                                placeholder="16 digit angka Nomor KK"
                                class="w-full text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition" />
                            <p class="text-2xs text-slate-500 mt-1">Wajib 16 digit angka. Nantinya dapat digunakan untuk login ke portal.</p>
                        </div>
                    </div>

                    <!-- Upload Softcopy KTP & KK (Storage Private yang Aman) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- Softcopy KTP -->
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                            <label for="file_ktp" class="block text-xs font-bold text-slate-800 mb-1">
                                Upload Softcopy KTP <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-2xs text-slate-500 mb-2">Format: JPG, PNG, atau PDF (Maks. 5MB). File dienkripsi & disimpan di storage private aman.</p>
                            <input type="file" id="file_ktp" name="file_ktp" accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
                            @if ($warga?->file_ktp)
                                <p class="text-2xs text-emerald-600 font-semibold mt-1.5">✓ Softcopy KTP sudah tersimpan sebelumnya.</p>
                            @endif
                        </div>

                        <!-- Softcopy KK -->
                        <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80">
                            <label for="file_kk" class="block text-xs font-bold text-slate-800 mb-1">
                                Upload Softcopy Kartu Keluarga (KK) <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-2xs text-slate-500 mb-2">Format: JPG, PNG, atau PDF (Maks. 5MB). File dienkripsi & disimpan di storage private aman.</p>
                            <input type="file" id="file_kk" name="file_kk" accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
                            @if ($warga?->file_kk)
                                <p class="text-2xs text-emerald-600 font-semibold mt-1.5">✓ Softcopy KK sudah tersimpan sebelumnya.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Pertanyaan: Apakah Memiliki Anak? -->
                    <div class="pt-3 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Apakah Anda Memiliki Anak? <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3 max-w-sm">
                            <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition border-slate-200 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/40">
                                <input type="radio" name="memiliki_anak" value="1" id="anak_ya"
                                    {{ old('memiliki_anak', $warga?->memiliki_anak) === '1' || old('memiliki_anak', $warga?->memiliki_anak) === 1 || old('memiliki_anak', $warga?->memiliki_anak) === true ? 'checked' : '' }}
                                    class="text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-xs font-bold text-slate-800">Ya, Memiliki Anak</span>
                            </label>
                            <label class="flex items-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-slate-50 transition border-slate-200 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/40">
                                <input type="radio" name="memiliki_anak" value="0" id="anak_tidak"
                                    {{ (old('memiliki_anak', $warga?->memiliki_anak) === '0' || old('memiliki_anak', $warga?->memiliki_anak) === 0 || (!old('memiliki_anak') && !$warga?->memiliki_anak)) ? 'checked' : '' }}
                                    class="text-indigo-600 focus:ring-indigo-500" />
                                <span class="text-xs font-bold text-slate-800">Tidak Memiliki Anak</span>
                            </label>
                        </div>
                    </div>
                </section>

                <!-- SEKSI 3: DATA ANAK (DINAMIS via JAVASCRIPT) -->
                <section id="section-anak" class="space-y-4 pt-4 border-t border-slate-100 hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-emerald-600 text-white text-xs font-bold flex items-center justify-center">3</div>
                            <h3 class="text-sm font-bold text-slate-900 tracking-wide uppercase">Daftar Data Anak</h3>
                        </div>
                        <button type="button" id="btn-tambah-anak"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-300 rounded-xl hover:bg-emerald-100 transition shadow-2xs cursor-pointer self-start sm:self-auto">
                            <span>➕</span>
                            <span>Tambah Data Anak</span>
                        </button>
                    </div>

                    <p class="text-2xs text-slate-500">
                        Klik tombol <strong>"Tambah Data Anak"</strong> untuk memasukkan informasi setiap anak (nama, umur, tempat & tgl lahir).
                    </p>

                    <!-- Container Kartu Input Anak Dinamis -->
                    <div id="container-anak" class="space-y-3">
                        <!-- Kartu anak akan digenerate dinamis melalui JS di sini -->
                    </div>
                </section>

                <!-- TOMBOL SUBMIT PENYELESAIAN ONBOARDING -->
                <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-500">
                        Pastikan semua data sudah diisi dengan benar sebelum menyelesaikan pendaftaran.
                    </p>
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition cursor-pointer">
                        <span>Simpan Data Profil & Masuk ke Dashboard</span>
                        <span>→</span>
                    </button>
                </div>
            </form>
        </main>
    </div>

    <!-- TEMPLATE ROW/CARD ANAK UNTUK JAVASCRIPT -->
    <template id="template-anak">
        <div class="anak-card p-4 rounded-2xl border border-slate-200 bg-slate-50/60 relative transition hover:border-slate-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-700 anak-badge">Data Anak #<span class="anak-num">1</span></span>
                <button type="button" class="btn-hapus-anak text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg border border-rose-200 transition cursor-pointer">
                    ✕ Hapus
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-2xs font-bold text-slate-600 mb-1">Nama Lengkap Anak *</label>
                    <input type="text" name="anak[__INDEX__][nama]" required placeholder="Nama lengkap anak"
                        class="w-full text-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-600" />
                </div>
                <div>
                    <label class="block text-2xs font-bold text-slate-600 mb-1">Umur (Tahun) *</label>
                    <input type="number" min="0" max="100" name="anak[__INDEX__][umur]" required placeholder="Contoh: 7"
                        class="w-full text-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-600" />
                </div>
                <div>
                    <label class="block text-2xs font-bold text-slate-600 mb-1">Tempat Lahir</label>
                    <input type="text" name="anak[__INDEX__][tempat_lahir]" placeholder="Kota lahir"
                        class="w-full text-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-600" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-2xs font-bold text-slate-600 mb-1">Tanggal Lahir</label>
                    <input type="date" name="anak[__INDEX__][tanggal_lahir]"
                        class="w-full text-xs rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-800 focus:outline-none focus:ring-1 focus:ring-emerald-600" />
                </div>
            </div>
        </div>
    </template>

    <!-- LOGIKA JAVASCRIPT FORM DINAMIS ONBOARDING -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radioBerkeluargaYa = document.getElementById('berkeluarga_ya');
            const radioBerkeluargaTidak = document.getElementById('berkeluarga_tidak');
            const sectionKeluarga = document.getElementById('section-keluarga');

            const radioAnakYa = document.getElementById('anak_ya');
            const radioAnakTidak = document.getElementById('anak_tidak');
            const sectionAnak = document.getElementById('section-anak');

            const containerAnak = document.getElementById('container-anak');
            const btnTambahAnak = document.getElementById('btn-tambah-anak');
            const templateAnak = document.getElementById('template-anak');

            const inputNik = document.getElementById('nik');
            const inputNoKk = document.getElementById('no_kk');

            // 1. Filter numerik & batasan digit untuk NIK dan Nomor KK
            function sanitizeNumeric(el, maxLen) {
                if (!el) return;
                el.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '').slice(0, maxLen);
                });
            }
            sanitizeNumeric(inputNik, 16);
            sanitizeNumeric(inputNoKk, 16);

            // 2. Toggle visibilitas seksi Berkeluarga
            function updateBerkeluargaVisibility() {
                const isBerkeluarga = radioBerkeluargaYa && radioBerkeluargaYa.checked;
                if (isBerkeluarga) {
                    sectionKeluarga.classList.remove('hidden');
                    if (inputNik) inputNik.setAttribute('required', 'required');
                    if (inputNoKk) inputNoKk.setAttribute('required', 'required');
                } else {
                    sectionKeluarga.classList.add('hidden');
                    if (inputNik) inputNik.removeAttribute('required');
                    if (inputNoKk) inputNoKk.removeAttribute('required');

                    // Jika tidak berkeluarga, sembunyikan juga seksi anak
                    sectionAnak.classList.add('hidden');
                }
                updateAnakVisibility();
            }

            // 3. Toggle visibilitas seksi Anak
            function updateAnakVisibility() {
                const isBerkeluarga = radioBerkeluargaYa && radioBerkeluargaYa.checked;
                const memilikiAnak = radioAnakYa && radioAnakYa.checked;

                if (isBerkeluarga && memilikiAnak) {
                    sectionAnak.classList.remove('hidden');
                    if (containerAnak.children.length === 0) {
                        tambahKartuAnak();
                    }
                } else {
                    sectionAnak.classList.add('hidden');
                }
            }

            // 4. Tambah Kartu Input Anak Baru
            let anakIndex = 0;
            function tambahKartuAnak(data = null) {
                const clone = templateAnak.content.cloneNode(true);
                const card = clone.querySelector('.anak-card');

                // Update index field name
                card.innerHTML = card.innerHTML.replace(/__INDEX__/g, anakIndex);

                containerAnak.appendChild(card);
                anakIndex++;
                updateAnakNumbers();

                // Isi data awal jika ada (misal dari old/database)
                if (data) {
                    const namaInput = card.querySelector(`input[name="anak[${anakIndex - 1}][nama]"]`);
                    const umurInput = card.querySelector(`input[name="anak[${anakIndex - 1}][umur]"]`);
                    const tempatInput = card.querySelector(`input[name="anak[${anakIndex - 1}][tempat_lahir]"]`);
                    const tanggalInput = card.querySelector(`input[name="anak[${anakIndex - 1}][tanggal_lahir]"]`);

                    if (namaInput && data.nama) namaInput.value = data.nama;
                    if (umurInput && data.umur) umurInput.value = data.umur;
                    if (tempatInput && data.tempat_lahir) tempatInput.value = data.tempat_lahir;
                    if (tanggalInput && data.tanggal_lahir) tanggalInput.value = data.tanggal_lahir;
                }
            }

            // 5. Update penomoran kartu anak
            function updateAnakNumbers() {
                const cards = containerAnak.querySelectorAll('.anak-card');
                cards.forEach((c, idx) => {
                    const numSpan = c.querySelector('.anak-num');
                    if (numSpan) numSpan.textContent = idx + 1;
                });
            }

            // 6. Event listener Hapus Anak (delegated)
            containerAnak.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('btn-hapus-anak')) {
                    const card = e.target.closest('.anak-card');
                    if (card) {
                        card.remove();
                        updateAnakNumbers();
                    }
                }
            });

            // Event Listeners Radio
            if (radioBerkeluargaYa) radioBerkeluargaYa.addEventListener('change', updateBerkeluargaVisibility);
            if (radioBerkeluargaTidak) radioBerkeluargaTidak.addEventListener('change', updateBerkeluargaVisibility);
            if (radioAnakYa) radioAnakYa.addEventListener('change', updateAnakVisibility);
            if (radioAnakTidak) radioAnakTidak.addEventListener('change', updateAnakVisibility);

            if (btnTambahAnak) {
                btnTambahAnak.addEventListener('click', function () {
                    tambahKartuAnak();
                });
            }

            // Inisialisasi awal saat halaman dimuat
            @if (old('anak'))
                const oldAnakData = @json(old('anak'));
                if (Array.isArray(oldAnakData)) {
                    oldAnakData.forEach(item => {
                        tambahKartuAnak(item);
                    });
                }
            @elseif(isset($anaks) && count($anaks) > 0)
                const savedAnakData = @json($anaks);
                savedAnakData.forEach(item => {
                    tambahKartuAnak({
                        nama: item.nama_anak,
                        umur: item.umur,
                        tempat_lahir: item.tempat_lahir,
                        tanggal_lahir: item.tanggal_lahir ? item.tanggal_lahir.substring(0, 10) : ''
                    });
                });
            @endif

            updateBerkeluargaVisibility();
        });
    </script>
</body>
</html>

