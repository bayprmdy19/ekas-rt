<x-admin-layout>
    @php
        $formattedKK = $kartuKeluargas->map(function ($item) {
            return [
                'id' => $item->id,
                'nomor_kk' => $item->nomor_kk ?: '-',
                'kepala_keluarga' => $item->kepala_keluarga,
                'nik_kepala_keluarga' => $item->nik_kepala_keluarga ?: '-',
                'nomor_rumah' => $item->nomor_rumah,
                'rt' => $item->rt ?: '01',
                'rw' => $item->rw ?: '01',
                'no_hp' => $item->no_hp ?: '-',
                'jumlah_anggota' => $item->jumlah_anggota ?: 1,
                'status_verifikasi' => $item->status_verifikasi,
                'status_label' => $item->status_label,
                'catatan_admin' => $item->catatan_admin ?: '',
                'foto_kk_url' => $item->foto_kk_url,
                'foto_ktp_url' => $item->foto_ktp_url,
                'foto_bpjs_url' => $item->foto_bpjs_url,
                'foto_lainnya_url' => $item->foto_lainnya_url,
                'is_lengkap' => $item->isLengkap(),
                'kelengkapan' => $item->kelengkapan,
                'created_at_formatted' => $item->created_at ? $item->created_at->translatedFormat('d M Y') : '-',
            ];
        });
    @endphp

    <div x-data="kkManagement(@js($formattedKK))" class="p-5 sm:p-7 space-y-6">

        <!-- Top Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                        📁
                    </span>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">
                        Data Kartu Keluarga (KK)
                    </h1>
                </div>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    Manajemen data KK warga, pemeriksaan foto berkas identitas resmi (Foto KK, KTP, BPJS), dan status verifikasi kependudukan.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('public.dashboard') }}" 
                   title="Lihat Website"
                   class="h-9 px-3 sm:px-3.5 rounded-xl bg-white border border-slate-200/90 text-slate-600 hover:text-blue-600 hover:border-blue-300 inline-flex items-center gap-1.5 sm:gap-2 text-xs font-semibold transition shadow-xs shrink-0 group">
                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span>Lihat Website</span>
                </a>
                <button type="button" 
                        @click="openAddModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1d4ed8] hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Data KK</span>
                </button>
            </div>
        </div>

        <!-- Flash Message Alerts -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 font-bold">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 font-semibold flex items-center justify-between shadow-2xs">
                <span>{{ session('error') }}</span>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-500 font-bold">&times;</button>
            </div>
        @endif

        <!-- 4 Metric Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total KK -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Kartu Keluarga</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">👨‍👩‍👧‍👦</span>
                </div>
                <div class="text-2xl font-black text-slate-800 mt-2">{{ $totalKK }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Keluarga terdaftar di RT 01</div>
            </div>

            <!-- Berkas Lengkap -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Dokumen Lengkap</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">✓</span>
                </div>
                <div class="text-2xl font-black text-emerald-600 mt-2">{{ $totalDokumenLengkap }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Foto KK, KTP & BPJS terunggah</div>
            </div>

            <!-- Menunggu Verifikasi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Verifikasi</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">⏳</span>
                </div>
                <div class="text-2xl font-black text-amber-600 mt-2">{{ $totalMenunggu }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Perlu ditinjau pengurus RT</div>
            </div>

            <!-- Terverifikasi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Sah & Terverifikasi</span>
                    <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs">🛡️</span>
                </div>
                <div class="text-2xl font-black text-blue-600 mt-2">{{ $totalTerverifikasi }}</div>
                <div class="text-[11px] text-slate-400 mt-1">Data valid & terverifikasi</div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-100 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari kepala keluarga, No. KK, NIK, atau nomor rumah..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50 text-slate-800 placeholder:text-slate-400" />
                </div>

                <!-- Status Filter Buttons -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <button type="button" 
                            @click="statusFilter = 'all'"
                            :class="statusFilter === 'all' ? 'bg-[#1d4ed8] text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                            class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                        Semua (<span x-text="items.length"></span>)
                    </button>
                    <button type="button" 
                            @click="statusFilter = 'menunggu'"
                            :class="statusFilter === 'menunggu' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                            class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                        Menunggu (<span x-text="items.filter(i => i.status_verifikasi === 'menunggu').length"></span>)
                    </button>
                    <button type="button" 
                            @click="statusFilter = 'terverifikasi'"
                            :class="statusFilter === 'terverifikasi' ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                            class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                        Terverifikasi (<span x-text="items.filter(i => i.status_verifikasi === 'terverifikasi').length"></span>)
                    </button>
                    <button type="button" 
                            @click="statusFilter = 'ditolak'"
                            :class="statusFilter === 'ditolak' ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold'"
                            class="px-3 py-1.5 rounded-xl text-xs whitespace-nowrap transition">
                        Ditolak (<span x-text="items.filter(i => i.status_verifikasi === 'ditolak').length"></span>)
                    </button>
                </div>
            </div>

            <!-- Tabel Data KK -->
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/70 border-b border-slate-100">
                            <th class="py-3.5 px-4">Kepala Keluarga & No. KK</th>
                            <th class="py-3.5 px-4">Alamat Rumah & Kontak</th>
                            <th class="py-3.5 px-4 text-center">Anggota</th>
                            <th class="py-3.5 px-4 text-center">Foto Berkas Dokumen</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        <template x-for="item in filteredItems" :key="item.id">
                            <tr class="hover:bg-slate-50/70 transition">
                                <!-- Kepala Keluarga -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 font-black flex items-center justify-center text-xs shrink-0"
                                             x-text="item.kepala_keluarga.charAt(0).toUpperCase()">
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-xs" x-text="item.kepala_keluarga"></div>
                                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                                No. KK: <span x-text="item.nomor_kk"></span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono">
                                                NIK: <span x-text="item.nik_kepala_keluarga"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Alamat & Kontak -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-800 text-xs" x-text="item.nomor_rumah"></div>
                                    <div class="text-[11px] text-slate-500">
                                        RT <span x-text="item.rt"></span> / RW <span x-text="item.rw"></span>
                                    </div>
                                    <div class="text-[10px] text-blue-600 font-medium mt-0.5 flex items-center gap-1">
                                        <span>📞</span> <span x-text="item.no_hp"></span>
                                    </div>
                                </td>

                                <!-- Jumlah Anggota -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700">
                                        <span x-text="item.jumlah_anggota"></span> Jiwa
                                    </span>
                                </td>

                                <!-- Kelengkapan Foto Berkas Dokumen -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Foto KK -->
                                        <button type="button" 
                                                @click="item.foto_kk_url ? openLightbox(item.foto_kk_url, 'Foto Kartu Keluarga - ' + item.kepala_keluarga) : openDetailModal(item)"
                                                :class="item.foto_kk_url ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 cursor-pointer' : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed'"
                                                class="px-2 py-1 rounded-md text-[10px] font-bold border transition"
                                                title="Foto KK">
                                            KK <span x-text="item.foto_kk_url ? '✓' : '✗'"></span>
                                        </button>

                                        <!-- Foto KTP -->
                                        <button type="button" 
                                                @click="item.foto_ktp_url ? openLightbox(item.foto_ktp_url, 'Foto KTP - ' + item.kepala_keluarga) : openDetailModal(item)"
                                                :class="item.foto_ktp_url ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 cursor-pointer' : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed'"
                                                class="px-2 py-1 rounded-md text-[10px] font-bold border transition"
                                                title="Foto KTP">
                                            KTP <span x-text="item.foto_ktp_url ? '✓' : '✗'"></span>
                                        </button>

                                        <!-- Foto BPJS -->
                                        <button type="button" 
                                                @click="item.foto_bpjs_url ? openLightbox(item.foto_bpjs_url, 'Foto BPJS - ' + item.kepala_keluarga) : openDetailModal(item)"
                                                :class="item.foto_bpjs_url ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 cursor-pointer' : 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed'"
                                                class="px-2 py-1 rounded-md text-[10px] font-bold border transition"
                                                title="Foto BPJS">
                                            BPJS <span x-text="item.foto_bpjs_url ? '✓' : '✗'"></span>
                                        </button>
                                    </div>
                                    <div class="text-[9px] text-slate-400 mt-1">
                                        <span x-text="item.is_lengkap ? 'Berkas Lengkap' : 'Belum Lengkap'"></span>
                                    </div>
                                </td>

                                <!-- Status Verifikasi -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold"
                                          :class="{
                                              'bg-emerald-50 text-emerald-700 border border-emerald-200': item.status_verifikasi === 'terverifikasi',
                                              'bg-amber-50 text-amber-700 border border-amber-200': item.status_verifikasi === 'menunggu',
                                              'bg-rose-50 text-rose-700 border border-rose-200': item.status_verifikasi === 'ditolak'
                                          }">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="{
                                                  'bg-emerald-500': item.status_verifikasi === 'terverifikasi',
                                                  'bg-amber-500 animate-pulse': item.status_verifikasi === 'menunggu',
                                                  'bg-rose-500': item.status_verifikasi === 'ditolak'
                                              }"></span>
                                        <span x-text="item.status_label"></span>
                                    </span>
                                </td>

                                <!-- Aksi -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Lihat Foto & Detail -->
                                        <button type="button" 
                                                @click="openDetailModal(item)" 
                                                class="px-3 py-1.5 bg-[#1d4ed8] hover:bg-blue-800 text-white font-bold text-[11px] rounded-lg shadow-2xs transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Lihat Foto & Detail</span>
                                        </button>

                                        <!-- Edit -->
                                        <button type="button" 
                                                @click="openEditModal(item)"
                                                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition" 
                                                title="Edit Data">
                                            ✏️
                                        </button>

                                        <!-- Hapus -->
                                        <form :action="'{{ url('admin/kk') }}/' + item.id" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data KK ini? Seluruh berkas foto terkait akan dihapus.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition" title="Hapus Data">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <tr x-show="filteredItems.length === 0">
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <span class="text-3xl block">📁</span>
                                    <div class="font-bold text-slate-700 text-xs">Tidak ada data Kartu Keluarga ditemukan</div>
                                    <p class="text-[11px] text-slate-400">Coba ubah kata kunci pencarian atau filter status yang Anda pilih.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 1: DETAIL ISI FOTO-FOTO DATA DIRI PER KK                            -->
        <!-- ========================================================================= -->
        <div x-show="isDetailModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div @click.away="isDetailModalOpen = false" 
                 class="bg-white rounded-3xl max-w-4xl w-full p-6 sm:p-7 space-y-6 shadow-2xl border border-slate-100 my-8 max-h-[90vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-blue-100 text-blue-700 font-extrabold flex items-center justify-center text-base shrink-0">
                            📁
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-extrabold text-slate-900" x-text="selectedItem?.kepala_keluarga"></h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                                      :class="{
                                          'bg-emerald-50 text-emerald-700 border border-emerald-200': selectedItem?.status_verifikasi === 'terverifikasi',
                                          'bg-amber-50 text-amber-700 border border-amber-200': selectedItem?.status_verifikasi === 'menunggu',
                                          'bg-rose-50 text-rose-700 border border-rose-200': selectedItem?.status_verifikasi === 'ditolak'
                                      }"
                                      x-text="selectedItem?.status_label">
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                No. KK: <span class="font-mono font-bold text-slate-700" x-text="selectedItem?.nomor_kk"></span> | 
                                Rumah: <span class="font-semibold text-slate-700" x-text="selectedItem?.nomor_rumah"></span>
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="isDetailModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-sm">
                        ✕
                    </button>
                </div>

                <!-- Info Ringkasan KK -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">NIK Kepala Keluarga</div>
                        <div class="font-mono font-bold text-slate-800 mt-0.5" x-text="selectedItem?.nik_kepala_keluarga"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">RT / RW</div>
                        <div class="font-semibold text-slate-800 mt-0.5">RT <span x-text="selectedItem?.rt"></span> / RW <span x-text="selectedItem?.rw"></span></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">WhatsApp / HP</div>
                        <div class="font-semibold text-blue-600 mt-0.5" x-text="selectedItem?.no_hp"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase">Jumlah Anggota</div>
                        <div class="font-bold text-slate-800 mt-0.5"><span x-text="selectedItem?.jumlah_anggota"></span> Jiwa</div>
                    </div>
                </div>

                <!-- Galeri Foto-Foto Berkas KK -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🖼️</span> Galeri Berkas Dokumen Kependudukan
                        </h4>
                        <span class="text-[11px] text-slate-400">Klik tombol perbesar untuk melihat ukuran penuh</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- 1. Foto KK -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                    📄 Foto Kartu Keluarga (KK)
                                </span>
                                <template x-if="selectedItem?.foto_kk_url">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Tersedia</span>
                                </template>
                                <template x-if="!selectedItem?.foto_kk_url">
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">Belum Diunggah</span>
                                </template>
                            </div>

                            <template x-if="selectedItem?.foto_kk_url">
                                <div class="relative group rounded-xl overflow-hidden border border-slate-100 bg-slate-50 h-44 flex items-center justify-center">
                                    <img :src="selectedItem?.foto_kk_url" alt="Foto KK" class="h-full w-full object-cover group-hover:scale-105 transition" />
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                        <button type="button" @click="openLightbox(selectedItem.foto_kk_url, 'Foto Kartu Keluarga')" class="px-3 py-1.5 rounded-xl bg-white text-slate-800 font-bold text-xs shadow-md">
                                            🔍 Perbesar Foto
                                        </button>
                                        <a :href="selectedItem?.foto_kk_url" target="_blank" class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md">
                                            Buka File
                                        </a>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedItem?.foto_kk_url">
                                <div class="h-44 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs">
                                    <span class="text-2xl mb-1">📄</span>
                                    <span>Foto KK belum diunggah warga</span>
                                </div>
                            </template>
                        </div>

                        <!-- 2. Foto KTP -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                    🪪 Foto KTP (Kepala Keluarga)
                                </span>
                                <template x-if="selectedItem?.foto_ktp_url">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Tersedia</span>
                                </template>
                                <template x-if="!selectedItem?.foto_ktp_url">
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">Belum Diunggah</span>
                                </template>
                            </div>

                            <template x-if="selectedItem?.foto_ktp_url">
                                <div class="relative group rounded-xl overflow-hidden border border-slate-100 bg-slate-50 h-44 flex items-center justify-center">
                                    <img :src="selectedItem?.foto_ktp_url" alt="Foto KTP" class="h-full w-full object-cover group-hover:scale-105 transition" />
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                        <button type="button" @click="openLightbox(selectedItem.foto_ktp_url, 'Foto KTP')" class="px-3 py-1.5 rounded-xl bg-white text-slate-800 font-bold text-xs shadow-md">
                                            🔍 Perbesar Foto
                                        </button>
                                        <a :href="selectedItem?.foto_ktp_url" target="_blank" class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md">
                                            Buka File
                                        </a>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedItem?.foto_ktp_url">
                                <div class="h-44 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs">
                                    <span class="text-2xl mb-1">🪪</span>
                                    <span>Foto KTP belum diunggah warga</span>
                                </div>
                            </template>
                        </div>

                        <!-- 3. Foto BPJS -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                    🏥 Foto Kartu BPJS
                                </span>
                                <template x-if="selectedItem?.foto_bpjs_url">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Tersedia</span>
                                </template>
                                <template x-if="!selectedItem?.foto_bpjs_url">
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">Belum Diunggah</span>
                                </template>
                            </div>

                            <template x-if="selectedItem?.foto_bpjs_url">
                                <div class="relative group rounded-xl overflow-hidden border border-slate-100 bg-slate-50 h-44 flex items-center justify-center">
                                    <img :src="selectedItem?.foto_bpjs_url" alt="Foto BPJS" class="h-full w-full object-cover group-hover:scale-105 transition" />
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                        <button type="button" @click="openLightbox(selectedItem.foto_bpjs_url, 'Foto BPJS')" class="px-3 py-1.5 rounded-xl bg-white text-slate-800 font-bold text-xs shadow-md">
                                            🔍 Perbesar Foto
                                        </button>
                                        <a :href="selectedItem?.foto_bpjs_url" target="_blank" class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md">
                                            Buka File
                                        </a>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedItem?.foto_bpjs_url">
                                <div class="h-44 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs">
                                    <span class="text-2xl mb-1">🏥</span>
                                    <span>Foto BPJS belum diunggah warga</span>
                                </div>
                            </template>
                        </div>

                        <!-- 4. Foto Dokumen Pendukung Lainnya -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-xs text-slate-800 flex items-center gap-1.5">
                                    📑 Dokumen Pendukung Lainnya
                                </span>
                                <template x-if="selectedItem?.foto_lainnya_url">
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Tersedia</span>
                                </template>
                                <template x-if="!selectedItem?.foto_lainnya_url">
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">Tidak Ada</span>
                                </template>
                            </div>

                            <template x-if="selectedItem?.foto_lainnya_url">
                                <div class="relative group rounded-xl overflow-hidden border border-slate-100 bg-slate-50 h-44 flex items-center justify-center">
                                    <img :src="selectedItem?.foto_lainnya_url" alt="Dokumen Lainnya" class="h-full w-full object-cover group-hover:scale-105 transition" />
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                        <button type="button" @click="openLightbox(selectedItem.foto_lainnya_url, 'Dokumen Pendukung Lainnya')" class="px-3 py-1.5 rounded-xl bg-white text-slate-800 font-bold text-xs shadow-md">
                                            🔍 Perbesar Foto
                                        </button>
                                        <a :href="selectedItem?.foto_lainnya_url" target="_blank" class="px-3 py-1.5 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-md">
                                            Buka File
                                        </a>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!selectedItem?.foto_lainnya_url">
                                <div class="h-44 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs">
                                    <span class="text-2xl mb-1">📑</span>
                                    <span>Tidak ada berkas tambahan</span>
                                </div>
                            </template>
                        </div>

                    </div>
                </div>

                <!-- Form Verifikasi Dokumen oleh Pengurus RT -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                        Tindakan Verifikasi Pengurus RT
                    </h4>

                    <form :action="'{{ url('admin/kk') }}/' + selectedItem?.id + '/status'" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Ubah Status Verifikasi:</label>
                                <select name="status_verifikasi" x-model="selectedItem.status_verifikasi" class="w-full text-xs rounded-xl border-slate-200 font-semibold">
                                    <option value="terverifikasi">✅ Terverifikasi (Berkas Sah & Lengkap)</option>
                                    <option value="menunggu">⏳ Menunggu Verifikasi</option>
                                    <option value="ditolak">❌ Tolak / Perlu Perbaikan Berkas</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Pengurus RT untuk Warga:</label>
                                <input type="text" name="catatan_admin" x-model="selectedItem.catatan_admin" placeholder="Contoh: Foto KTP agak buram, mohon unggah ulang..." class="w-full text-xs rounded-xl border-slate-200" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-200">
                            <button type="button" @click="isDetailModalOpen = false" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                                Tutup
                            </button>
                            <button type="submit" class="px-5 py-2 rounded-xl bg-[#1d4ed8] hover:bg-blue-800 text-white font-bold text-xs shadow-xs transition">
                                Simpan Verifikasi
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 2: TAMBAH DATA KK BARU                                              -->
        <!-- ========================================================================= -->
        <div x-show="isAddModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div @click.away="isAddModalOpen = false" 
                 class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 space-y-5 shadow-2xl border border-slate-100 my-8 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Tambah Data KK & Berkas Warga</h3>
                        <p class="text-xs text-slate-500">Input data identitas keluarga dan arsipkan foto dokumen kependudukan.</p>
                    </div>
                    <button type="button" @click="isAddModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-sm">
                        ✕
                    </button>
                </div>

                <form action="{{ route('admin.kk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Kepala Keluarga <span class="text-rose-500">*</span></label>
                        <input type="text" name="kepala_keluarga" placeholder="Contoh: Budi Santoso" required class="w-full text-xs rounded-xl border-slate-200" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Kartu Keluarga (KK)</label>
                            <input type="text" name="nomor_kk" placeholder="16 Digit Nomor KK" class="w-full text-xs rounded-xl border-slate-200 font-mono" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">NIK Kepala Keluarga</label>
                            <input type="text" name="nik_kepala_keluarga" placeholder="16 Digit NIK" class="w-full text-xs rounded-xl border-slate-200 font-mono" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Rumah / Blok <span class="text-rose-500">*</span></label>
                            <input type="text" name="nomor_rumah" placeholder="Contoh: Blok A No. 12" required class="w-full text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">RT / RW</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <input type="text" name="rt" value="01" placeholder="RT" class="w-full text-xs rounded-xl border-slate-200 text-center" />
                                <input type="text" name="rw" value="01" placeholder="RW" class="w-full text-xs rounded-xl border-slate-200 text-center" />
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Jumlah Anggota</label>
                            <input type="number" name="jumlah_anggota" min="1" value="1" class="w-full text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                            <input type="text" name="no_hp" placeholder="Contoh: 081234567890" class="w-full text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Status Verifikasi</label>
                            <select name="status_verifikasi" class="w-full text-xs rounded-xl border-slate-200">
                                <option value="terverifikasi">✅ Terverifikasi</option>
                                <option value="menunggu">⏳ Menunggu Verifikasi</option>
                                <option value="ditolak">❌ Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <!-- Upload Foto Berkas -->
                    <div class="pt-2 border-t border-slate-100 space-y-3">
                        <span class="block font-bold text-slate-800 text-xs">Unggah Foto Berkas Dokumen (Opsional / Dapat Diunggah Nanti):</span>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">1. Foto Kartu Keluarga (KK)</label>
                                <input type="file" name="foto_kk" accept="image/*,.pdf" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">2. Foto KTP</label>
                                <input type="file" name="foto_ktp" accept="image/*,.pdf" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">3. Foto BPJS</label>
                                <input type="file" name="foto_bpjs" accept="image/*,.pdf" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">4. Dokumen Pendukung Lainnya</label>
                                <input type="file" name="foto_lainnya" accept="image/*,.pdf" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-[#1d4ed8] hover:bg-blue-800 text-white font-bold shadow-xs transition">
                            Simpan Data KK
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 3: EDIT DATA KK                                                     -->
        <!-- ========================================================================= -->
        <div x-show="isEditModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div @click.away="isEditModalOpen = false" 
                 class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 space-y-5 shadow-2xl border border-slate-100 my-8 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Perbarui Data KK</h3>
                        <p class="text-xs text-slate-500">Edit data identitas dan perbarui berkas dokumen.</p>
                    </div>
                    <button type="button" @click="isEditModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-sm">
                        ✕
                    </button>
                </div>

                <form :action="'{{ url('admin/kk') }}/' + editForm.id" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Kepala Keluarga <span class="text-rose-500">*</span></label>
                        <input type="text" name="kepala_keluarga" x-model="editForm.kepala_keluarga" required class="w-full text-xs rounded-xl border-slate-200" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Kartu Keluarga (KK)</label>
                            <input type="text" name="nomor_kk" x-model="editForm.nomor_kk" class="w-full text-xs rounded-xl border-slate-200 font-mono" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">NIK Kepala Keluarga</label>
                            <input type="text" name="nik_kepala_keluarga" x-model="editForm.nik_kepala_keluarga" class="w-full text-xs rounded-xl border-slate-200 font-mono" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Rumah / Blok <span class="text-rose-500">*</span></label>
                            <input type="text" name="nomor_rumah" x-model="editForm.nomor_rumah" required class="w-full text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">RT / RW</label>
                            <div class="grid grid-cols-2 gap-1.5">
                                <input type="text" name="rt" x-model="editForm.rt" placeholder="RT" class="w-full text-xs rounded-xl border-slate-200 text-center" />
                                <input type="text" name="rw" x-model="editForm.rw" placeholder="RW" class="w-full text-xs rounded-xl border-slate-200 text-center" />
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Jumlah Anggota</label>
                            <input type="number" name="jumlah_anggota" x-model="editForm.jumlah_anggota" min="1" class="w-full text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP</label>
                            <input type="text" name="no_hp" x-model="editForm.no_hp" class="w-full text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Status Verifikasi</label>
                            <select name="status_verifikasi" x-model="editForm.status_verifikasi" class="w-full text-xs rounded-xl border-slate-200">
                                <option value="terverifikasi">✅ Terverifikasi</option>
                                <option value="menunggu">⏳ Menunggu Verifikasi</option>
                                <option value="ditolak">❌ Ditolak</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ganti / Upload Berkas Baru -->
                    <div class="pt-2 border-t border-slate-100 space-y-3">
                        <span class="block font-bold text-slate-800 text-xs">Perbarui Foto Berkas (Pilih file baru jika ingin mengganti):</span>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">1. Foto KK</label>
                                <input type="file" name="foto_kk" accept="image/*,.pdf" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">2. Foto KTP</label>
                                <input type="file" name="foto_ktp" accept="image/*,.pdf" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">3. Foto BPJS</label>
                                <input type="file" name="foto_bpjs" accept="image/*,.pdf" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" />
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-600 mb-1">4. Dokumen Lainnya</label>
                                <input type="file" name="foto_lainnya" accept="image/*,.pdf" class="w-full text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700" />
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isEditModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-[#1d4ed8] hover:bg-blue-800 text-white font-bold shadow-xs transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 4: LIGHTBOX PERBESAR FOTO FULLSCREEN                                -->
        <!-- ========================================================================= -->
        <div x-show="lightboxUrl" 
             x-cloak
             @click.away="lightboxUrl = null"
             class="fixed inset-0 z-50 bg-black/85 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="relative max-w-4xl max-h-[90vh] bg-white rounded-3xl overflow-hidden p-2 shadow-2xl">
                <div class="absolute top-4 left-4 z-10 px-3 py-1.5 rounded-xl bg-black/70 text-white font-bold text-xs" x-text="lightboxTitle"></div>
                <button type="button" @click="lightboxUrl = null" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/70 text-white hover:bg-black flex items-center justify-center font-bold text-sm z-10 transition">
                    ✕
                </button>
                <img :src="lightboxUrl" :alt="lightboxTitle" class="max-h-[85vh] w-auto mx-auto rounded-2xl object-contain" />
            </div>
        </div>

    </div>

    <!-- Script Alpine.js Controller -->
    <script>
        function kkManagement(initialData) {
            return {
                items: initialData || [],
                searchQuery: '',
                statusFilter: 'all',
                isDetailModalOpen: false,
                isAddModalOpen: false,
                isEditModalOpen: false,
                selectedItem: null,
                editForm: {},
                lightboxUrl: null,
                lightboxTitle: '',

                get filteredItems() {
                    return this.items.filter(item => {
                        // Filter Status
                        const matchStatus = this.statusFilter === 'all' || item.status_verifikasi === this.statusFilter;

                        // Filter Search
                        const q = this.searchQuery.toLowerCase().trim();
                        const matchSearch = !q || 
                            (item.kepala_keluarga && item.kepala_keluarga.toLowerCase().includes(q)) ||
                            (item.nomor_kk && item.nomor_kk.toLowerCase().includes(q)) ||
                            (item.nik_kepala_keluarga && item.nik_kepala_keluarga.toLowerCase().includes(q)) ||
                            (item.nomor_rumah && item.nomor_rumah.toLowerCase().includes(q));

                        return matchStatus && matchSearch;
                    });
                },

                openDetailModal(item) {
                    this.selectedItem = Object.assign({}, item);
                    this.isDetailModalOpen = true;
                },

                openAddModal() {
                    this.isAddModalOpen = true;
                },

                openEditModal(item) {
                    this.editForm = Object.assign({}, item);
                    this.isEditModalOpen = true;
                },

                openLightbox(url, title) {
                    this.lightboxUrl = url;
                    this.lightboxTitle = title || 'Pratinjau Foto Dokumen';
                }
            };
        }
    </script>
</x-admin-layout>
