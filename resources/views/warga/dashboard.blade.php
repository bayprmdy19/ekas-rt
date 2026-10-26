<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                        Warga RT
                    </span>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Portal Layanan Warga
                    </h2>
                </div>
                <p class="text-xs text-slate-500 mt-1">Halo, {{ Auth::user()->name }} ({{ '@' . Auth::user()->username }}). Selamat datang di portal warga RT/RW Digital.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('public.dashboard') }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition shadow-2xs">
                    Ke Halaman Utama Publik
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="wargaDashboard()" class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Message Alerts -->
            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 font-semibold flex items-center justify-between shadow-2xs">
                    <span>{{ session('error') }}</span>
                    <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 font-bold">&times;</button>
                </div>
            @endif

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <span>✅</span>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 font-bold">&times;</button>
                </div>
            @endif

            <!-- Banner Sambutan Warga -->
            <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-md flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-1.5 max-w-xl">
                    <span class="text-xs font-semibold text-blue-200 uppercase tracking-wider">Akses Terpadu Warga</span>
                    <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight">Transparansi, Pelayanan & Evaluasi Lingkungan</h3>
                    <p class="text-xs sm:text-sm text-blue-100">
                        Pantau keuangan kas RT secara terbuka, laporkan kendala lingkungan, dan berikan penilaian kepuasan pelayanan pengurus RT.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2.5">
                    <a href="{{ route('public.dashboard') }}#layanan-warga" class="px-4 py-2.5 bg-white text-blue-700 font-bold text-xs rounded-xl shadow hover:bg-blue-50 transition">
                        + Buat Pengajuan / Aduan
                    </a>
                    <a href="{{ route('warga.dokumen.index') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs rounded-xl border border-white/20 transition shadow-xs">
                        📁 Data Diri & KK
                    </a>
                    <a href="#section-surat" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl border border-white/20 transition shadow-xs">
                        📄 Surat Saya
                    </a>
                    <a href="#section-pengaduan" class="px-4 py-2.5 bg-blue-600/60 hover:bg-blue-600 text-white font-bold text-xs rounded-xl border border-white/20 transition">
                        ⭐ Evaluasi Pelayanan
                    </a>
                </div>
            </div>

            <!-- Card Saldo Transparansi Kas -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Kas RT Terkini</div>
                    <div class="text-2xl font-black text-blue-600 mt-2">Rp {{ number_format($saldoKas, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Transparan dan terverifikasi</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kas Masuk</div>
                    <div class="text-2xl font-black text-emerald-600 mt-2">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Akumulasi seluruh pemasukan</div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Kas Keluar</div>
                    <div class="text-2xl font-black text-rose-600 mt-2">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</div>
                    <div class="text-[11px] text-slate-400 mt-1">Kegiatan & perbaikan sarana</div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- SECTION KHUSUS: PENGADUAN SAYA & EVALUASI PELAYANAN (RATING & ULASAN)     -->
            <!-- ========================================================================= -->
            <div id="section-pengaduan" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-7 space-y-5">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                                Status Pengaduan & Evaluasi Pelayanan
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Berikan ulasan dan penilaian bintang (⭐ 1-5) setelah laporan pengaduan Anda diselesaikan oleh pengurus RT
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('public.dashboard') }}#layanan-warga" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-2xs shrink-0 self-start sm:self-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Buat Aduan Baru</span>
                    </a>
                </div>

                <!-- Info Card Otomatisasi Rating 1x24 Jam -->
                <div class="p-4 bg-sky-50/70 border border-sky-200/80 rounded-2xl flex items-start gap-3 text-xs text-sky-900">
                    <span class="text-base mt-0.5">ℹ️</span>
                    <div class="leading-relaxed">
                        <strong class="font-bold">Ketentuan Penilaian Layanan (SOP 1x24 Jam):</strong>
                        <p class="text-slate-600 text-[11px] mt-0.5">
                            Jika aduan Anda telah diselesaikan oleh pengurus RT namun belum sempat Anda nilai dalam rentang waktu <strong>1x24 jam sejak selesai</strong>, sistem otomatis memberikan nilai <strong>⭐ 5 (Layanan tuntas)</strong>. Namun, Anda tetap dapat memperbarui rating dan ulasan kepuasan Anda melalui tombol <em>"Beri / Ubah Rating"</em> di bawah ini.
                        </p>
                    </div>
                </div>

                <!-- Tabel Riwayat Aduan Warga -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-2xs">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-[#1d4ed8] text-white select-none">
                            <tr>
                                <th class="px-4 py-3 font-bold">Tanggal</th>
                                <th class="px-4 py-3 font-bold">Kategori</th>
                                <th class="px-4 py-3 font-bold">Judul & Laporan</th>
                                <th class="px-4 py-3 font-bold text-center">Status</th>
                                <th class="px-4 py-3 font-bold text-center">Evaluasi / Rating Pelayanan</th>
                                <th class="px-4 py-3 font-bold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($pengaduanWarga as $item)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Tanggal -->
                                    <td class="px-4 py-3.5 text-slate-500 whitespace-nowrap font-medium">
                                        {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                                    </td>

                                    <!-- Kategori -->
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $item->kategori ?: 'Layanan Umum' }}
                                        </span>
                                    </td>

                                    <!-- Judul & Isi -->
                                    <td class="px-4 py-3.5 max-w-xs">
                                        <div class="font-bold text-slate-800 line-clamp-1">{{ $item->judul_laporan }}</div>
                                        <div class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $item->isi_laporan }}</div>
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if(in_array(strtolower($item->status), ['pending', 'diterima']))
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Diterima
                                            </span>
                                        @elseif(strtolower($item->status) === 'diproses')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Sedang Diproses
                                            </span>
                                        @elseif(strtolower($item->status) === 'selesai')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Selesai
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Rating Pelayanan -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if(strtolower($item->status) === 'selesai')
                                            @if($item->rating)
                                                <div class="flex flex-col items-center">
                                                    <div class="flex items-center text-amber-400 text-xs">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            <span>{{ $s <= $item->rating ? '★' : '☆' }}</span>
                                                        @endfor
                                                    </div>
                                                    <span class="text-[10px] text-slate-500 font-semibold mt-0.5">{{ $item->rating }} / 5</span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                                    Menunggu Penilaian Anda
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 text-xs italic">-</span>
                                        @endif
                                    </td>

                                    <!-- Tombol Aksi Beri Rating -->
                                    <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                        @if(strtolower($item->status) === 'selesai')
                                            <button type="button" 
                                                    @click="openRatingModal({{ json_encode([
                                                        'id' => $item->id,
                                                        'judul' => $item->judul_laporan,
                                                        'kategori' => $item->kategori ?: 'Layanan Umum',
                                                        'selesai_at' => $item->selesai_at ? $item->selesai_at->translatedFormat('d M Y, H:i') : 'Baru saja',
                                                        'current_rating' => $item->rating ?: 5,
                                                        'current_ulasan' => $item->ulasan_warga ?: '',
                                                    ]) }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl {{ $item->rating ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white shadow-2xs font-bold' }} text-xs font-semibold transition">
                                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <span>{{ $item->rating ? 'Ubah Penilaian' : 'Beri Penilaian' }}</span>
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Menunggu Tuntas</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 italic">
                                        Belum ada riwayat pengaduan. Anda dapat mengajukan kendala lingkungan melalui tombol di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- SECTION KHUSUS: STATUS PENGAJUAN SURAT ADMINISTRASI WARGA                 -->
            <!-- ========================================================================= -->
            <div id="section-surat" class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-7 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 tracking-tight flex items-center gap-2">
                                Status Pengajuan Surat Warga
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Pantau permohonan surat pengantar Anda dan unduh dokumen resmi saat status sudah selesai
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('public.dashboard') }}#layanan-warga" 
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs shrink-0 self-start sm:self-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Ajukan Surat Baru</span>
                    </a>
                </div>

                <!-- Tabel Riwayat Pengajuan Surat -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-2xs">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-[#1e293b] text-white select-none">
                            <tr>
                                <th class="px-5 py-3 font-bold">Tanggal</th>
                                <th class="px-5 py-3 font-bold">Jenis Surat</th>
                                <th class="px-5 py-3 font-bold">Keperluan / Keterangan</th>
                                <th class="px-5 py-3 font-bold text-center">Status</th>
                                <th class="px-5 py-3 font-bold text-center">Dokumen Surat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($suratWarga as $surat)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Tanggal Pengajuan -->
                                    <td class="px-5 py-3.5 text-slate-500 whitespace-nowrap font-medium">
                                        {{ $surat->created_at ? $surat->created_at->translatedFormat('d M Y') : '-' }}
                                    </td>

                                    <!-- Jenis Surat -->
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        <div class="font-bold text-slate-800">{{ $surat->judul_laporan }}</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Rumah: {{ $surat->nomor_rumah ?: '-' }}</div>
                                    </td>

                                    <!-- Keperluan / Detail -->
                                    <td class="px-5 py-3.5 max-w-xs">
                                        <div class="text-slate-600 line-clamp-2 leading-relaxed">
                                            {{ $surat->isi_laporan ?: 'Tidak ada rincian tambahan' }}
                                        </div>
                                    </td>

                                    <!-- Status Pengurusan -->
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                        @if(in_array(strtolower($surat->status), ['pending', 'diterima']))
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Diterima
                                            </span>
                                        @elseif(strtolower($surat->status) === 'diproses')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Sedang Diproses
                                            </span>
                                        @elseif(strtolower($surat->status) === 'selesai')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Selesai Disetujui
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Tombol Download / Cetak Surat -->
                                    <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                        @if(strtolower($surat->status) === 'selesai')
                                            <a href="{{ route('warga.surat.download', $surat->id) }}" 
                                               target="_blank"
                                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span>Unduh / Cetak Surat</span>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-slate-400 text-xs italic">
                                                <span>⏳</span> Menunggu Selesai
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">
                                        Belum ada riwayat pengajuan surat. Anda dapat mengajukan permohonan surat pengantar melalui tombol di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- MODAL INTERAKTIF: BERI RATING & ULASAN PELAYANAN OLEH WARGA               -->
        <!-- ========================================================================= -->
        <div x-show="isRatingModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            
            <div @click.away="isRatingModalOpen = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5 my-8 transform transition-all text-slate-800">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 tracking-tight">Evaluasi Pelayanan Warga</h3>
                            <p class="text-xs text-slate-500">Berikan penilaian kepuasan Anda</p>
                        </div>
                    </div>
                    <button type="button" @click="isRatingModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
                </div>

                <!-- Informasi Laporan yang Dinilai -->
                <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200/80 text-xs space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Aduan Diselesaikan:</span>
                    <div class="font-bold text-slate-800 text-sm line-clamp-1" x-text="targetItem?.judul"></div>
                    <div class="text-slate-500 flex items-center justify-between text-[11px] pt-1">
                        <span x-text="'Kategori: ' + targetItem?.kategori"></span>
                        <span class="text-emerald-700 font-semibold" x-text="'Selesai: ' + targetItem?.selesai_at"></span>
                    </div>
                </div>

                <!-- Form Penilaian Rating -->
                <form :action="`/warga/pengaduan/${targetItem?.id}/rating`" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <!-- Hidden Input Rating -->
                    <input type="hidden" name="rating" :value="selectedRating">

                    <!-- Bintang Interaktif -->
                    <div class="text-center space-y-2 py-1">
                        <label class="text-xs font-bold text-slate-700 block">Tingkat Kepuasan Pelayanan:</label>
                        
                        <div class="flex items-center justify-center gap-2">
                            <template x-for="star in 5" :key="star">
                                <button type="button" 
                                        @click="selectedRating = star"
                                        @mouseenter="hoverRating = star"
                                        @mouseleave="hoverRating = 0"
                                        class="text-3xl transition-transform hover:scale-125 focus:outline-none cursor-pointer">
                                    <span :class="(hoverRating || selectedRating) >= star ? 'text-amber-400 drop-shadow-xs' : 'text-slate-300'">★</span>
                                </button>
                            </template>
                        </div>

                        <!-- Deskripsi Rating Dinamis -->
                        <div class="text-xs font-bold transition-all"
                             :class="{
                                 'text-rose-600': (hoverRating || selectedRating) === 1,
                                 'text-orange-500': (hoverRating || selectedRating) === 2,
                                 'text-amber-600': (hoverRating || selectedRating) === 3,
                                 'text-blue-600': (hoverRating || selectedRating) === 4,
                                 'text-emerald-600': (hoverRating || selectedRating) === 5
                             }"
                             x-text="getRatingLabel(hoverRating || selectedRating)">
                        </div>
                    </div>

                    <!-- Input Ulasan Komentar Warga -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 block">
                            Ulasan & Komentar Warga (Opsional):
                        </label>
                        <textarea name="ulasan_warga" 
                                  rows="3" 
                                  x-model="ulasanText"
                                  placeholder="Contoh: Terima kasih banyak pengurus RT, lampu jalan sudah menyala terang dan penanganannya cepat sekali!"
                                  class="w-full text-xs rounded-2xl border-slate-200 p-3.5 focus:border-blue-600 focus:ring-blue-600 bg-slate-50 text-slate-800 placeholder:text-slate-400"></textarea>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" 
                                @click="isRatingModalOpen = false" 
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl bg-[#1d4ed8] hover:bg-blue-700 text-white font-bold text-xs transition shadow-2xs">
                            Kirim Penilaian
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <!-- Alpine.js Component Script untuk Rating Modal -->
    <script>
        function wargaDashboard() {
            return {
                isRatingModalOpen: false,
                targetItem: null,
                selectedRating: 5,
                hoverRating: 0,
                ulasanText: '',

                openRatingModal(item) {
                    this.targetItem = item;
                    this.selectedRating = item.current_rating || 5;
                    this.hoverRating = 0;
                    this.ulasanText = item.current_ulasan || '';
                    this.isRatingModalOpen = true;
                },

                getRatingLabel(val) {
                    switch(val) {
                        case 1: return '⭐ 1 - Sangat Kecewa / Penanganan Lambat';
                        case 2: return '⭐ 2 - Kurang Puas';
                        case 3: return '⭐ 3 - Cukup / Standar';
                        case 4: return '⭐ 4 - Puas / Respon Baik';
                        case 5: return '⭐ 5 - Sangat Puas / Pelayanan Cepat & Tuntas!';
                        default: return 'Pilih Bintang Penilaian';
                    }
                }
            };
        }
    </script>
</x-app-layout>
