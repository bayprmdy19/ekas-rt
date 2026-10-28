<x-admin-layout>
    <div x-data="{ 
        tabData: 'kas',
        modalTambah: false,
        modalTab: 'kas',
        suratMenunggu: {{ $suratMenunggu }},
        pengaduanBaru: {{ $pengaduanBaru }},
        toastNotification: null,
        toastTimer: null,
        showToast(message, type = 'info') {
            this.toastNotification = { message, type };
            if (this.toastTimer) clearTimeout(this.toastTimer);
            this.toastTimer = setTimeout(() => { this.toastNotification = null; }, 6000);
        },
        init() {
            // Polling sinkronisasi real-time setiap 3.5 detik
            setInterval(() => {
                fetch('{{ route('admin.realtime-stats') }}', {
                    headers: { 'Accept': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.surat_menunggu !== undefined) {
                            if (data.surat_menunggu > this.suratMenunggu) {
                                const pelapor = data.latest_surat?.nama_pengaju ? ` dari ${data.latest_surat.nama_pengaju}` : '';
                                const jenis = data.latest_surat?.jenis_surat ? ` (${data.latest_surat.jenis_surat})` : '';
                                this.showToast(`📄 Pengajuan surat baru masuk${jenis}${pelapor}!`, 'surat');
                            }
                            this.suratMenunggu = data.surat_menunggu;
                        }

                        if (data.pengaduan_baru !== undefined) {
                            if (data.pengaduan_baru > this.pengaduanBaru) {
                                const pelapor = data.latest_pengaduan?.nama_pelapor ? ` dari ${data.latest_pengaduan.nama_pelapor}` : '';
                                const judul = data.latest_pengaduan?.judul ? ` (${data.latest_pengaduan.judul})` : '';
                                this.showToast(`📣 Laporan aduan warga baru diterima${judul}${pelapor}!`, 'aduan');
                            }
                            this.pengaduanBaru = data.pengaduan_baru;
                        }
                    }
                })
                .catch(() => {});
            }, 3500);
        }
    }" class="p-5 sm:p-7 space-y-6 relative">

        <!-- Header Dashboard -->
        <div class="flex items-center justify-between pb-1">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">
                Dashboard
            </h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('public.dashboard') }}" 
                   title="Lihat Website"
                   class="h-9 px-3 sm:px-3.5 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:text-blue-600 hover:border-blue-300 inline-flex items-center gap-1.5 sm:gap-2 text-xs font-semibold transition shadow-xs shrink-0 group">
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span>Lihat Website</span>
                </a>
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

        <!-- BARIS KARTU STATISTIK 2 (2 Kartu Bawah: Sinkronisasi Real-Time) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <!-- 4. Surat Menunggu (Biru - Opacity 70%) -->
            <a href="{{ route('admin.surat.index') }}" 
               class="text-white rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[115px] hover:opacity-95 transition group" 
               style="background-color: rgba(29, 78, 216, 0.70);">
                <div class="absolute -right-3 -bottom-3 w-24 h-24 bg-white/15 rounded-full blur-xs pointer-events-none group-hover:scale-110 transition duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2.5">
                        <svg class="w-6 h-6 text-white/95" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-[10px] font-bold text-white tracking-wide shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-300 animate-pulse"></span>
                            <span>Live Sync</span>
                        </span>
                    </div>
                    <div class="text-2xl font-black text-white leading-none flex items-baseline gap-2">
                        <span x-text="suratMenunggu">{{ $suratMenunggu }}</span>
                        <span class="text-xs font-normal text-white/70">berkas</span>
                    </div>
                    <div class="text-[11px] font-semibold text-white/90 mt-1 flex items-center justify-between">
                        <span>Surat Menunggu</span>
                        <span class="text-[10px] text-white/80 group-hover:translate-x-0.5 transition inline-flex items-center gap-1 font-bold">
                            Kelola Surat &rarr;
                        </span>
                    </div>
                </div>
            </a>

            <!-- 5. Pengaduan Baru (Biru - Opacity 60%) -->
            <a href="{{ route('admin.pengaduan.index') }}" 
               class="text-white rounded-2xl p-5 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[115px] hover:opacity-95 transition group" 
               style="background-color: rgba(29, 78, 216, 0.60);">
                <div class="absolute -right-3 -bottom-3 w-24 h-24 bg-white/15 rounded-full blur-xs pointer-events-none group-hover:scale-110 transition duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2.5">
                        <svg class="w-6 h-6 text-white/95" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/20 text-[10px] font-bold text-white tracking-wide shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-300 animate-pulse"></span>
                            <span>Live Sync</span>
                        </span>
                    </div>
                    <div class="text-2xl font-black text-white leading-none flex items-baseline gap-2">
                        <span x-text="pengaduanBaru">{{ $pengaduanBaru }}</span>
                        <span class="text-xs font-normal text-white/70">aduan</span>
                    </div>
                    <div class="text-[11px] font-semibold text-white/90 mt-1 flex items-center justify-between">
                        <span>Pengaduan Baru</span>
                        <span class="text-[10px] text-white/80 group-hover:translate-x-0.5 transition inline-flex items-center gap-1 font-bold">
                            Kelola Aduan &rarr;
                        </span>
                    </div>
                </div>
            </a>

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
                    <button type="button" 
                            @click="tabData = 'surat'"
                            :class="tabData === 'surat' ? 'bg-[#1d4ed8] text-white shadow-xs font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                            class="px-4 py-2 rounded-xl text-xs transition">
                        List Pengajuan Surat
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
                                    {{ $iuran->warga ? $iuran->warga->nama_lengkap : 'Warga Telah Dihapus' }}
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

            <!-- Tabel List Pengajuan & List Surat -->
            <div x-show="tabData === 'surat'" class="mt-4 overflow-x-auto" style="display: none;">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Nama Pemohon</th>
                            <th class="py-3 px-3">WhatsApp</th>
                            <th class="py-3 px-3">Jenis Surat</th>
                            <th class="py-3 px-3">Keperluan / Keterangan</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($suratTerbaru as $surat)
                            @php
                                $pemohonDisplay = $surat->warga ? $surat->warga->nama_lengkap : ($surat->warga_id === null ? 'Warga Telah Dihapus' : ($surat->nama_pengaju ?: 'Warga Telah Dihapus'));
                                $waDisplay = $surat->nomor_whatsapp ?: ($surat->warga?->no_hp ?: '-');
                            @endphp
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-3 text-slate-500 whitespace-nowrap">{{ $surat->created_at ? $surat->created_at->format('d M Y') : '-' }}</td>
                                <td class="py-3 px-3 font-semibold text-slate-800">
                                    {{ $pemohonDisplay }}
                                    <span class="text-[10px] text-slate-400 block font-normal">Rumah: {{ $surat->nomor_rumah ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    @if ($waDisplay !== '-')
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $waDisplay) }}" target="_blank" class="text-emerald-600 font-semibold hover:underline inline-flex items-center gap-1">
                                            <span>💬</span>
                                            <span>{{ $waDisplay }}</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-semibold text-slate-800">{{ $surat->jenis_surat }}</span>
                                    @if (!empty($surat->tujuan_surat))
                                        <span class="text-[10px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded border border-amber-200 block mt-0.5 w-fit font-medium">
                                            Tujuan: {{ $surat->tujuan_surat }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-slate-500 max-w-xs truncate" title="{{ $surat->keperluan }}">
                                    {{ $surat->keterangan_detail ?: $surat->keperluan }}
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    @if (in_array(strtolower($surat->status), ['disetujui', 'selesai']))
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">DISETUJUI</span>
                                    @elseif (strtolower($surat->status) === 'ditolak')
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-700 uppercase">DITOLAK</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-700 uppercase">MENUNGGU</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">Belum ada pengajuan surat tercatat.</td>
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

                <!-- Form Warga (Sinkron dengan Form Registrasi Warga) -->
                <div x-show="modalTab === 'warga'">
                    <form action="{{ route('admin.warga.store') }}" method="POST" class="space-y-3.5 text-xs">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                                placeholder="Contoh: Budi Santoso"
                                class="w-full text-xs border border-slate-200 rounded-xl p-3 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nomor HP</label>
                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="15"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 15)"
                                name="no_hp" value="{{ old('no_hp') }}" required autocomplete="tel"
                                placeholder="Contoh: 081234567890"
                                class="w-full text-xs border border-slate-200 rounded-xl p-3 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Password</label>
                            <input type="password" name="password" required autocomplete="new-password"
                                placeholder="Minimal 8 karakter kombinasi huruf & angka"
                                class="w-full text-xs border border-slate-200 rounded-xl p-3 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <p class="text-[11px] text-slate-500 mt-1">Wajib minimal 8 karakter dan berisi gabungan huruf serta angka.</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required autocomplete="new-password"
                                placeholder="Ketik ulang password"
                                class="w-full text-xs border border-slate-200 rounded-xl p-3 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="modalTambah = false" class="px-4 py-2 border border-slate-200 rounded-xl font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-[#1d4ed8] hover:bg-blue-800 text-white font-bold rounded-xl shadow-xs">Simpan Warga</button>
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

        <!-- Floating Realtime Alert Toast (Pemberitahuan Langsung Saat Ada Surat/Aduan Masuk) -->
        <div x-show="toastNotification" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="fixed bottom-6 right-6 z-50 max-w-sm bg-slate-900/95 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl border border-white/15 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-inner"
                 :class="toastNotification?.type === 'surat' ? 'bg-amber-500/20 text-amber-300' : 'bg-blue-500/20 text-blue-300'">
                <template x-if="toastNotification?.type === 'surat'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </template>
                <template x-if="toastNotification?.type !== 'surat'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </template>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-300">Live Notifikasi Real-time</span>
                </div>
                <div class="text-xs font-semibold text-white mt-1 leading-snug" x-text="toastNotification?.message"></div>
            </div>
            <button type="button" @click="toastNotification = null" class="text-slate-400 hover:text-white font-bold text-lg p-1">&times;</button>
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
