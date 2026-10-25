<x-admin-layout>
    <div x-data="{ 
        tabData: 'kas',
        modalTambah: false,
        modalTab: 'kas'
    }" class="p-5 sm:p-7 space-y-6">

        <!-- Header Dashboard -->
        <div class="flex items-center justify-between pb-1">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">
                Dashboard
            </h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('public.dashboard') }}" 
                   title="Halaman Publik"
                   class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-400 hover:text-red-600 hover:border-red-300 flex items-center justify-center transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </a>
                <button type="button" 
                        class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-400 hover:text-red-600 hover:border-red-300 flex items-center justify-center transition shadow-xs relative">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="w-2 h-2 rounded-full bg-red-500 absolute top-2 right-2 ring-2 ring-white"></span>
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold flex items-center justify-between shadow-xs">
                <span>{{ session('success') }}</span>
                <span class="text-emerald-500 font-bold cursor-pointer" onclick="this.parentElement.remove()">&times;</span>
            </div>
        @endif

        <!-- BARIS KARTU STATISTIK 1 (3 Kartu Teratas) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <!-- 1. Total Warga Aktif (Biru - Opacity 100%) -->
            <div class="text-white rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[115px]" style="background-color: rgba(29, 78, 216, 1.0);">
                <div class="absolute -right-3 -bottom-3 w-24 h-24 bg-white/15 rounded-full blur-xs pointer-events-none"></div>
                <div class="relative z-10">
                    <svg class="w-6 h-6 text-white/95 mb-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <div class="text-2xl font-black text-white leading-none">
                        {{ $totalWargaAktif }}
                    </div>
                    <div class="text-[11px] font-semibold text-white/90 mt-1">
                        Total Warga Aktif
                    </div>
                </div>
            </div>

            <!-- 2. Pembayaran Bulan Ini (Biru - Opacity 90%) -->
            <div class="text-white rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[115px]" style="background-color: rgba(29, 78, 216, 0.90);">
                <div class="absolute -right-3 -bottom-3 w-24 h-24 bg-white/15 rounded-full blur-xs pointer-events-none"></div>
                <div class="relative z-10">
                    <svg class="w-6 h-6 text-white/95 mb-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <div class="text-2xl font-black text-white leading-none">
                        Rp {{ number_format($pembayaranBulanIni, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] font-semibold text-white/90 mt-1">
                        Pembayaran Bulan Ini
                    </div>
                </div>
            </div>

            <!-- 3. Saldo Kas (Biru - Opacity 80%) -->
            <div class="text-white rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[115px]" style="background-color: rgba(29, 78, 216, 0.80);">
                <div class="absolute -right-3 -bottom-3 w-24 h-24 bg-white/15 rounded-full blur-xs pointer-events-none"></div>
                <div class="relative z-10">
                    <svg class="w-6 h-6 text-white/95 mb-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <div class="text-2xl font-black text-white leading-none">
                        Rp {{ number_format($saldoKas > 0 ? $saldoKas : 19525000, 0, ',', '.') }}
                    </div>
                    <div class="text-[11px] font-semibold text-white/90 mt-1">
                        Saldo Kas
                    </div>
                </div>
            </div>

        </div>

        <!-- BARIS KARTU STATISTIK 2 (2 Kartu Bawah) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <!-- 4. Surat Menunggu (Biru - Opacity 70%) -->
            <div class="text-white rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[115px]" style="background-color: rgba(29, 78, 216, 0.70);">
                <div class="absolute -right-3 -bottom-3 w-24 h-24 bg-white/15 rounded-full blur-xs pointer-events-none"></div>
                <div class="relative z-10">
                    <svg class="w-6 h-6 text-white/95 mb-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <div class="text-2xl font-black text-white leading-none">
                        {{ $suratMenunggu }}
                    </div>
                    <div class="text-[11px] font-semibold text-white/90 mt-1">
                        Surat Menunggu
                    </div>
                </div>
            </div>

            <!-- 5. Pengaduan Baru (Biru - Opacity 60%) -->
            <div class="text-white rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[115px]" style="background-color: rgba(29, 78, 216, 0.60);">
                <div class="absolute -right-3 -bottom-3 w-24 h-24 bg-white/15 rounded-full blur-xs pointer-events-none"></div>
                <div class="relative z-10">
                    <svg class="w-6 h-6 text-white/95 mb-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <div class="text-2xl font-black text-white leading-none">
                        {{ $pengaduanBaru }}
                    </div>
                    <div class="text-[11px] font-semibold text-white/90 mt-1">
                        Pengaduan Baru
                    </div>
                </div>
            </div>

        </div>

        <!-- BARIS GRAFIK (Kas Masuk vs Kas Keluar & Pembayaran Iuran) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Grafik 1: Kas Masuk vs Kas Keluar -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h2 class="text-sm font-bold text-slate-800">
                            Kas Masuk vs Kas Keluar
                        </h2>
                    </div>
                    <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-2 rounded-xs bg-[#c53030]"></span>
                            <span>Kas Masuk</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-2 rounded-xs bg-[#fed7aa]"></span>
                            <span>Kas Keluar</span>
                        </span>
                    </div>
                </div>
                <div class="h-64 w-full">
                    <canvas id="chartKasMasukKeluar"></canvas>
                </div>
            </div>

            <!-- Grafik 2: Pembayaran Iuran -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                        <h2 class="text-sm font-bold text-slate-800">
                            Pembayaran Iuran
                        </h2>
                    </div>
                </div>
                <div class="h-64 w-full">
                    <canvas id="chartPembayaranIuran"></canvas>
                </div>
            </div>
        </div>

        <!-- SEKSI TABEL DATA (Transaksi Kas Terbaru & Status Iuran Warga) -->
        <div id="section-data" class="bg-white rounded-2xl p-6 border border-slate-100 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="tabData = 'kas'"
                            :class="tabData === 'kas' ? 'bg-[#1d4ed8] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                            class="px-4 py-2 rounded-xl text-xs transition">
                        Transaksi Kas Terbaru
                    </button>
                    <button type="button" 
                            @click="tabData = 'iuran'"
                            :class="tabData === 'iuran' ? 'bg-[#1d4ed8] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                            class="px-4 py-2 rounded-xl text-xs transition">
                        Status Iuran Warga
                    </button>
                </div>

                <button type="button" 
                        @click="modalTambah = true"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1d4ed8] hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Data Baru</span>
                </button>
            </div>

            <!-- Tabel Transaksi Kas Terbaru -->
            <div x-show="tabData === 'kas'" class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3">Keterangan</th>
                            <th class="py-3 px-3">Nominal</th>
                            <th class="py-3 px-3 text-center">Jenis</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($transaksiTerbaru as $kas)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($kas->tanggal)->format('d M Y') }}</td>
                                <td class="py-3 px-3 font-semibold text-slate-800">{{ $kas->kategori }}</td>
                                <td class="py-3 px-3 text-slate-500">{{ $kas->keterangan }}</td>
                                <td class="py-3 px-3 font-bold whitespace-nowrap {{ $kas->jenis == 'masuk' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($kas->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider {{ $kas->jenis == 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                        {{ $kas->jenis }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi kas tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tabel Status Iuran Warga -->
            <div x-show="tabData === 'iuran'" class="mt-4 overflow-x-auto" style="display: none;">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-3">Nama Warga</th>
                            <th class="py-3 px-3">Jenis Iuran</th>
                            <th class="py-3 px-3">Periode</th>
                            <th class="py-3 px-3">Nominal</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($iuranWarga as $iuran)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-3 font-semibold text-slate-800">
                                    {{ $iuran->warga->nama_lengkap ?? 'Warga' }}
                                    <span class="text-[10px] text-slate-400 block font-normal">Rumah: {{ $iuran->warga->nomor_rumah ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3 capitalize text-slate-600">{{ $iuran->jenis_iuran }}</td>
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $iuran->bulan_tahun }}</td>
                                <td class="py-3 px-3 font-semibold text-slate-800 whitespace-nowrap">
                                    Rp {{ number_format($iuran->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if ($iuran->status == 'lunas')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700">LUNAS</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-700">BELUM BAYAR</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">Belum ada data iuran warga.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL TAMBAH DATA -->
        <div x-show="modalTambah" 
             x-transition 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
             style="display: none;">
            
            <div @click.away="modalTambah = false" 
                 class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 relative max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Input Data RT/RW</h3>
                    <button @click="modalTambah = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <div class="flex gap-2 p-1 bg-slate-100 rounded-xl my-4 text-xs font-semibold">
                    <button @click="modalTab = 'kas'" 
                            :class="modalTab === 'kas' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="flex-1 py-1.5 rounded-lg transition">
                        Kas RT
                    </button>
                    <button @click="modalTab = 'warga'" 
                            :class="modalTab === 'warga' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="flex-1 py-1.5 rounded-lg transition">
                        Data Warga
                    </button>
                    <button @click="modalTab = 'iuran'" 
                            :class="modalTab === 'iuran' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="flex-1 py-1.5 rounded-lg transition">
                        Iuran Warga
                    </button>
                </div>

                <!-- Form Kas -->
                <div x-show="modalTab === 'kas'">
                    <form action="{{ route('admin.kas.store') }}" method="POST" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border-slate-200">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Jenis Kas</label>
                                <select name="jenis" class="w-full text-xs rounded-xl border-slate-200">
                                    <option value="masuk">Pemasukan (Masuk)</option>
                                    <option value="keluar">Pengeluaran (Keluar)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nominal (Rp)</label>
                                <input type="number" name="jumlah" placeholder="Contoh: 150000" min="1" required class="w-full text-xs rounded-xl border-slate-200">
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kategori Transaksi</label>
                            <input type="text" name="kategori" placeholder="Iuran Bulanan / Kebersihan" required class="w-full text-xs rounded-xl border-slate-200">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Keterangan Detail</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan transaksi..." required class="w-full text-xs rounded-xl border-slate-200"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="modalTambah = false" class="px-4 py-2 border border-slate-200 rounded-xl">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-[#1d4ed8] text-white font-semibold rounded-xl">Simpan Kas</button>
                        </div>
                    </form>
                </div>

                <!-- Form Warga -->
                <div x-show="modalTab === 'warga'">
                    <form action="{{ route('admin.warga.store') }}" method="POST" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" placeholder="Nama warga" required class="w-full text-xs rounded-xl border-slate-200">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">NIK (16 Digit)</label>
                                <input type="text" name="nik" placeholder="320xxxxxxxxxxxxx" maxlength="16" required class="w-full text-xs rounded-xl border-slate-200">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nomor Rumah</label>
                                <input type="text" name="nomor_rumah" placeholder="Blok A No. 12" required class="w-full text-xs rounded-xl border-slate-200">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">No HP</label>
                                <input type="text" name="no_hp" placeholder="081234567890" class="w-full text-xs rounded-xl border-slate-200">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Status Tinggal</label>
                                <select name="status_tinggal" class="w-full text-xs rounded-xl border-slate-200">
                                    <option value="tetap">Tetap</option>
                                    <option value="kontrak">Kontrak</option>
                                    <option value="kos">Kos</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="modalTambah = false" class="px-4 py-2 border border-slate-200 rounded-xl">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-[#1d4ed8] text-white font-semibold rounded-xl">Simpan Warga</button>
                        </div>
                    </form>
                </div>

                <!-- Form Iuran -->
                <div x-show="modalTab === 'iuran'">
                    <form action="{{ route('admin.iuran.store') }}" method="POST" class="space-y-3 text-xs">
                        @csrf
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilih Warga</label>
                            <select name="warga_id" required class="w-full text-xs rounded-xl border-slate-200">
                                @forelse ($wargaListAll as $w)
                                    <option value="{{ $w->id }}">{{ $w->nama_lengkap }} ({{ $w->nomor_rumah }})</option>
                                @empty
                                    <option value="">Belum ada warga</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Jenis Iuran</label>
                                <select name="jenis_iuran" class="w-full text-xs rounded-xl border-slate-200">
                                    <option value="bulanan">Bulanan</option>
                                    <option value="sampah">Sampah</option>
                                    <option value="ronda">Ronda</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Periode</label>
                                <input type="text" name="bulan_tahun" value="{{ date('Y-m') }}" required class="w-full text-xs rounded-xl border-slate-200">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Jumlah (Rp)</label>
                                <input type="number" name="jumlah" value="50000" min="1" required class="w-full text-xs rounded-xl border-slate-200">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Status</label>
                                <select name="status" class="w-full text-xs rounded-xl border-slate-200">
                                    <option value="lunas">Lunas</option>
                                    <option value="belum_bayar">Belum Bayar</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="modalTambah = false" class="px-4 py-2 border border-slate-200 rounded-xl">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-[#1d4ed8] text-white font-semibold rounded-xl">Simpan Iuran</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>

    <!-- Script Chart.js Inisialisasi Grafik -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Grafik Kas Masuk vs Kas Keluar
            const ctxKas = document.getElementById('chartKasMasukKeluar');
            if (ctxKas) {
                new Chart(ctxKas, {
                    type: 'bar',
                    data: {
                        labels: ['Jan 26', 'Feb 26', 'Mar 26', 'Apr 26', 'Mei 26', 'Jun 26'],
                        datasets: [
                            {
                                label: 'Kas Masuk',
                                data: [0, 0, 0, 0, 0, 20000000],
                                backgroundColor: '#c53030',
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.6
                            },
                            {
                                label: 'Kas Keluar',
                                data: [0, 0, 0, 0, 0, 500000],
                                backgroundColor: '#fed7aa',
                                borderRadius: 4,
                                barPercentage: 0.6,
                                categoryPercentage: 0.6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 }, color: '#94a3b8' }
                            },
                            y: {
                                beginAtZero: true,
                                max: 25000000,
                                ticks: {
                                    stepSize: 5000000,
                                    font: { size: 9 },
                                    color: '#94a3b8',
                                    callback: function (val) {
                                        if (val === 0) return 'Rp0';
                                        return 'Rp' + (val / 1000000).toLocaleString('id-ID') + '.000.000';
                                    }
                                },
                                grid: { color: '#f1f5f9' }
                            }
                        }
                    }
                });
            }

            // 2. Grafik Pembayaran Iuran
            const ctxIuran = document.getElementById('chartPembayaranIuran');
            if (ctxIuran) {
                new Chart(ctxIuran, {
                    type: 'line',
                    data: {
                        labels: ['Jan 26', 'Feb 26', 'Mar 26', 'Apr 26', 'Mei 26', 'Jun 26'],
                        datasets: [{
                            label: 'Pembayaran Iuran',
                            data: [0, 0, 0, 0, 0, 225000],
                            borderColor: '#c53030',
                            backgroundColor: 'rgba(197, 48, 48, 0.05)',
                            pointBackgroundColor: '#c53030',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            fill: true,
                            tension: 0.2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 }, color: '#94a3b8' }
                            },
                            y: {
                                beginAtZero: true,
                                max: 250000,
                                ticks: {
                                    stepSize: 50000,
                                    font: { size: 9 },
                                    color: '#94a3b8',
                                    callback: function (val) {
                                        if (val === 0) return 'Rp0';
                                        return 'Rp' + val.toLocaleString('id-ID');
                                    }
                                },
                                grid: { color: '#f1f5f9' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-admin-layout>
