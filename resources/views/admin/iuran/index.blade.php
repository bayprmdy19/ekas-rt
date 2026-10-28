<x-admin-layout>
    <div x-data="iuranManagement()" class="p-5 sm:p-7 space-y-6">

        <!-- Top Header Iuran & Tagihan -->
        <div class="flex items-center justify-between pb-1">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">
                Iuran & Tagihan
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

        <!-- Alert Notifikasi Flash Message -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <span class="text-emerald-500 font-bold cursor-pointer" onclick="this.parentElement.remove()">&times;</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <span class="text-rose-500 font-bold cursor-pointer" onclick="this.parentElement.remove()">&times;</span>
            </div>
        @endif

        <!-- Grid Layout Utama (Kolom Kiri: Daftar Iuran, Kolom Kanan: Generate & Daftar Tagihan) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- KOLOM KIRI (7 / 12): DAFTAR IURAN -->
            <div class="lg:col-span-7 xl:col-span-8 bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6">
                <!-- Header Card Daftar Iuran -->
                <div class="flex items-center justify-between pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-slate-800 tracking-tight">Daftar Iuran</h2>
                    </div>

                    <!-- Tombol Tambah Iuran (Warna Biru sesuai dashboard admin) -->
                    <button type="button" 
                            @click="openModalTambah()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1d4ed8] hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Iuran</span>
                    </button>
                </div>

                <!-- Kontrol Filter (Tampilkan & Cari) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-600 pt-1 pb-3">
                    <div class="flex items-center gap-2">
                        <span>Tampilkan</span>
                        <select x-model.number="perPage" 
                                class="bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 cursor-pointer">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span>data</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <span>Cari:</span>
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder=""
                               class="bg-white border border-slate-200 rounded-lg px-3 py-1 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 w-full sm:w-44 transition">
                    </div>
                </div>

                <!-- Tabel Daftar Iuran (Header Biru Senada Dashboard) -->
                <div class="overflow-x-auto rounded-xl border border-slate-200/90 shadow-2xs">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-[#1d4ed8] text-white select-none">
                            <tr>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight">
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('nama_iuran')">
                                        <span>Nama Iuran</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight">
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('nominal')">
                                        <span>Nominal</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight">
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('tanggal_mulai')">
                                        <span>Tanggal Mulai</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight">
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('status')">
                                        <span>Status</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <template x-for="item in paginatedItems" :key="item.id">
                                <tr class="hover:bg-slate-50/75 transition-colors">
                                    <!-- Nama Iuran -->
                                    <td class="py-3 px-3.5 font-medium text-slate-800" x-text="item.nama_iuran"></td>

                                    <!-- Nominal Format Rupiah -->
                                    <td class="py-3 px-3.5 font-semibold text-slate-700" x-text="formatRupiah(item.nominal)"></td>

                                    <!-- Tanggal Mulai -->
                                    <td class="py-3 px-3.5 text-slate-500" x-text="item.tanggal_mulai_formatted"></td>

                                    <!-- Status Pill Badge -->
                                    <td class="py-3 px-3.5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                              :class="item.status === 'Aktif' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200'"
                                              x-text="item.status">
                                        </span>
                                    </td>

                                    <!-- Aksi Tombol Edit & Hapus -->
                                    <td class="py-3 px-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Edit Icon -->
                                            <button type="button" 
                                                    @click="openModalEdit(item)"
                                                    title="Edit Iuran"
                                                    class="w-7 h-7 rounded-lg border border-slate-200/90 text-slate-400 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </button>

                                            <!-- Hapus Icon -->
                                            <button type="button" 
                                                    @click="confirmHapus(item)"
                                                    title="Hapus Iuran"
                                                    class="w-7 h-7 rounded-lg border border-slate-200/90 text-slate-400 hover:text-rose-600 hover:border-rose-300 flex items-center justify-center transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="paginatedItems.length === 0">
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 italic">
                                        Tidak ada data iuran yang ditemukan.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Navigasi & Pagination -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 pt-4">
                    <div x-text="`Data ${startRecord} - ${endRecord} dari ${totalFiltered}`"></div>

                    <div class="flex items-center gap-1.5 select-none">
                        <button type="button" 
                                @click="prevPage()" 
                                :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium transition">
                            Sebelumnya
                        </button>

                        <template x-for="p in totalPages" :key="p">
                            <button type="button" 
                                    @click="goToPage(p)"
                                    :class="currentPage === p ? 'bg-[#1d4ed8] text-white font-bold' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200'"
                                    class="w-8 h-8 rounded-lg text-xs flex items-center justify-center transition"
                                    x-text="p">
                            </button>
                        </template>

                        <button type="button" 
                                @click="nextPage()" 
                                :disabled="currentPage === totalPages || totalPages === 0"
                                :class="currentPage === totalPages || totalPages === 0 ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium transition">
                            Selanjutnya
                        </button>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (5 / 12): GENERATE TAGIHAN & DAFTAR TAGIHAN -->
            <div class="lg:col-span-5 xl:col-span-4 space-y-6">

                <!-- KARTU 1: GENERATE TAGIHAN -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-800 tracking-tight">Generate Tagihan</h2>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-4 leading-relaxed">
                        Generate tagihan iuran untuk semua warga aktif
                    </p>

                    <!-- Form Generate Tagihan -->
                    <form action="{{ route('admin.tagihan.generate') }}" method="POST" class="space-y-3.5">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Bulan</label>
                            <select name="bulan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                                @php
                                    $bulanList = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    $currentMonth = (int)date('n');
                                @endphp
                                @foreach ($bulanList as $num => $nama)
                                    <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun</label>
                            <input type="number" 
                                   name="tahun" 
                                   value="{{ date('Y') }}" 
                                   min="2020" 
                                   max="2035"
                                   required
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white transition">
                        </div>

                        <!-- Tombol Generate Warna Biru Senada Dashboard -->
                        <button type="submit" 
                                class="w-full bg-[#1d4ed8] hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition flex items-center justify-center gap-2 mt-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Generate Tagihan</span>
                        </button>
                    </form>
                </div>

                <!-- KARTU 2: DAFTAR TAGIHAN -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6" x-data="{ filterStatus: 'semua', filterTahun: '' }">
                    <div class="flex items-center gap-2 mb-3.5">
                        <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                        </div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-800 tracking-tight">Daftar Tagihan</h2>
                    </div>

                    <!-- Filter Dropdown (Semua & Tahun) -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div>
                            <select x-model="filterStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 cursor-pointer">
                                <option value="semua">Semua</option>
                                <option value="lunas">Lunas</option>
                                <option value="belum_bayar">Belum Lunas</option>
                            </select>
                        </div>
                        <div>
                            <input type="text" 
                                   x-model="filterTahun" 
                                   placeholder="Tahun" 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2.5 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600">
                        </div>
                    </div>

                    <!-- Daftar Item Tagihan Warga -->
                    <div class="space-y-3 max-h-[380px] overflow-y-auto pr-1">
                        @forelse ($daftarTagihan as $tagihan)
                            @php
                                $wargaName = $tagihan->warga ? $tagihan->warga->nama_lengkap : 'Warga Telah Dihapus';
                                $subKeterangan = $tagihan->jenis_iuran . ($tagihan->keterangan ? ' • ' . $tagihan->keterangan : ' • ' . $tagihan->bulan_tahun);
                                $isLunas = $tagihan->status === 'lunas';
                                $tahunTagihan = substr($tagihan->bulan_tahun, 0, 4);
                            @endphp
                            <div x-show="(filterStatus === 'semua' || filterStatus === '{{ $tagihan->status }}') && (filterTahun === '' || '{{ $tahunTagihan }}'.includes(filterTahun))"
                                 class="flex items-center justify-between py-2 border-b border-slate-100 last:border-b-0 hover:bg-slate-50/50 px-1 rounded-lg transition">
                                <div>
                                    <div class="font-bold text-xs text-slate-800 leading-tight">
                                        {{ $wargaName }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium leading-normal mt-0.5">
                                        {{ $subKeterangan }}
                                    </div>
                                </div>

                                <div class="text-right flex flex-col items-end gap-1">
                                    <div class="font-bold text-xs text-slate-800 leading-none">
                                        Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}
                                    </div>

                                    <form action="{{ route('admin.tagihan.toggle', $tagihan->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                title="Klik untuk ubah status pembayaran" 
                                                class="cursor-pointer transition">
                                            @if ($isLunas)
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">
                                                    Belum Lunas
                                                </span>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-xs text-slate-400 italic">
                                Belum ada daftar tagihan yang dibuat.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        <!-- MODAL TAMBAH MASTER IURAN -->
        <div x-show="modalTambahIuran" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" 
             style="display: none;">
            
            <div @click.away="modalTambahIuran = false" 
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-150">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Data Iuran Baru</h3>
                    <button type="button" @click="modalTambahIuran = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.master-iuran.store') }}" method="POST" class="space-y-4 mt-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Iuran</label>
                        <input type="text" 
                               name="nama_iuran" 
                               placeholder="Contoh: Iuran RT, Kebersihan, dsb" 
                               required 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" 
                               name="nominal" 
                               placeholder="Contoh: 75000" 
                               min="0"
                               step="1000"
                               required 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Berlaku</label>
                        <input type="date" 
                               name="tanggal_mulai" 
                               value="{{ date('Y-m-d') }}"
                               required 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <textarea name="keterangan" rows="2" placeholder="Catatan atau tujuan iuran..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="modalTambahIuran = false" class="px-4 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#1d4ed8] hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                            Simpan Iuran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT MASTER IURAN -->
        <div x-show="modalEditIuran" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" 
             style="display: none;">
            
            <div @click.away="modalEditIuran = false" 
                 class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 relative animate-in fade-in zoom-in-95 duration-150">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-sm">Edit Data Iuran</h3>
                    <button type="button" @click="modalEditIuran = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="editFormAction" method="POST" class="space-y-4 mt-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Iuran</label>
                        <input type="text" 
                               name="nama_iuran" 
                               x-model="editItem.nama_iuran"
                               required 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal (Rp)</label>
                        <input type="number" 
                               name="nominal" 
                               x-model="editItem.nominal"
                               min="0"
                               step="1000"
                               required 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Berlaku</label>
                        <input type="date" 
                               name="tanggal_mulai" 
                               x-model="editItem.tanggal_mulai"
                               required 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                        <select name="status" x-model="editItem.status" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white">
                            <option value="Aktif">Aktif</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan (Opsional)</label>
                        <textarea name="keterangan" x-model="editItem.keterangan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="modalEditIuran = false" class="px-4 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-semibold rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#1d4ed8] hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                            Perbarui Iuran
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL KONFIRMASI HAPUS -->
        <div x-show="modalHapusIuran" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" 
             style="display: none;">
            
            <div @click.away="modalHapusIuran = false" 
                 class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-100 relative text-center">
                
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h3 class="font-bold text-slate-800 text-sm">Hapus Data Iuran?</h3>
                <p class="text-xs text-slate-500 mt-1 mb-5">
                    Apakah Anda yakin ingin menghapus iuran <span class="font-semibold text-slate-800" x-text="hapusItem.nama_iuran"></span>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <form :action="hapusFormAction" method="POST" class="flex items-center justify-center gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="modalHapusIuran = false" class="w-1/2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="w-1/2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Script Alpine.js Pengelola Data Iuran -->
    <script>
        function iuranManagement() {
            return {
                rawItems: @json($masterIuransFormatted),
                searchQuery: '',
                perPage: 10,
                currentPage: 1,
                sortColumn: 'id',
                sortDirection: 'asc',

                // State Modal
                modalTambahIuran: false,
                modalEditIuran: false,
                modalHapusIuran: false,
                editItem: {},
                editFormAction: '',
                hapusItem: {},
                hapusFormAction: '',

                get filteredItems() {
                    let items = [...this.rawItems];

                    if (this.searchQuery.trim() !== '') {
                        const query = this.searchQuery.toLowerCase();
                        items = items.filter(item => {
                            return item.nama_iuran.toLowerCase().includes(query) ||
                                   item.nominal.toString().includes(query) ||
                                   item.tanggal_mulai_formatted.toLowerCase().includes(query) ||
                                   item.status.toLowerCase().includes(query);
                        });
                    }

                    items.sort((a, b) => {
                        let fieldA = a[this.sortColumn];
                        let fieldB = b[this.sortColumn];

                        if (typeof fieldA === 'string') {
                            fieldA = fieldA.toLowerCase();
                            fieldB = fieldB.toLowerCase();
                        }

                        if (fieldA < fieldB) return this.sortDirection === 'asc' ? -1 : 1;
                        if (fieldA > fieldB) return this.sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    });

                    return items;
                },

                get totalFiltered() {
                    return this.filteredItems.length;
                },

                get totalPages() {
                    return Math.ceil(this.totalFiltered / this.perPage) || 1;
                },

                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredItems.slice(start, start + this.perPage);
                },

                get startRecord() {
                    if (this.totalFiltered === 0) return 0;
                    return (this.currentPage - 1) * this.perPage + 1;
                },

                get endRecord() {
                    const end = this.currentPage * this.perPage;
                    return end > this.totalFiltered ? this.totalFiltered : end;
                },

                sortBy(col) {
                    if (this.sortColumn === col) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = col;
                        this.sortDirection = 'asc';
                    }
                    this.currentPage = 1;
                },

                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                    }
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                    }
                },

                goToPage(p) {
                    this.currentPage = p;
                },

                formatRupiah(num) {
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                },

                openModalTambah() {
                    this.modalTambahIuran = true;
                },

                openModalEdit(item) {
                    this.editItem = { ...item };
                    this.editFormAction = `{{ url('/admin/master-iuran') }}/${item.id}`;
                    this.modalEditIuran = true;
                },

                confirmHapus(item) {
                    this.hapusItem = { ...item };
                    this.hapusFormAction = `{{ url('/admin/master-iuran') }}/${item.id}`;
                    this.modalHapusIuran = true;
                }
            };
        }
    </script>
</x-admin-layout>
