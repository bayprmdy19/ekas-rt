<x-admin-layout>
    @php
        $formattedSurats = $surats->map(function ($item) {
            return [
                'id' => $item->id,
                'nomor_surat' => $item->nomor_surat ?: $item->nomor_surat_display,
                'tanggal' => $item->created_at ? $item->created_at->format('Y-m-d') : '-',
                'tanggal_lengkap' => $item->created_at ? $item->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                'nama_pengaju' => $item->nama_pengaju ?: 'Warga (Anonim)',
                'nomor_rumah' => $item->nomor_rumah ?: '-',
                'jenis_surat' => $item->jenis_surat ?: 'Surat Pengantar RT',
                'keperluan' => $item->keperluan ?: '-',
                'status' => in_array(strtolower($item->status), ['disetujui', 'selesai']) ? 'disetujui' : (strtolower($item->status) === 'ditolak' ? 'ditolak' : 'menunggu'),
                'status_label' => $item->status_label,
                'catatan' => $item->catatan ?: '-',
                'cetak_url' => route('admin.surat.cetak', $item->id),
            ];
        });
    @endphp

    <div x-data="suratManagement(@js($formattedSurats))" class="p-5 sm:p-7 space-y-6">

        <!-- Top Header: Title & Navigation Icons -->
        <div class="flex items-center justify-between pb-1">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">
                    Surat
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 font-medium">
                    Pusat penerbitan, verifikasi permohonan, dan pengarsipan surat pengantar warga RT/RW Digital
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('public.dashboard') }}" 
                   title="Halaman Publik"
                   class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-500 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </a>
                <div class="w-9 h-9 rounded-xl bg-white border border-slate-200/90 text-slate-500 flex items-center justify-center shadow-2xs relative">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($totalMenunggu > 0)
                        <span class="w-2 h-2 rounded-full bg-blue-600 absolute top-2 right-2 ring-2 ring-white"></span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dynamic AJAX Toast Notification -->
        <div x-show="toastMessage" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span x-text="toastMessage"></span>
            </div>
            <button type="button" class="text-emerald-500 hover:text-emerald-700 font-bold p-1" @click="toastMessage = null">&times;</button>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Surat -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pengajuan</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mt-1" x-text="surats.length"></h3>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Menunggu Verifikasi -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Menunggu</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1" x-text="countByStatus('menunggu')"></h3>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Disetujui -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Disetujui</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1" x-text="countByStatus('disetujui')"></h3>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Ditolak -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-2xs hover:shadow-sm transition">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Ditolak</span>
                        <h3 class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-1" x-text="countByStatus('ditolak')"></h3>
                    </div>
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Container Utama: Pengajuan Surat -->
        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
            <!-- Card Header: Title & + Ajukan Surat Button (Warna Biru Sesuai Permintaan) -->
            <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-800">
                            Pengajuan Surat
                        </h2>
                        <p class="text-xs text-slate-400">
                            Daftar permohonan surat administrasi yang diajukan oleh warga
                        </p>
                    </div>
                </div>

                <!-- Tombol + Ajukan Surat Warna Biru -->
                <button type="button" 
                        @click="openAddModal()" 
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-semibold text-xs transition shadow-sm hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Ajukan Surat</span>
                </button>
            </div>

            <!-- Card Controls: Tampilkan [10] data & Cari: [______] -->
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50">
                <div class="flex items-center gap-2 text-xs text-slate-600 w-full sm:w-auto">
                    <span>Tampilkan</span>
                    <select x-model.number="perPage" 
                            class="bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 text-xs text-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-2xs">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>data</span>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <label class="text-xs text-slate-600 font-medium">Cari:</label>
                    <div class="relative w-full sm:w-64">
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder="Cari no surat, nama, keperluan..."
                               class="w-full bg-white border border-slate-300 rounded-lg pl-3 pr-8 py-1.5 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-2xs transition">
                        <button x-show="searchQuery" 
                                @click="searchQuery = ''" 
                                type="button" 
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            &times;
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Pengajuan Surat (Header Biru Sesuai Permintaan) -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <!-- Table Header: Warna Biru #1d4ed8 -->
                    <thead>
                        <tr class="bg-[#1d4ed8] text-white">
                            <th scope="col" class="py-3 px-4 font-bold tracking-wider">No. Surat</th>
                            <th scope="col" class="py-3 px-4 font-bold tracking-wider">Tanggal</th>
                            <th scope="col" class="py-3 px-4 font-bold tracking-wider">Nama Pemohon</th>
                            <th scope="col" class="py-3 px-4 font-bold tracking-wider">Jenis Surat</th>
                            <th scope="col" class="py-3 px-4 font-bold tracking-wider">Keperluan</th>
                            <th scope="col" class="py-3 px-4 font-bold tracking-wider text-center">Status</th>
                            <th scope="col" class="py-3 px-4 font-bold tracking-wider">Catatan</th>
                            <th scope="col" class="py-3 px-4 font-bold tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <template x-for="(item, index) in paginatedItems" :key="item.id">
                            <tr class="hover:bg-blue-50/30 transition">
                                <!-- No. Surat -->
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-700 text-[11px] whitespace-nowrap">
                                    <span x-text="item.nomor_surat"></span>
                                </td>

                                <!-- Tanggal -->
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap" x-text="item.tanggal"></td>

                                <!-- Nama Pemohon & Info Rumah -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-800" x-text="item.nama_pengaju"></span>
                                        <span x-show="item.nomor_rumah && item.nomor_rumah !== '-'" 
                                              class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200"
                                              x-text="item.nomor_rumah"></span>
                                    </div>
                                </td>

                                <!-- Jenis Surat -->
                                <td class="py-3.5 px-4 text-slate-700 font-semibold" x-text="item.jenis_surat"></td>

                                <!-- Keperluan -->
                                <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate" :title="item.keperluan" x-text="item.keperluan"></td>

                                <!-- Status Badge -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <!-- Disetujui -->
                                    <template x-if="item.status === 'disetujui'">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Disetujui
                                        </span>
                                    </template>
                                    <!-- Menunggu -->
                                    <template x-if="item.status === 'menunggu'">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Menunggu
                                        </span>
                                    </template>
                                    <!-- Ditolak -->
                                    <template x-if="item.status === 'ditolak'">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            Ditolak
                                        </span>
                                    </template>
                                </td>

                                <!-- Catatan -->
                                <td class="py-3.5 px-4 text-slate-500 text-[11px]" x-text="item.catatan || '-'"></td>

                                <!-- Aksi Buttons (Checkmark, Edit, Print, Delete) -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Tombol Verifikasi / Ubah Status (Checkmark) -->
                                        <button type="button" 
                                                @click="openStatusModal(item)"
                                                title="Verifikasi Status Surat"
                                                class="w-7 h-7 rounded-lg border border-slate-300 hover:border-blue-500 hover:bg-blue-50 text-slate-500 hover:text-blue-600 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>

                                        <!-- Tombol Edit Data (Pencil) -->
                                        <button type="button" 
                                                @click="openEditModal(item)"
                                                title="Edit Data Surat"
                                                class="w-7 h-7 rounded-lg border border-slate-300 hover:border-blue-500 hover:bg-blue-50 text-slate-500 hover:text-blue-600 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>

                                        <!-- Tombol Cetak / Unduh Surat (Printer) -->
                                        <a :href="item.cetak_url" 
                                           target="_blank"
                                           title="Cetak Surat Resmi RT"
                                           class="w-7 h-7 rounded-lg border border-slate-300 hover:border-emerald-500 hover:bg-emerald-50 text-slate-500 hover:text-emerald-600 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </a>

                                        <!-- Tombol Hapus (Trash) -->
                                        <button type="button" 
                                                @click="openDeleteModal(item)"
                                                title="Hapus Surat"
                                                class="w-7 h-7 rounded-lg border border-slate-300 hover:border-rose-500 hover:bg-rose-50 text-slate-500 hover:text-rose-600 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <tr x-show="filteredItems.length === 0">
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-400 flex items-center justify-center mb-2">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-slate-600">Belum ada data pengajuan surat</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Surat yang diajukan warga atau admin akan tampil di sini</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Card Footer: Info Data & Pagination -->
            <div class="p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100 bg-white">
                <div class="text-xs text-slate-600">
                    <span x-text="`Data ${paginationInfo.from} - ${paginationInfo.to} dari ${paginationInfo.total}`"></span>
                </div>

                <!-- Pagination Buttons (Sesuai Mockup Screenshot) -->
                <div class="flex items-center gap-1.5" x-show="totalPages > 1">
                    <button type="button" 
                            @click="currentPage--"
                            :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        Sebelumnya
                    </button>

                    <template x-for="p in totalPages" :key="p">
                        <button type="button" 
                                @click="currentPage = p"
                                :class="currentPage === p ? 'bg-blue-600 text-white font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                                class="w-8 h-8 rounded-lg text-xs flex items-center justify-center transition"
                                x-text="p">
                        </button>
                    </template>

                    <button type="button" 
                            @click="currentPage++"
                            :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition">
                        Selanjutnya
                    </button>
                </div>
            </div>
        </div>

        <!-- ================= MODAL 1: TAMBAH / AJUKAN SURAT BARU ================= -->
        <div x-show="isAddModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" 
             aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="isAddModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 transition-all">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Ajukan Surat Baru</h3>
                                <p class="text-xs text-slate-500">Penerbitan surat keterangan atau pengantar untuk warga</p>
                            </div>
                        </div>
                        <button type="button" @click="isAddModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 text-xl font-bold">&times;</button>
                    </div>

                    <!-- Modal Form -->
                    <form @submit.prevent="submitAddSurat()" class="mt-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemohon *</label>
                            <input type="text" 
                                   x-model="formAdd.nama_pengaju" 
                                   required 
                                   list="wargaSuggestion"
                                   placeholder="Contoh: Arif atau pilih dari warga"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <datalist id="wargaSuggestion">
                                @foreach($wargaList as $w)
                                    <option value="{{ $w->nama_lengkap }}">{{ $w->nik }} (Rumah: {{ $w->nomor_rumah ?? '-' }})</option>
                                @endforeach
                            </datalist>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rumah / Blok *</label>
                            <input type="text" 
                                   x-model="formAdd.nomor_rumah" 
                                   required 
                                   placeholder="Contoh: Blok B No. 05"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Surat *</label>
                            <select x-model="formAdd.jenis_surat" 
                                    required 
                                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="Surat Pengantar RT">Surat Pengantar RT</option>
                                <option value="Surat Domisili">Surat Domisili</option>
                                <option value="Surat Usaha">Surat Usaha (SKU)</option>
                                <option value="Surat Pengantar SKCK">Surat Pengantar SKCK</option>
                                <option value="Surat Pengantar Nikah">Surat Pengantar Nikah</option>
                                <option value="Surat Keterangan Kematian">Surat Keterangan Kematian</option>
                                <option value="Surat Keterangan Kelahiran">Surat Keterangan Kelahiran</option>
                                <option value="Surat Keterangan Tidak Mampu (SKTM)">Surat Keterangan Tidak Mampu (SKTM)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keperluan / Keterangan *</label>
                            <textarea x-model="formAdd.keperluan" 
                                      required 
                                      rows="3" 
                                      placeholder="Contoh: Untuk Mengurus Sekolah / Melamar Pekerjaan"
                                      class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Status Awal</label>
                                <select x-model="formAdd.status" 
                                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="menunggu">Menunggu</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Surat (Opsional)</label>
                                <input type="text" 
                                       x-model="formAdd.nomor_surat" 
                                       placeholder="Kosongkan untuk otomatis"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <input type="text" 
                                   x-model="formAdd.catatan" 
                                   placeholder="Contoh: Dokumen fisik telah diserahkan"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" 
                                    @click="isAddModalOpen = false" 
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs transition shadow-sm">
                                <span x-show="!isSubmitting">Simpan Pengajuan</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= MODAL 2: VERIFIKASI / UBAH STATUS (CHECKMARK) ================= -->
        <div x-show="isStatusModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" 
             aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="isStatusModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 transition-all">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Verifikasi Status Surat</h3>
                                <p class="text-xs text-slate-500" x-text="selectedItem ? selectedItem.jenis_surat : ''"></p>
                            </div>
                        </div>
                        <button type="button" @click="isStatusModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 text-xl font-bold">&times;</button>
                    </div>

                    <!-- Modal Body -->
                    <form @submit.prevent="submitStatusUpdate()" class="mt-5 space-y-4">
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/70 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pemohon:</span>
                                <span class="font-bold text-slate-800" x-text="selectedItem ? selectedItem.nama_pengaju : ''"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Keperluan:</span>
                                <span class="font-medium text-slate-700" x-text="selectedItem ? selectedItem.keperluan : ''"></span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Status Baru *</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" 
                                        @click="formStatus.status = 'disetujui'"
                                        :class="formStatus.status === 'disetujui' ? 'bg-emerald-600 text-white font-bold ring-2 ring-emerald-300' : 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100'"
                                        class="py-2.5 px-2 rounded-xl text-xs font-semibold text-center transition flex flex-col items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Disetujui</span>
                                </button>

                                <button type="button" 
                                        @click="formStatus.status = 'menunggu'"
                                        :class="formStatus.status === 'menunggu' ? 'bg-amber-500 text-white font-bold ring-2 ring-amber-300' : 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100'"
                                        class="py-2.5 px-2 rounded-xl text-xs font-semibold text-center transition flex flex-col items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Menunggu</span>
                                </button>

                                <button type="button" 
                                        @click="formStatus.status = 'ditolak'"
                                        :class="formStatus.status === 'ditolak' ? 'bg-rose-600 text-white font-bold ring-2 ring-rose-300' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100'"
                                        class="py-2.5 px-2 rounded-xl text-xs font-semibold text-center transition flex flex-col items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span>Ditolak</span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Surat Resmi</label>
                            <input type="text" 
                                   x-model="formStatus.nomor_surat" 
                                   placeholder="Contoh: 001/PRT/RT/06/2026"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="text-[10px] text-slate-400 mt-1 block">Nomor ini yang akan tercetak resmi pada surat fisik / PDF.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Verifikasi (Ditampilkan ke Warga)</label>
                            <textarea x-model="formStatus.catatan" 
                                      rows="2" 
                                      placeholder="Contoh: Surat telah disetujui, silakan diunduh atau ambil tanda tangan di pos RT."
                                      class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" 
                                    @click="isStatusModalOpen = false" 
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs transition shadow-sm">
                                <span x-show="!isSubmitting">Simpan Status</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= MODAL 3: EDIT DATA SURAT (PENCIL) ================= -->
        <div x-show="isEditModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" 
             aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="isEditModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-lg bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 transition-all">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Edit Data Pengajuan Surat</h3>
                                <p class="text-xs text-slate-500">Perbarui rincian permohonan surat</p>
                            </div>
                        </div>
                        <button type="button" @click="isEditModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 text-xl font-bold">&times;</button>
                    </div>

                    <!-- Modal Form -->
                    <form @submit.prevent="submitEditSurat()" class="mt-5 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemohon *</label>
                            <input type="text" 
                                   x-model="formEdit.nama_pengaju" 
                                   required 
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Rumah / Blok *</label>
                            <input type="text" 
                                   x-model="formEdit.nomor_rumah" 
                                   required 
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Surat *</label>
                            <input type="text" 
                                   x-model="formEdit.jenis_surat" 
                                   required 
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keperluan / Keterangan *</label>
                            <textarea x-model="formEdit.keperluan" 
                                      required 
                                      rows="3" 
                                      class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                                <select x-model="formEdit.status" 
                                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="menunggu">Menunggu</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Surat</label>
                                <input type="text" 
                                       x-model="formEdit.nomor_surat" 
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs font-mono text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                            <input type="text" 
                                   x-model="formEdit.catatan" 
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" 
                                    @click="isEditModalOpen = false" 
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                                Batal
                            </button>
                            <button type="submit" 
                                    :disabled="isSubmitting"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs transition shadow-sm">
                                <span x-show="!isSubmitting">Perbarui Data</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ================= MODAL 4: HAPUS SURAT ================= -->
        <div x-show="isDeleteModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" 
             aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="isDeleteModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Hapus Surat Ini?</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Surat <span class="font-bold text-slate-700" x-text="selectedItem ? selectedItem.nomor_surat : ''"></span> milik <span class="font-bold text-slate-700" x-text="selectedItem ? selectedItem.nama_pengaju : ''"></span> akan dihapus dari arsip sistem.
                    </p>

                    <div class="flex items-center justify-center gap-3 mt-5">
                        <button type="button" 
                                @click="isDeleteModalOpen = false" 
                                class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                        <button type="button" 
                                @click="submitDeleteSurat()"
                                :disabled="isSubmitting"
                                class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold text-xs transition shadow-sm">
                            <span x-show="!isSubmitting">Ya, Hapus</span>
                            <span x-show="isSubmitting">Menghapus...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Component Script -->
    <script>
        function suratManagement(initialData) {
            return {
                surats: initialData || [],
                searchQuery: '',
                perPage: 10,
                currentPage: 1,
                toastMessage: null,
                isSubmitting: false,

                // Modal States
                isAddModalOpen: false,
                isStatusModalOpen: false,
                isEditModalOpen: false,
                isDeleteModalOpen: false,
                selectedItem: null,

                // Forms
                formAdd: {
                    nama_pengaju: '',
                    nomor_rumah: '',
                    jenis_surat: 'Surat Pengantar RT',
                    keperluan: '',
                    status: 'menunggu',
                    nomor_surat: '',
                    catatan: ''
                },
                formStatus: {
                    status: 'disetujui',
                    nomor_surat: '',
                    catatan: ''
                },
                formEdit: {
                    id: null,
                    nama_pengaju: '',
                    nomor_rumah: '',
                    jenis_surat: '',
                    keperluan: '',
                    status: 'menunggu',
                    nomor_surat: '',
                    catatan: ''
                },

                init() {
                    // Reset page on search or perPage change
                    this.$watch('searchQuery', () => { this.currentPage = 1; });
                    this.$watch('perPage', () => { this.currentPage = 1; });
                },

                countByStatus(status) {
                    return this.surats.filter(s => s.status === status).length;
                },

                get filteredItems() {
                    if (!this.searchQuery.trim()) {
                        return this.surats;
                    }
                    const q = this.searchQuery.toLowerCase();
                    return this.surats.filter(item => {
                        return (item.nomor_surat && item.nomor_surat.toLowerCase().includes(q)) ||
                               (item.nama_pengaju && item.nama_pengaju.toLowerCase().includes(q)) ||
                               (item.jenis_surat && item.jenis_surat.toLowerCase().includes(q)) ||
                               (item.keperluan && item.keperluan.toLowerCase().includes(q)) ||
                               (item.status && item.status.toLowerCase().includes(q)) ||
                               (item.catatan && item.catatan.toLowerCase().includes(q)) ||
                               (item.tanggal && item.tanggal.toLowerCase().includes(q));
                    });
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredItems.length / this.perPage));
                },

                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredItems.slice(start, start + this.perPage);
                },

                get paginationInfo() {
                    const total = this.filteredItems.length;
                    if (total === 0) return { from: 0, to: 0, total: 0 };
                    const from = (this.currentPage - 1) * this.perPage + 1;
                    const to = Math.min(this.currentPage * this.perPage, total);
                    return { from, to, total };
                },

                // Open Modals
                openAddModal() {
                    this.formAdd = {
                        nama_pengaju: '',
                        nomor_rumah: '',
                        jenis_surat: 'Surat Pengantar RT',
                        keperluan: '',
                        status: 'menunggu',
                        nomor_surat: '',
                        catatan: ''
                    };
                    this.isAddModalOpen = true;
                },

                openStatusModal(item) {
                    this.selectedItem = item;
                    this.formStatus = {
                        status: item.status,
                        nomor_surat: item.nomor_surat,
                        catatan: item.catatan && item.catatan !== '-' ? item.catatan : ''
                    };
                    this.isStatusModalOpen = true;
                },

                openEditModal(item) {
                    this.selectedItem = item;
                    this.formEdit = {
                        id: item.id,
                        nama_pengaju: item.nama_pengaju,
                        nomor_rumah: item.nomor_rumah,
                        jenis_surat: item.jenis_surat,
                        keperluan: item.keperluan,
                        status: item.status,
                        nomor_surat: item.nomor_surat,
                        catatan: item.catatan && item.catatan !== '-' ? item.catatan : ''
                    };
                    this.isEditModalOpen = true;
                },

                openDeleteModal(item) {
                    this.selectedItem = item;
                    this.isDeleteModalOpen = true;
                },

                showToast(msg) {
                    this.toastMessage = msg;
                    setTimeout(() => {
                        if (this.toastMessage === msg) this.toastMessage = null;
                    }, 5000);
                },

                // Async Submissions with CSRF Header
                async submitAddSurat() {
                    this.isSubmitting = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch("{{ route('admin.surat.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify(this.formAdd)
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            const newSurat = {
                                id: data.surat.id,
                                nomor_surat: data.surat.nomor_surat,
                                tanggal: new Date().toISOString().slice(0, 10),
                                tanggal_lengkap: 'Hari ini',
                                nama_pengaju: data.surat.nama_pengaju,
                                nomor_rumah: data.surat.nomor_rumah,
                                jenis_surat: data.surat.jenis_surat,
                                keperluan: data.surat.keperluan,
                                status: data.surat.status,
                                status_label: data.surat.status === 'disetujui' ? 'Disetujui' : (data.surat.status === 'ditolak' ? 'Ditolak' : 'Menunggu'),
                                catatan: data.surat.catatan || '-',
                                cetak_url: `/admin/surat/${data.surat.id}/cetak`
                            };
                            this.surats.unshift(newSurat);
                            this.isAddModalOpen = false;
                            this.showToast(data.message || 'Pengajuan surat berhasil ditambahkan.');
                        } else {
                            alert(data.message || 'Gagal menyimpan pengajuan surat.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan koneksi server.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async submitStatusUpdate() {
                    if (!this.selectedItem) return;
                    this.isSubmitting = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch(`/admin/surat/${this.selectedItem.id}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify(this.formStatus)
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            const idx = this.surats.findIndex(s => s.id === this.selectedItem.id);
                            if (idx !== -1) {
                                this.surats[idx].status = data.surat.status;
                                this.surats[idx].status_label = data.surat.status_label;
                                this.surats[idx].nomor_surat = data.surat.nomor_surat;
                                this.surats[idx].catatan = data.surat.catatan;
                            }
                            this.isStatusModalOpen = false;
                            this.showToast(data.message || 'Status surat berhasil diperbarui.');
                        } else {
                            alert(data.message || 'Gagal memperbarui status.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan koneksi server.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async submitEditSurat() {
                    if (!this.selectedItem) return;
                    this.isSubmitting = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch(`/admin/surat/${this.selectedItem.id}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify(this.formEdit)
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            const idx = this.surats.findIndex(s => s.id === this.selectedItem.id);
                            if (idx !== -1) {
                                this.surats[idx].nama_pengaju = data.surat.nama_pengaju;
                                this.surats[idx].nomor_rumah = data.surat.nomor_rumah;
                                this.surats[idx].jenis_surat = data.surat.jenis_surat;
                                this.surats[idx].keperluan = data.surat.keperluan;
                                this.surats[idx].status = data.surat.status;
                                this.surats[idx].nomor_surat = data.surat.nomor_surat;
                                this.surats[idx].catatan = data.surat.catatan;
                            }
                            this.isEditModalOpen = false;
                            this.showToast(data.message || 'Data surat berhasil diperbarui.');
                        } else {
                            alert(data.message || 'Gagal memperbarui surat.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan koneksi server.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async submitDeleteSurat() {
                    if (!this.selectedItem) return;
                    this.isSubmitting = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch(`/admin/surat/${this.selectedItem.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            }
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.surats = this.surats.filter(s => s.id !== this.selectedItem.id);
                            this.isDeleteModalOpen = false;
                            this.showToast(data.message || 'Surat berhasil dihapus.');
                        } else {
                            alert(data.message || 'Gagal menghapus surat.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan koneksi server.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>
</x-admin-layout>
