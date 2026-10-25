<x-admin-layout>
    <div x-data="pembayaranManagement()" class="p-5 sm:p-7 space-y-6">

        <!-- Header Halaman Pembayaran -->
        <div class="flex items-center justify-between pb-1">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">
                Pembayaran
            </h1>
            <div class="flex items-center gap-3">
                <a href="{{ route('public.dashboard') }}" 
                   title="Halaman Publik"
                   class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-400 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </a>
                <button type="button" 
                        class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-400 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition shadow-xs relative">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="w-2 h-2 rounded-full bg-blue-600 absolute top-2 right-2 ring-2 ring-white"></span>
                </button>
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

        <!-- Grid Layout Utama (Kolom Kiri: Riwayat Pembayaran, Kolom Kanan: Info Rekening & Input Pembayaran) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- KOLOM KIRI (7 / 12): RIWAYAT PEMBAYARAN -->
            <div class="lg:col-span-7 xl:col-span-8 bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6">
                
                <!-- Header Card Riwayat Pembayaran -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold text-slate-800 tracking-tight">Riwayat Pembayaran</h2>
                    </div>

                    <!-- Dropdown Filter Semua Status -->
                    <div>
                        <select x-model="statusFilter" 
                                class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 shadow-2xs cursor-pointer">
                            <option value="semua">Semua Status</option>
                            <option value="Lunas">Lunas</option>
                            <option value="Belum Lunas">Belum Lunas</option>
                        </select>
                    </div>
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

                <!-- Tabel Riwayat Pembayaran (Header Biru Sesuai Dashboard) -->
                <div class="overflow-x-auto rounded-xl border border-slate-200/90 shadow-2xs">
                    <table class="w-full text-left text-xs text-slate-700">
                        <thead class="bg-[#1d4ed8] text-white select-none">
                            <tr>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight">
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('tanggal')">
                                        <span>Tanggal</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight">
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('nama_warga')">
                                        <span>Nama Warga</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight">
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('bln_thn')">
                                        <span>Bln/Thn</span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </div>
                                </th>
                                <th scope="col" class="py-3 px-3.5 font-bold tracking-tight">
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('jenis_iuran')">
                                        <span>Jenis Iuran</span>
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
                                    <div class="flex items-center gap-1.5 cursor-pointer" @click="sortBy('metode')">
                                        <span>Metode</span>
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
                                    <!-- Tanggal -->
                                    <td class="py-3 px-3.5 text-slate-600 whitespace-nowrap" x-text="item.tanggal"></td>

                                    <!-- Nama Warga -->
                                    <td class="py-3 px-3.5 font-bold text-slate-800" x-text="item.nama_warga"></td>

                                    <!-- Bln/Thn -->
                                    <td class="py-3 px-3.5 text-slate-500 whitespace-nowrap" x-text="item.bln_thn"></td>

                                    <!-- Jenis Iuran -->
                                    <td class="py-3 px-3.5 text-slate-700 whitespace-nowrap" x-text="item.jenis_iuran"></td>

                                    <!-- Nominal Format Rupiah -->
                                    <td class="py-3 px-3.5 font-semibold text-slate-800 whitespace-nowrap" x-text="formatRupiah(item.nominal)"></td>

                                    <!-- Metode -->
                                    <td class="py-3 px-3.5 text-slate-600 whitespace-nowrap" x-text="item.metode"></td>

                                    <!-- Status Badge -->
                                    <td class="py-3 px-3.5 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                              :class="item.status === 'Lunas' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200'"
                                              x-text="item.status">
                                        </span>
                                    </td>

                                    <!-- Aksi Tombol Bukti & Detail -->
                                    <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Tombol Bukti Bayar -->
                                            <button type="button" 
                                                    @click="openBukti(item)"
                                                    title="Lihat Bukti Bayar"
                                                    class="w-7 h-7 rounded-lg border border-slate-200/90 text-slate-400 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </button>

                                            <!-- Tombol Lihat Detail -->
                                            <button type="button" 
                                                    @click="openDetail(item)"
                                                    title="Detail Pembayaran"
                                                    class="w-7 h-7 rounded-lg border border-slate-200/90 text-slate-400 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition shadow-2xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="paginatedItems.length === 0">
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400 italic">
                                        Tidak ada riwayat pembayaran yang ditemukan.
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

            <!-- KOLOM KANAN (5 / 12): INFO REKENING & INPUT PEMBAYARAN WARGA -->
            <div class="lg:col-span-5 xl:col-span-4 space-y-6">

                <!-- KARTU 1: INFO REKENING -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-3.5">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                </svg>
                            </div>
                            <h2 class="text-sm sm:text-base font-bold text-slate-800 tracking-tight">Info Rekening</h2>
                        </div>

                        <!-- Tombol Kelola Rekening (Warna Biru Dashboard) -->
                        <button type="button" 
                                @click="modalKelolaRekening = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#1d4ed8] hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-2xs transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Kelola</span>
                        </button>
                    </div>

                    <!-- Daftar Item Rekening & E-Wallet -->
                    <div class="space-y-2.5 pt-1">
                        @forelse ($rekenings as $rek)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 hover:border-blue-200 transition">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#1d4ed8] to-[#1e40af] text-white flex items-center justify-center shrink-0 shadow-2xs">
                                    @if ($rek->jenis === 'bank')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="font-bold text-xs text-slate-800 leading-tight">
                                        {{ $rek->nama_bank }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium truncate mt-0.5">
                                        {{ $rek->nomor_rekening }} a.n {{ $rek->atas_nama }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-xs text-slate-400 italic py-2 text-center">
                                Belum ada info rekening RT.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- KARTU 2: INPUT PEMBAYARAN WARGA -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <h2 class="text-sm sm:text-base font-bold text-slate-800 tracking-tight">Input Pembayaran Warga</h2>
                    </div>

                    <!-- Form Input Pembayaran -->
                    <form action="{{ route('admin.pembayaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                        @csrf

                        <!-- Nama Warga -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Warga</label>
                            <select name="warga_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                                <option value="">Ketik / Pilih nama warga...</option>
                                @foreach ($wargas as $w)
                                    <option value="{{ $w->id }}">
                                        {{ $w->nama_lengkap }} ({{ $w->nomor_rumah ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jenis Iuran -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Iuran</label>
                            <select name="jenis_iuran" 
                                    @change="onJenisIuranChange($event)"
                                    required 
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                                <option value="">-- Pilih Jenis Iuran --</option>
                                @foreach ($masterIurans as $mi)
                                    <option value="{{ $mi->nama_iuran }}" data-nominal="{{ (int)$mi->nominal }}">
                                        {{ $mi->nama_iuran }} (Rp {{ number_format($mi->nominal, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bulan & Tahun -->
                        <div class="grid grid-cols-2 gap-2.5">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Bulan</label>
                                <select name="bulan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                                    @php
                                        $bulanList = [
                                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                        ];
                                        $currMonth = (int)date('n');
                                    @endphp
                                    @foreach ($bulanList as $n => $namaBulan)
                                        <option value="{{ $n }}" {{ $currMonth == $n ? 'selected' : '' }}>
                                            {{ $namaBulan }}
                                        </option>
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
                        </div>

                        <!-- Nominal (Rp) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal (Rp)</label>
                            <input type="number" 
                                   name="jumlah" 
                                   x-model="formNominal" 
                                   placeholder="0"
                                   min="1"
                                   required 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white transition">
                        </div>

                        <!-- Metode Pembayaran -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="Tunai">Tunai / Cash</option>
                                <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
                            </select>
                        </div>

                        <!-- Upload Bukti Bayar (Opsional) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Bukti Transfer (Opsional)</label>
                            <input type="file" 
                                   name="bukti_bayar" 
                                   accept="image/*"
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        </div>

                        <!-- Tombol Submit (Biru Dashboard) -->
                        <button type="submit" 
                                class="w-full bg-[#1d4ed8] hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition flex items-center justify-center gap-2 mt-4">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Pembayaran</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>

        <!-- MODAL KELOLA REKENING -->
        <div x-show="modalKelolaRekening" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" 
             style="display: none;">
            
            <div @click.away="modalKelolaRekening = false" 
                 class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 relative max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-sm">Kelola Rekening RT / E-Wallet</h3>
                    <button type="button" @click="modalKelolaRekening = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form Tambah Rekening -->
                <form action="{{ route('admin.rekening.store') }}" method="POST" class="space-y-3.5 my-4 p-4 bg-slate-50 rounded-xl border border-slate-200/80">
                    @csrf
                    <div class="font-bold text-xs text-slate-800">+ Tambah Rekening / Dompet Digital Baru</div>
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama Bank / E-Wallet</label>
                            <input type="text" name="nama_bank" placeholder="Contoh: BCA / DANA" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jenis</label>
                            <select name="jenis" class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs">
                                <option value="bank">Bank</option>
                                <option value="ewallet">E-Wallet</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Nomor Rekening / No HP</label>
                            <input type="text" name="nomor_rekening" placeholder="Contoh: 12345678" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Atas Nama</label>
                            <input type="text" name="atas_nama" placeholder="Contoh: Kas RT 01" required class="w-full bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#1d4ed8] hover:bg-blue-700 text-white font-semibold text-xs py-2 rounded-lg transition shadow-2xs">
                        Simpan Rekening Baru
                    </button>
                </form>

                <!-- Daftar Rekening Saat Ini -->
                <div class="space-y-2 mt-4">
                    <div class="font-bold text-xs text-slate-700 mb-2">Daftar Rekening Aktif:</div>
                    @foreach ($rekenings as $r)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-white">
                            <div>
                                <div class="font-bold text-xs text-slate-800">{{ $r->nama_bank }} ({{ ucfirst($r->jenis) }})</div>
                                <div class="text-[11px] text-slate-500">{{ $r->nomor_rekening }} a.n {{ $r->atas_nama }}</div>
                            </div>
                            <form action="{{ route('admin.rekening.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-semibold px-2 py-1 bg-rose-50 rounded-lg">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- MODAL DETAIL PEMBAYARAN -->
        <div x-show="modalDetail" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs" 
             style="display: none;">
            
            <div @click.away="modalDetail = false" 
                 class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-100 relative">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-sm">Detail Pembayaran</h3>
                    <button type="button" @click="modalDetail = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-3 py-4 text-xs">
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Nama Warga:</span>
                        <span class="font-bold text-slate-800" x-text="detailItem.nama_warga"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Nomor Rumah:</span>
                        <span class="font-semibold text-slate-700" x-text="detailItem.nomor_rumah"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Tanggal Bayar:</span>
                        <span class="font-semibold text-slate-700" x-text="detailItem.tanggal"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Jenis Iuran:</span>
                        <span class="font-semibold text-slate-700" x-text="detailItem.jenis_iuran"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Periode:</span>
                        <span class="font-semibold text-slate-700" x-text="detailItem.bln_thn"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Nominal:</span>
                        <span class="font-bold text-blue-700" x-text="formatRupiah(detailItem.nominal)"></span>
                    </div>
                    <div class="flex justify-between border-b border-slate-50 pb-2">
                        <span class="text-slate-500">Metode Bayar:</span>
                        <span class="font-semibold text-slate-700" x-text="detailItem.metode"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status:</span>
                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200" x-text="detailItem.status"></span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex justify-end">
                    <button type="button" @click="modalDetail = false" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL BUKTI TRANSFER -->
        <div x-show="modalBukti" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" 
             style="display: none;">
            
            <div @click.away="modalBukti = false" 
                 class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-100 text-center">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="font-bold text-slate-800 text-sm">Bukti Pembayaran</h3>
                    <button type="button" @click="modalBukti = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <template x-if="detailItem.bukti_bayar">
                    <div>
                        <img :src="'/storage/' + detailItem.bukti_bayar" alt="Bukti Transfer" class="w-full max-h-64 object-contain rounded-xl border border-slate-200 mb-3">
                    </div>
                </template>

                <template x-if="!detailItem.bukti_bayar">
                    <div class="py-8 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-400 mb-3">
                        <svg class="w-10 h-10 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div class="text-xs font-semibold">Bukti fisik/struk tidak diunggah</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Transaksi diverifikasi oleh Admin RT</div>
                    </div>
                </template>

                <div class="text-xs text-slate-600 font-medium mb-4">
                    Pembayaran a.n <span class="font-bold text-slate-800" x-text="detailItem.nama_warga"></span> (<span x-text="formatRupiah(detailItem.nominal)"></span>)
                </div>

                <button type="button" @click="modalBukti = false" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Tutup
                </button>
            </div>
        </div>

    </div>

    <!-- Script Alpine.js Pengelola Data Pembayaran -->
    <script>
        function pembayaranManagement() {
            return {
                rawItems: @json($riwayatPembayaranFormatted),
                statusFilter: 'semua',
                searchQuery: '',
                perPage: 10,
                currentPage: 1,
                sortColumn: 'tanggal',
                sortDirection: 'desc',

                // Form nominal
                formNominal: 0,

                // State Modal
                modalKelolaRekening: false,
                modalDetail: false,
                modalBukti: false,
                detailItem: {},

                onJenisIuranChange(e) {
                    const sel = e.target;
                    const opt = sel.options[sel.selectedIndex];
                    const nom = opt.getAttribute('data-nominal');
                    if (nom) {
                        this.formNominal = parseInt(nom);
                    }
                },

                get filteredItems() {
                    let items = [...this.rawItems];

                    if (this.statusFilter !== 'semua') {
                        items = items.filter(i => i.status === this.statusFilter);
                    }

                    if (this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase();
                        items = items.filter(item => {
                            return item.nama_warga.toLowerCase().includes(q) ||
                                   item.jenis_iuran.toLowerCase().includes(q) ||
                                   item.metode.toLowerCase().includes(q) ||
                                   item.bln_thn.toLowerCase().includes(q) ||
                                   item.tanggal.toLowerCase().includes(q) ||
                                   item.nominal.toString().includes(q);
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

                openDetail(item) {
                    this.detailItem = { ...item };
                    this.modalDetail = true;
                },

                openBukti(item) {
                    this.detailItem = { ...item };
                    this.modalBukti = true;
                }
            };
        }
    </script>
</x-admin-layout>
