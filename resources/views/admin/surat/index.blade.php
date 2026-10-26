<x-admin-layout>
    @php
        $formattedSurats = $surats->map(function ($item) use ($wargaList) {
            $warga = $wargaList->first(function($w) use ($item) {
                return strtolower($w->nama_lengkap) === strtolower($item->nama_pengaju) ||
                       strtolower($w->nomor_rumah) === strtolower($item->nomor_rumah);
            });

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
                'nik_pemohon' => $warga?->nik ?: '-',
                'no_hp_pemohon' => $warga?->no_hp ?: '',
                'blok_pemohon' => $warga?->blok ?: ($item->nomor_rumah ?: '-'),
                'status_tinggal' => $warga?->status_tinggal ?: 'tetap',
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
                   title="Lihat Website"
                   class="h-9 px-3 sm:px-3.5 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:text-blue-600 hover:border-blue-300 inline-flex items-center gap-1.5 sm:gap-2 text-xs font-semibold transition shadow-2xs shrink-0 group">
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span>Lihat Website</span>
                </a>
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

                                <!-- Aksi Buttons (Verifikasi, Lihat Surat, Kirim Surat, Hapus) -->
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

                                        <!-- Tombol Lihat Surat / Detail Surat (Eye) -->
                                        <button type="button" 
                                                @click="openDetailSuratModal(item)"
                                                title="Lihat & Baca Dokumen Surat"
                                                class="w-7 h-7 rounded-lg border border-slate-300 hover:border-indigo-500 hover:bg-indigo-50 text-slate-500 hover:text-indigo-600 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        <!-- Tombol Kirim Surat ke Pemohon (Paper Plane) -->
                                        <button type="button" 
                                                @click="openKirimModal(item)"
                                                title="Kirim Surat ke Pemohon"
                                                class="w-7 h-7 rounded-lg border border-slate-300 hover:border-emerald-500 hover:bg-emerald-50 text-slate-500 hover:text-emerald-600 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                        </button>

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

        <!-- ================= MODAL 5: DETAIL / PRATINJAU DOKUMEN SURAT RESMI ================= -->
        <div x-show="isDetailSuratModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" 
             aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="isDetailSuratModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 my-6">
                <div class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl border border-slate-100 transition-all overflow-hidden flex flex-col max-h-[90vh]">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/70 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Pratinjau & Isi Dokumen Surat</h3>
                                <p class="text-xs text-slate-500">Lihat berkas surat resmi RT sebelum dikirimkan ke pemohon</p>
                            </div>
                        </div>
                        <button type="button" @click="isDetailSuratModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 text-xl font-bold transition">&times;</button>
                    </div>

                    <!-- Modal Body: Paper Surat View -->
                    <div class="p-4 sm:p-6 overflow-y-auto bg-slate-100 flex-1 space-y-4">
                        <!-- Lembar Kertas Dokumen Resmi -->
                        <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-10 text-slate-900 leading-relaxed text-xs sm:text-sm font-serif">
                            
                            <!-- Kop Surat -->
                            <div class="text-center relative pb-3 border-b-2 border-slate-900 space-y-1">
                                <h3 class="text-xs sm:text-sm font-bold tracking-wider uppercase leading-none font-sans">PENGURUS RUKUN TETANGGA 01 / RW 05</h3>
                                <h2 class="text-base sm:text-lg font-bold tracking-tight uppercase leading-snug font-sans text-slate-900">KELURAHAN / DESA SEJAHTERA</h2>
                                <p class="text-[11px] sm:text-xs text-slate-600 leading-tight font-sans">
                                    Sekretariat: Balai Pertemuan RT 01, Blok Sentral No. 01 • Telp/WA: 0812-3456-7890 • E-Mail: sekretariat@rt01sejahtera.id
                                </p>
                            </div>
                            <!-- Garis Ganda Pembatas Kop Surat -->
                            <div class="border-b border-slate-900 mt-0.5 mb-5"></div>

                            <!-- Judul & Nomor Surat -->
                            <div class="text-center my-4 space-y-1">
                                <h1 class="text-sm sm:text-base font-bold tracking-wider underline uppercase text-slate-900" x-text="selectedItem ? selectedItem.jenis_surat.toUpperCase() : 'SURAT PENGANTAR RT'"></h1>
                                <p class="text-xs text-slate-700 font-semibold tracking-wide font-sans">
                                    Nomor: <span class="font-mono text-slate-900" x-text="selectedItem ? selectedItem.nomor_surat : '-'"></span>
                                </p>
                            </div>

                            <!-- Konten Surat -->
                            <div class="space-y-3.5 text-justify leading-relaxed mt-4">
                                <p>
                                    Yang bertanda tangan di bawah ini, Ketua / Pengurus Rukun Tetangga (RT) 01 Rukun Warga (RW) 05, dengan ini menerangkan bahwa:
                                </p>

                                <!-- Tabel Identitas Pemohon -->
                                <table class="w-full my-2 text-xs sm:text-sm font-sans">
                                    <tbody>
                                        <tr class="align-top">
                                            <td class="w-40 py-1 font-bold text-slate-700">Nama Lengkap</td>
                                            <td class="w-3 py-1">:</td>
                                            <td class="py-1 uppercase font-bold text-slate-900" x-text="selectedItem ? selectedItem.nama_pengaju : '-'"></td>
                                        </tr>
                                        <tr class="align-top">
                                            <td class="w-40 py-1 font-bold text-slate-700">NIK</td>
                                            <td class="w-3 py-1">:</td>
                                            <td class="py-1 font-mono text-slate-900" x-text="selectedItem ? (selectedItem.nik_pemohon || '-') : '-'"></td>
                                        </tr>
                                        <tr class="align-top">
                                            <td class="w-40 py-1 font-bold text-slate-700">Nomor Rumah / Alamat</td>
                                            <td class="w-3 py-1">:</td>
                                            <td class="py-1 text-slate-800" x-text="selectedItem ? (selectedItem.nomor_rumah + (selectedItem.blok_pemohon ? ' (Blok ' + selectedItem.blok_pemohon + ')' : '')) : '-'"></td>
                                        </tr>
                                        <tr class="align-top">
                                            <td class="w-40 py-1 font-bold text-slate-700">Status Tempat Tinggal</td>
                                            <td class="w-3 py-1">:</td>
                                            <td class="py-1 capitalize text-slate-800" x-text="selectedItem ? (selectedItem.status_tinggal || 'Warga Tetap') : 'Warga Tetap'"></td>
                                        </tr>
                                        <tr class="align-top">
                                            <td class="w-40 py-1 font-bold text-slate-700">Kategori / Jenis Surat</td>
                                            <td class="w-3 py-1">:</td>
                                            <td class="py-1 font-bold text-blue-900 uppercase" x-text="selectedItem ? selectedItem.jenis_surat : '-'"></td>
                                        </tr>
                                        <tr class="align-top">
                                            <td class="w-40 py-1 font-bold text-slate-700">Keperluan / Keterangan</td>
                                            <td class="w-3 py-1">:</td>
                                            <td class="py-1 text-slate-800 italic" x-text="selectedItem ? ('&ldquo;' + selectedItem.keperluan + '&rdquo;') : '-'"></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <p>
                                    Berdasarkan catatan kependudukan lingkungan, nama yang bersangkutan di atas adalah benar warga yang berdomisili sah di wilayah RT 01 / RW 05 dan memiliki kelakuan baik dalam kehidupan bermasyarakat.
                                </p>

                                <p>
                                    Surat keterangan / pengantar ini diterbitkan secara sah kepada yang bersangkutan untuk dipergunakan sebagai kelengkapan berkas administrasi sesuai dengan keperluan tersebut di atas.
                                </p>

                                <p>
                                    Demikian surat ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya oleh pihak yang berkepentingan.
                                </p>
                            </div>

                            <!-- Tanda Tangan & Cap Stempel RT -->
                            <div class="mt-8 flex justify-between items-start text-xs sm:text-sm font-sans">
                                <div class="w-40 sm:w-48 text-center space-y-1">
                                    <p class="font-bold text-slate-700">Pemohon / Warga,</p>
                                    <div class="h-16 flex items-center justify-center"></div>
                                    <p class="font-bold uppercase underline text-slate-900" x-text="selectedItem ? selectedItem.nama_pengaju : '( ................................ )'"></p>
                                </div>

                                <div class="w-56 sm:w-64 text-center space-y-1 relative">
                                    <p class="text-slate-700 text-xs">
                                        Dikeluarkan di: Jakarta<br>
                                        Pada tanggal: <strong x-text="selectedItem ? (selectedItem.tanggal || '-') : '-'"></strong>
                                    </p>
                                    <p class="font-bold text-xs mt-1">Pengurus RT 01 / RW 05,</p>
                                    <p class="font-bold text-xs -mt-1">Ketua RT 01</p>

                                    <!-- Stempel & Tanda Tangan Digital RT -->
                                    <div class="h-16 relative flex items-center justify-center my-1">
                                        <!-- Cap Stempel Digital -->
                                        <div class="absolute w-20 h-20 rounded-full border-2 border-dashed border-blue-600/70 text-blue-700 flex flex-col items-center justify-center text-[8px] font-black uppercase tracking-tight transform -rotate-12 opacity-80 pointer-events-none">
                                            <span>★ RT 01 / RW 05 ★</span>
                                            <span class="text-[6.5px]">TERVERIFIKASI</span>
                                            <span class="text-[6.5px]">RESMI DIGITAL</span>
                                        </div>
                                        <!-- Tanda Tangan Digital -->
                                        <div class="font-serif italic text-base text-slate-800 font-bold z-10 select-none">
                                            Pengurus RT 01
                                        </div>
                                    </div>

                                    <p class="font-bold uppercase underline text-xs text-slate-900 mt-1">BAMBANG SUTEDJO, S.E.</p>
                                    <p class="text-[10px] text-slate-500 -mt-1 font-sans">Ketua RT 01 Periode 2024 - 2027</p>
                                </div>
                            </div>

                            <!-- Footer Berkas Digital -->
                            <div class="mt-8 pt-3 border-t border-slate-200 text-[10px] text-slate-400 flex items-center justify-between font-sans">
                                <span>Dokumen resmi diterbitkan melalui Sistem Digital E-KAS RT.</span>
                                <span>ID Berkas: RT01-SRT-<span x-text="selectedItem ? String(selectedItem.id).padStart(5, '0') : '00000'"></span></span>
                            </div>

                        </div>

                        <!-- Card Rangkuman Status Surat -->
                        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-700">Status Saat Ini:</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold"
                                      :class="{
                                          'bg-emerald-100 text-emerald-800 border border-emerald-200': selectedItem?.status === 'disetujui',
                                          'bg-amber-100 text-amber-800 border border-amber-200': selectedItem?.status === 'menunggu',
                                          'bg-rose-100 text-rose-800 border border-rose-200': selectedItem?.status === 'ditolak'
                                      }"
                                      x-text="selectedItem ? selectedItem.status_label : '-'">
                                </span>
                            </div>
                            <div class="text-slate-500">
                                <span>No. WhatsApp Pemohon: </span>
                                <strong class="text-slate-800 font-mono" x-text="selectedItem?.no_hp_pemohon || 'Belum terdaftar'"></strong>
                            </div>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-6 py-4 border-t border-slate-100 bg-white shrink-0">
                        <button type="button" 
                                @click="isDetailSuratModalOpen = false" 
                                class="w-full sm:w-auto px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition text-center">
                            Tutup
                        </button>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <!-- Tombol Cetak PDF -->
                            <a :href="selectedItem ? selectedItem.cetak_url : '#'" 
                               target="_blank"
                               class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-2xs">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span>Cetak / PDF</span>
                            </a>

                            <!-- Tombol Kirim Surat ke Pemohon -->
                            <button type="button" 
                                    @click="isDetailSuratModalOpen = false; openKirimModal(selectedItem)"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span>Kirim Surat ke Pemohon</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= MODAL 6: KIRIM SURAT KE PEMOHON ================= -->
        <div x-show="isKirimModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             role="dialog" 
             aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="isKirimModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-3 sm:p-4 my-6">
                <div class="relative w-full max-w-xl bg-white rounded-3xl p-6 sm:p-7 shadow-2xl border border-slate-100 transition-all">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-800">Kirim Surat ke Pemohon</h3>
                                <p class="text-xs text-slate-500">Kirim dokumen via WhatsApp & terbitkan langsung ke Portal Warga</p>
                            </div>
                        </div>
                        <button type="button" @click="isKirimModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 text-xl font-bold transition">&times;</button>
                    </div>

                    <!-- Ringkasan Info Surat & Pemohon -->
                    <div class="mt-4 p-4 rounded-2xl bg-blue-50/60 border border-blue-100 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Nama Pemohon:</span>
                            <span class="font-bold text-slate-800 uppercase" x-text="selectedItem ? selectedItem.nama_pengaju : '-'"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Kategori Surat:</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold bg-blue-100 text-blue-700" x-text="selectedItem ? selectedItem.jenis_surat : '-'"></span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Nomor Surat:</span>
                            <span class="font-mono font-bold text-slate-700" x-text="selectedItem ? selectedItem.nomor_surat : '-'"></span>
                        </div>
                    </div>

                    <!-- Form Pengiriman -->
                    <form @submit.prevent="submitKirimSurat(false)" class="mt-5 space-y-4">
                        <!-- Nomor WhatsApp Pemohon -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                Nomor WhatsApp / Telepon Pemohon
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600 font-bold text-xs">
                                    WA:
                                </div>
                                <input type="text" 
                                       x-model="formKirim.no_hp" 
                                       placeholder="Contoh: 08123456789 atau 628123456789"
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-12 pr-3.5 py-2.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition font-mono">
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">Nomor otomatis diambil dari database profil warga jika sudah terdaftar.</p>
                        </div>

                        <!-- Template Pesan Sesuai Kategori -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-slate-700">
                                    Pesan Pemberitahuan (Disesuaikan Kategori Surat)
                                </label>
                                <button type="button" 
                                        @click="formKirim.pesan = generatePesanKirim(selectedItem)" 
                                        class="text-[11px] font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    Reset Template
                                </button>
                            </div>
                            <textarea x-model="formKirim.pesan" 
                                      rows="7" 
                                      required 
                                      class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3.5 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-sans leading-relaxed"></textarea>
                        </div>

                        <!-- Catatan Sistem -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Pengiriman (Tersimpan di Arsip Surat)</label>
                            <input type="text" 
                                   x-model="formKirim.catatan" 
                                   placeholder="Contoh: Surat resmi telah dikirim ke pemohon via WhatsApp & Portal"
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-2.5">
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="button" 
                                        @click="isKirimModalOpen = false" 
                                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                                    Batal
                                </button>
                                <button type="button" 
                                        @click="copyPesanKirim()" 
                                        title="Salin Teks Pesan"
                                        class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-bold transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                    </svg>
                                    <span>Salin</span>
                                </button>
                            </div>

                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <!-- Tombol Kirim WhatsApp -->
                                <button type="button" 
                                        @click="submitKirimSurat(true)"
                                        :disabled="isSubmitting"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold text-xs transition shadow-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.288.043.088.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.071.376-.044.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.203c.043.072.043.419-.101.824z"/>
                                    </svg>
                                    <span>Kirim via WhatsApp</span>
                                </button>

                                <!-- Tombol Terbitkan ke Portal -->
                                <button type="submit" 
                                        :disabled="isSubmitting"
                                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs transition shadow-sm">
                                    <span x-show="!isSubmitting">Kirim ke Portal</span>
                                    <span x-show="isSubmitting">Memproses...</span>
                                </button>
                            </div>
                        </div>
                    </form>
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
                isDetailSuratModalOpen: false,
                isKirimModalOpen: false,
                selectedItem: null,

                init() {
                    // Polling real-time sinkronisasi pengajuan surat baru setiap 4 detik
                    setInterval(() => {
                        // Jangan timpa array jika modal sedang aktif terbuka
                        if (this.isAddModalOpen || this.isEditModalOpen || this.isStatusModalOpen || this.isDeleteModalOpen) return;

                        fetch('{{ route('admin.surat.index') }}', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success && res.surats) {
                                if (res.surats.length > this.surats.length) {
                                    this.toastMessage = '📄 Ada permohonan surat baru yang masuk!';
                                    setTimeout(() => { this.toastMessage = null; }, 5000);
                                }
                                this.surats = res.surats;
                            }
                        })
                        .catch(() => {});
                    }, 4000);
                },

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
                formKirim: {
                    no_hp: '',
                    pesan: '',
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

                openDetailSuratModal(item) {
                    this.selectedItem = item;
                    this.isDetailSuratModalOpen = true;
                },

                openKirimModal(item) {
                    this.selectedItem = item;
                    this.formKirim.no_hp = item.no_hp_pemohon || '';
                    this.formKirim.pesan = this.generatePesanKirim(item);
                    this.formKirim.catatan = 'Surat telah dikirimkan ke pemohon via WhatsApp dan Portal Warga.';
                    this.isKirimModalOpen = true;
                },

                generatePesanKirim(item) {
                    if (!item) return '';
                    const nama = item.nama_pengaju || 'Bpk/Ibu Warga';
                    const noSurat = item.nomor_surat || '-';
                    const jenis = item.jenis_surat || 'Surat Pengantar RT';
                    const keperluan = item.keperluan || '-';
                    const link = item.cetak_url || (window.location.origin + `/admin/surat/${item.id}/cetak`);
                    const jenisLower = jenis.toLowerCase();

                    if (jenisLower.includes('nikah')) {
                        return `Yth. ${nama},\n\nPermohonan ${jenis} Anda (Nomor: ${noSurat}) telah selesai diverifikasi dan DISETUJUI oleh Pengurus RT 01.\n\nKeperluan: ${keperluan}\nDokumen surat resmi telah diterbitkan dan dapat diunduh melalui Portal Warga atau tautan resmi berikut:\n${link}\n\nSilakan membawa cetakan berkas ini ke kantor Kelurahan untuk proses pencatatan nikah selanjutnya.\n\nSalam hormat,\nPengurus RT 01 RW 06 Sukamakmur`;
                    } else if (jenisLower.includes('usaha') || jenisLower.includes('sku')) {
                        return `Yth. ${nama},\n\nPermohonan Surat Keterangan Usaha (SKU) Anda (Nomor: ${noSurat}) telah selesai diterbitkan dan DISETUJUI oleh Pengurus RT 01.\n\nKeperluan: ${keperluan}\nDokumen resmi dapat diakses dan diunduh di Portal Warga atau tautan:\n${link}\n\nSurat ini sah berlaku untuk kelengkapan administrasi perbankan/perizinan usaha Anda.\n\nSalam hormat,\nPengurus RT 01 RW 06 Sukamakmur`;
                    } else if (jenisLower.includes('skck')) {
                        return `Yth. ${nama},\n\nPermohonan Surat Pengantar SKCK Anda (Nomor: ${noSurat}) telah selesai disetujui oleh Pengurus RT 01.\n\nKeperluan: ${keperluan}\nDokumen resmi dapat diunduh pada tautan berikut:\n${link}\n\nSilakan bawa cetakan surat pengantar ini ke Polsek/Polres untuk penerbitan SKCK.\n\nSalam hormat,\nPengurus RT 01 RW 06 Sukamakmur`;
                    } else if (jenisLower.includes('domisili')) {
                        return `Yth. ${nama},\n\nSurat Keterangan Domisili Anda (Nomor: ${noSurat}) telah selesai diverifikasi dan DISETUJUI oleh Pengurus RT 01.\n\nAlamat/Rumah: ${item.nomor_rumah || '-'}\nKeperluan: ${keperluan}\nDokumen resmi dapat diunduh di:\n${link}\n\nSalam hormat,\nPengurus RT 01 RW 06 Sukamakmur`;
                    } else if (jenisLower.includes('mati') || jenisLower.includes('kematian')) {
                        return `Yth. Keluarga ${nama},\n\nSurat Keterangan Kematian (Nomor: ${noSurat}) telah selesai diterbitkan oleh Pengurus RT 01.\n\nKeperluan: ${keperluan}\nDokumen resmi dapat diunduh di:\n${link}\n\nTurut berduka cita yang sedalam-dalamnya atas musibah yang dialami keluarga.\n\nSalam hormat,\nPengurus RT 01 RW 06 Sukamakmur`;
                    } else if (jenisLower.includes('lahir') || jenisLower.includes('kelahiran')) {
                        return `Yth. ${nama},\n\nSurat Keterangan Kelahiran (Nomor: ${noSurat}) telah selesai diterbitkan oleh Pengurus RT 01.\n\nKeperluan: ${keperluan}\nDokumen resmi dapat diunduh di:\n${link}\n\nSelamat atas kelahiran putra/putri tercinta, semoga tumbuh sehat dan berkah.\n\nSalam hormat,\nPengurus RT 01 RW 06 Sukamakmur`;
                    } else if (jenisLower.includes('tidak mampu') || jenisLower.includes('sktm')) {
                        return `Yth. ${nama},\n\nSurat Keterangan Tidak Mampu (SKTM) Anda (Nomor: ${noSurat}) telah selesai diverifikasi dan DISETUJUI oleh Pengurus RT 01.\n\nKeperluan: ${keperluan}\nDokumen resmi dapat diunduh di:\n${link}\n\nSemoga bantuan dan kemudahan administrasi segera terwujud.\n\nSalam hormat,\nPengurus RT 01 RW 06 Sukamakmur`;
                    } else {
                        return `Yth. ${nama},\n\nPermohonan ${jenis} Anda (Nomor: ${noSurat}) telah selesai diproses dan DISETUJUI oleh Pengurus RT 01.\n\nKeperluan: ${keperluan}\nDokumen resmi dapat diunduh melalui tautan berikut atau melalui Portal Warga:\n${link}\n\nTerima kasih atas kerja samanya.\n\nSalam hormat,\nPengurus RT 01 RW 06 Sukamakmur`;
                    }
                },

                copyPesanKirim() {
                    if (!this.formKirim.pesan) return;
                    navigator.clipboard.writeText(this.formKirim.pesan).then(() => {
                        this.showToast('Teks pesan pemberitahuan berhasil disalin ke clipboard!');
                    }).catch(() => {
                        alert('Gagal menyalin teks secara otomatis. Silakan salin secara manual.');
                    });
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
                },

                async submitKirimSurat(openWhatsApp = false) {
                    if (!this.selectedItem) return;
                    this.isSubmitting = true;
                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        const res = await fetch(`/admin/surat/${this.selectedItem.id}/kirim`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify(this.formKirim)
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
                            this.isKirimModalOpen = false;
                            this.showToast(data.message || 'Surat berhasil dikirim ke pemohon.');

                            if (openWhatsApp) {
                                if (data.whatsapp_url) {
                                    window.open(data.whatsapp_url, '_blank');
                                } else {
                                    let phone = (this.formKirim.no_hp || '').replace(/[^0-9]/g, '');
                                    if (phone.startsWith('0')) phone = '62' + phone.substring(1);
                                    if (phone) {
                                        const waUrl = `https://wa.me/${phone}?text=` + encodeURIComponent(this.formKirim.pesan);
                                        window.open(waUrl, '_blank');
                                    } else {
                                        alert('Nomor WhatsApp belum terisi. Berkas surat tetap berhasil dikirim dan dipublikasikan ke Portal Warga pemohon.');
                                    }
                                }
                            }
                        } else {
                            alert(data.message || 'Gagal mengirimkan surat.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan koneksi server.');
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                countByStatus(status) {
                    if (!this.surats) return 0;
                    if (status === 'menunggu') {
                        return this.surats.filter(s => ['menunggu', 'pending'].includes((s.status || '').toLowerCase())).length;
                    }
                    if (status === 'disetujui') {
                        return this.surats.filter(s => ['disetujui', 'selesai'].includes((s.status || '').toLowerCase())).length;
                    }
                    return this.surats.filter(s => (s.status || '').toLowerCase() === status.toLowerCase()).length;
                }
            };
        }
    </script>
</x-admin-layout>
