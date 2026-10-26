<x-admin-layout>
    @php
        $formattedPengaduans = $pengaduans->map(function ($item) {
            $isMenunggu = $item->isMenungguRatingWarga();
            return [
                'id' => $item->id,
                'tanggal' => $item->created_at ? $item->created_at->format('Y-m-d') : '-',
                'tanggal_lengkap' => $item->created_at ? $item->created_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                'nama_warga' => $item->nama_pelapor ?: 'Anonim',
                'nomor_rumah' => $item->nomor_rumah ?: '-',
                'kategori' => $item->kategori ?: 'Layanan Umum',
                'judul' => $item->judul_laporan,
                'isi' => $item->isi_laporan,
                'foto_url' => $item->foto ? asset('storage/' . $item->foto) : null,
                'status' => strtolower($item->status),
                'status_label' => $item->status_label,
                'rating' => $item->rating,
                'ulasan_warga' => $item->ulasan_warga,
                'selesai_at' => $item->selesai_at ? $item->selesai_at->toIso8601String() : null,
                'selesai_at_lengkap' => $item->selesai_at ? $item->selesai_at->translatedFormat('d F Y, H:i') . ' WIB' : '-',
                'is_menunggu_rating' => $isMenunggu,
            ];
        });
    @endphp

    <div x-data="pengaduanManagement(@js($formattedPengaduans))" class="p-5 sm:p-7 space-y-6">

        <!-- Top Header: Breadcrumb / Title & Quick Actions -->
        <div class="flex items-center justify-between pb-1">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">
                    Pengaduan
                </h1>
                <p class="text-xs text-slate-500 mt-0.5 font-medium">
                    Pusat layanan aspirasi, pelaporan kendala lingkungan, dan evaluasi kepuasan warga RT/RW Digital
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

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs text-emerald-800 font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="text-emerald-500 hover:text-emerald-700 font-bold p-1" onclick="this.closest('div').remove()">&times;</button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 font-semibold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
                <button type="button" class="text-rose-500 hover:text-rose-700 font-bold p-1" onclick="this.closest('div').remove()">&times;</button>
            </div>
        @endif

        <!-- Banner Info Otomatisasi Evaluasi Pelayanan -->
        <div class="bg-gradient-to-r from-blue-50 via-sky-50 to-indigo-50 border border-blue-200/80 rounded-2xl p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-3 text-xs shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                    <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm tracking-tight flex items-center gap-2">
                        Sistem Otomatisasi Evaluasi Pelayanan (SOP 1x24 Jam)
                    </h3>
                    <p class="text-slate-600 text-[11px] mt-0.5">
                        Aduan berstatus <span class="font-semibold text-emerald-700">Selesai</span> yang belum diberi rating oleh warga dalam waktu <strong class="text-blue-700">1x24 jam sejak selesai</strong> secara otomatis ditetapkan sistem dengan <strong class="text-amber-600">Rating ⭐ 5</strong> dan catatan resmi: <em>"Otomatis selesai oleh sistem (Layanan tuntas)"</em>.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-blue-200 text-blue-700 font-bold text-[11px] shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem Aktif
                </span>
            </div>
        </div>

        <!-- Metric Badges Overview -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <!-- Total Aduan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-500 block">Total Pengaduan</span>
                    <span class="text-lg font-black text-slate-800 tracking-tight" x-text="items.length">{{ $totalAduan }}</span>
                </div>
            </div>

            <!-- Diterima / Menunggu -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-500 block">Diterima (Baru)</span>
                    <span class="text-lg font-black text-sky-600 tracking-tight" x-text="items.filter(i => ['pending', 'diterima'].includes(i.status)).length">{{ $totalDiterima }}</span>
                </div>
            </div>

            <!-- Diproses -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-500 block">Sedang Diproses</span>
                    <span class="text-lg font-black text-amber-600 tracking-tight" x-text="items.filter(i => i.status === 'diproses').length">{{ $totalDiproses }}</span>
                </div>
            </div>

            <!-- Selesai & Rata-rata Rating -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 flex items-center gap-3.5 shadow-2xs">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-semibold text-slate-500 block">Selesai (Rating)</span>
                    <div class="flex items-center gap-1.5">
                        <span class="text-lg font-black text-emerald-600 tracking-tight" x-text="items.filter(i => i.status === 'selesai').length">{{ $totalSelesai }}</span>
                        @if($rataRataRating > 0)
                            <span class="text-[11px] font-bold text-amber-500 flex items-center gap-0.5">
                                ⭐ {{ number_format($rataRataRating, 1) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Utama: Tabel Pengaduan Warga -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6 space-y-4">
            
            <!-- Card Header: Title & Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <h2 class="text-base font-extrabold text-slate-800 tracking-tight">
                        Pengaduan Warga
                    </h2>
                </div>

                <!-- Quick Filter Status & Kategori -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Filter Status -->
                    <select x-model="statusFilter" 
                            class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 shadow-2xs cursor-pointer font-medium">
                        <option value="semua">Semua Status</option>
                        <option value="diterima">Diterima</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>

                    <!-- Filter Kategori -->
                    <select x-model="categoryFilter" 
                            class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 shadow-2xs cursor-pointer font-medium">
                        <option value="semua">Semua Kategori</option>
                        <template x-for="cat in availableCategories" :key="cat">
                            <option :value="cat" x-text="cat"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Kontrol Tabel: Tampilkan [x] data & Cari: [______] -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-600 pt-1">
                <div class="flex items-center gap-2">
                    <span>Tampilkan</span>
                    <select x-model.number="perPage" 
                            class="bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 cursor-pointer font-medium shadow-2xs">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>data</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="font-medium">Cari:</span>
                    <div class="relative w-full sm:w-56">
                        <input type="text" 
                               x-model="searchQuery" 
                               placeholder="Cari nama, judul, kategori..."
                               class="bg-white border border-slate-200 rounded-lg pl-3 pr-8 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-1 focus:ring-blue-600 w-full transition shadow-2xs placeholder:text-slate-400">
                        <span x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 cursor-pointer text-xs font-bold">&times;</span>
                    </div>
                </div>
            </div>

            <!-- TABEL DATA PENGADUAN DENGAN HEADER WARNA BIRU -->
            <div class="overflow-x-auto rounded-xl border border-slate-200/90 shadow-2xs">
                <table class="w-full text-left text-xs text-slate-700">
                    <!-- Themed Blue Table Header Sesuai Instruksi User -->
                    <thead class="bg-[#1d4ed8] text-white select-none">
                        <tr>
                            <!-- Kolom Tanggal -->
                            <th scope="col" class="py-3 px-3.5 font-bold tracking-tight whitespace-nowrap">
                                <div class="flex items-center gap-1.5 cursor-pointer hover:text-blue-100 transition" @click="sortBy('tanggal')">
                                    <span>Tanggal</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>

                            <!-- Kolom Nama Warga -->
                            <th scope="col" class="py-3 px-3.5 font-bold tracking-tight whitespace-nowrap">
                                <div class="flex items-center gap-1.5 cursor-pointer hover:text-blue-100 transition" @click="sortBy('nama_warga')">
                                    <span>Nama Warga</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>

                            <!-- Kolom Kategori -->
                            <th scope="col" class="py-3 px-3.5 font-bold tracking-tight whitespace-nowrap">
                                <div class="flex items-center gap-1.5 cursor-pointer hover:text-blue-100 transition" @click="sortBy('kategori')">
                                    <span>Kategori</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>

                            <!-- Kolom Judul Pengaduan -->
                            <th scope="col" class="py-3 px-3.5 font-bold tracking-tight min-w-[200px]">
                                <div class="flex items-center gap-1.5 cursor-pointer hover:text-blue-100 transition" @click="sortBy('judul')">
                                    <span>Judul</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>

                            <!-- Kolom Status -->
                            <th scope="col" class="py-3 px-3.5 font-bold tracking-tight text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5 cursor-pointer hover:text-blue-100 transition" @click="sortBy('status')">
                                    <span>Status</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>

                            <!-- Kolom Rating Pelayanan -->
                            <th scope="col" class="py-3 px-3.5 font-bold tracking-tight text-center whitespace-nowrap min-w-[130px]">
                                <span>Rating Pelayanan</span>
                            </th>

                            <!-- Kolom Aksi -->
                            <th scope="col" class="py-3 px-3.5 font-bold tracking-tight text-center whitespace-nowrap">
                                <span>Aksi</span>
                            </th>
                        </tr>
                    </thead>

                    <!-- Body Tabel -->
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <template x-for="item in paginatedItems" :key="item.id">
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Tanggal -->
                                <td class="py-3 px-3.5 text-slate-600 whitespace-nowrap font-medium" x-text="item.tanggal"></td>

                                <!-- Nama Warga -->
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <div class="font-bold text-slate-800" x-text="item.nama_warga"></div>
                                    <div class="text-[10px] text-slate-400" x-text="'Rumah: ' + item.nomor_rumah"></div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-3 px-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/80"
                                          x-text="item.kategori">
                                    </span>
                                </td>

                                <!-- Judul Pengaduan -->
                                <td class="py-3 px-3.5">
                                    <div class="font-semibold text-slate-800 text-xs line-clamp-1" x-text="item.judul"></div>
                                    <div class="text-[11px] text-slate-400 line-clamp-1" x-text="item.isi"></div>
                                </td>

                                <!-- Status (Badge Warna: Diterima = Biru, Diproses = Kuning, Selesai = Hijau) -->
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <!-- Status Diterima / Pending (Biru) -->
                                    <template x-if="item.status === 'diterima' || item.status === 'pending'">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                            Diterima
                                        </span>
                                    </template>

                                    <!-- Status Diproses (Kuning / Amber) -->
                                    <template x-if="item.status === 'diproses'">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                            Diproses
                                        </span>
                                    </template>

                                    <!-- Status Selesai (Hijau) -->
                                    <template x-if="item.status === 'selesai'">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                                            Selesai
                                        </span>
                                    </template>
                                </td>

                                <!-- Rating Pelayanan Sesuai Ketentuan User:
                                     - Bintang ⭐ 1-5 jika sudah dinilai atau selesai
                                     - Badge abu-abu "(Menunggu Warga)" jika status selesai tapi belum ada rating dalam 1x24 jam
                                     - Strip (-) jika aduan belum berstatus selesai
                                -->
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <!-- Jika Status Selesai & Memiliki Rating (Dinilai / Otomatisasi Sistem) -->
                                    <template x-if="item.status === 'selesai' && item.rating">
                                        <div class="inline-flex flex-col items-center justify-center">
                                            <div class="flex items-center justify-center text-amber-400 text-xs leading-none">
                                                <template x-for="star in 5" :key="star">
                                                    <span x-text="star <= item.rating ? '★' : '☆'" 
                                                          :class="star <= item.rating ? 'text-amber-400' : 'text-slate-300'"
                                                          class="text-sm"></span>
                                                </template>
                                            </div>
                                            <span class="text-[10px] font-semibold text-slate-500 mt-0.5" x-text="item.rating + ' / 5'"></span>
                                        </div>
                                    </template>

                                    <!-- Jika Status Selesai Tapi Belum Ada Rating (Dalam 1x24 Jam) -->
                                    <template x-if="item.status === 'selesai' && !item.rating">
                                        <div class="inline-flex flex-col items-center gap-0.5">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                                <span>(Menunggu Warga)</span>
                                            </span>
                                            <template x-if="getRemainingTime(item.selesai_at)">
                                                <span class="text-[9px] text-slate-400 font-mono" x-text="'⏱️ Sisa ' + getRemainingTime(item.selesai_at)"></span>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Jika Belum Berstatus Selesai (Diterima / Diproses) -->
                                    <template x-if="item.status !== 'selesai'">
                                        <span class="text-slate-400 font-bold text-sm">-</span>
                                    </template>
                                </td>

                                <!-- Tombol Aksi (Lihat Detail, Proses/Selesaikan, Hapus) -->
                                <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Tombol Lihat Detail (Eye Icon) -->
                                        <button type="button" 
                                                @click="openDetailModal(item)"
                                                title="Lihat Detail Pengaduan"
                                                class="w-7 h-7 rounded-lg border border-slate-200/90 text-slate-500 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50/50 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        <!-- Tombol Proses / Selesaikan (Sync/Process Icon) -->
                                        <button type="button" 
                                                @click="openStatusModal(item)"
                                                title="Proses / Ubah Status"
                                                class="w-7 h-7 rounded-lg border border-slate-200/90 text-slate-500 hover:text-amber-600 hover:border-amber-300 hover:bg-amber-50/50 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>

                                        <!-- Tombol Hapus (Trash Icon) -->
                                        <button type="button" 
                                                @click="openDeleteModal(item)"
                                                title="Hapus Aduan"
                                                class="w-7 h-7 rounded-lg border border-slate-200/90 text-slate-500 hover:text-rose-600 hover:border-rose-300 hover:bg-rose-50/50 flex items-center justify-center transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Tampilan Jika Data Kosong -->
                        <template x-if="paginatedItems.length === 0">
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 italic bg-white">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>Tidak ada data pengaduan yang sesuai dengan kriteria pencarian.</span>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Footer Paginasi Sesuai Referensi Desain -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 pt-3">
                <div x-text="`Data ${startRecord} - ${endRecord} dari ${totalFiltered}`"></div>

                <div class="flex items-center gap-1.5 select-none">
                    <!-- Tombol Sebelumnya -->
                    <button type="button" 
                            @click="prevPage()" 
                            :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold transition">
                        Sebelumnya
                    </button>

                    <!-- Angka Halaman -->
                    <template x-for="p in totalPages" :key="p">
                        <button type="button" 
                                @click="goToPage(p)"
                                :class="currentPage === p ? 'bg-[#1d4ed8] text-white font-bold shadow-2xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 font-medium'"
                                class="w-8 h-8 rounded-lg text-xs flex items-center justify-center transition"
                                x-text="p">
                        </button>
                    </template>

                    <!-- Tombol Selanjutnya -->
                    <button type="button" 
                            @click="nextPage()" 
                            :disabled="currentPage === totalPages || totalPages === 0"
                            :class="(currentPage === totalPages || totalPages === 0) ? 'opacity-40 cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold transition">
                        Selanjutnya
                    </button>
                </div>
            </div>

        </div>

        <!-- ========================================================= -->
        <!-- MODAL 1: LIHAT DETAIL PENGADUAN LENGKAP -->
        <!-- ========================================================= -->
        <div x-show="isDetailModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            
            <div @click.away="isDetailModalOpen = false" 
                 class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 space-y-5 my-8 transform transition-all text-slate-800">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 tracking-tight" x-text="selectedItem?.judul"></h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 mt-0.5" x-text="selectedItem?.kategori"></span>
                        </div>
                    </div>
                    <button type="button" @click="isDetailModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
                </div>

                <!-- Info Grid Pelapor & Waktu -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 bg-slate-50/80 rounded-2xl p-3.5 border border-slate-200/60 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Nama Pelapor</span>
                        <span class="font-bold text-slate-800" x-text="selectedItem?.nama_warga"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Nomor Rumah</span>
                        <span class="font-bold text-slate-800" x-text="selectedItem?.nomor_rumah"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-bold tracking-wider">Tanggal Laporan</span>
                        <span class="font-semibold text-slate-700" x-text="selectedItem?.tanggal_lengkap"></span>
                    </div>
                </div>

                <!-- Isi Pengaduan Lengkap -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Isi Lengkap Pengaduan</label>
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 text-xs text-slate-700 leading-relaxed font-normal whitespace-pre-line shadow-2xs"
                         x-text="selectedItem?.isi">
                    </div>
                </div>

                <!-- Foto Bukti Pengaduan -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Foto Bukti Aduan</label>
                    <template x-if="selectedItem?.foto_url">
                        <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-50 max-h-64 flex items-center justify-center p-2">
                            <img :src="selectedItem?.foto_url" alt="Foto Bukti" class="max-h-60 rounded-xl object-contain shadow-xs">
                        </div>
                    </template>
                    <template x-if="!selectedItem?.foto_url">
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-dashed border-slate-200 text-center text-xs text-slate-400 italic">
                            Warga tidak melampirkan foto dokumentasi untuk pengaduan ini.
                        </div>
                    </template>
                </div>

                <!-- Status Pengerjaan & Waktu Selesai -->
                <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 text-xs space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-700">Status Pengerjaan:</span>
                        <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px]"
                              :class="{
                                  'bg-blue-100 text-blue-700': selectedItem?.status === 'diterima' || selectedItem?.status === 'pending',
                                  'bg-amber-100 text-amber-700': selectedItem?.status === 'diproses',
                                  'bg-emerald-100 text-emerald-700': selectedItem?.status === 'selesai'
                              }"
                              x-text="selectedItem?.status_label">
                        </span>
                    </div>

                    <template x-if="selectedItem?.selesai_at">
                        <div class="flex items-center justify-between text-slate-600 pt-1 border-t border-blue-100/60">
                            <span>Waktu Diselesaikan:</span>
                            <span class="font-bold text-emerald-700" x-text="selectedItem?.selesai_at_lengkap"></span>
                        </div>
                    </template>
                </div>

                <!-- Evaluasi Pelayanan & Rating Warga -->
                <div class="p-4 rounded-2xl border border-slate-200/90 bg-white space-y-2 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-800">Evaluasi Pelayanan Warga:</span>
                        <!-- Stars -->
                        <template x-if="selectedItem?.rating">
                            <div class="flex items-center gap-1 text-amber-400">
                                <template x-for="star in 5" :key="star">
                                    <span x-text="star <= selectedItem?.rating ? '★' : '☆'" class="text-base"></span>
                                </template>
                                <span class="text-xs font-bold text-slate-700 ml-1" x-text="'(' + selectedItem?.rating + '/5)'"></span>
                            </div>
                        </template>

                        <!-- Waiting badge -->
                        <template x-if="selectedItem?.status === 'selesai' && !selectedItem?.rating">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                (Menunggu Warga - Batas 1x24 Jam)
                            </span>
                        </template>

                        <!-- Not completed -->
                        <template x-if="selectedItem?.status !== 'selesai'">
                            <span class="text-slate-400 text-xs italic">Aduan belum selesai dikerjakan</span>
                        </template>
                    </div>

                    <!-- Ulasan Warga / Keterangan Otomatisasi -->
                    <template x-if="selectedItem?.ulasan_warga">
                        <div class="pt-2 text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200/70">
                            <span class="font-bold text-slate-700 block mb-0.5 text-[11px]">Ulasan / Catatan Penuntasan:</span>
                            <p class="italic text-slate-700" x-text="'“' + selectedItem?.ulasan_warga + '”'"></p>
                        </div>
                    </template>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-between pt-2">
                    <button type="button" 
                            @click="isDetailModalOpen = false; openStatusModal(selectedItem)"
                            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition shadow-2xs flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Ubah Status Aduan</span>
                    </button>

                    <button type="button" 
                            @click="isDetailModalOpen = false" 
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODAL 2: UBAH / SELESAIKAN STATUS ADUAN -->
        <!-- ========================================================= -->
        <div x-show="isStatusModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            
            <div @click.away="isStatusModalOpen = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 space-y-5 my-8 transform transition-all text-slate-800">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 tracking-tight">Proses / Selesaikan</h3>
                            <p class="text-[11px] text-slate-500">Perbarui tahapan penanganan laporan warga</p>
                        </div>
                    </div>
                    <button type="button" @click="isStatusModalOpen = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
                </div>

                <!-- Form Ubah Status -->
                <form :action="`/admin/pengaduan/${selectedItem?.id}/status`" method="POST" class="space-y-4" @submit.prevent="submitStatusForm">
                    @csrf
                    @method('PATCH')

                    <!-- Ringkasan Aduan -->
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200/80 text-xs">
                        <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Judul Pengaduan:</span>
                        <div class="font-bold text-slate-800 line-clamp-2" x-text="selectedItem?.judul"></div>
                        <div class="text-[11px] text-slate-500 mt-1" x-text="'Pelapor: ' + selectedItem?.nama_warga + ' (' + selectedItem?.nomor_rumah + ')'"></div>
                    </div>

                    <!-- Pilihan Status -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-700 block">Pilih Status Baru:</label>
                        <div class="space-y-2">
                            <!-- Opsi: Diterima -->
                            <label class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition text-xs"
                                   :class="newStatus === 'diterima' ? 'border-blue-600 bg-blue-50/40 text-blue-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                                <input type="radio" name="status" value="diterima" x-model="newStatus" class="text-blue-600 focus:ring-blue-500">
                                <div class="flex-1">
                                    <span class="block">Diterima</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Aduan telah masuk antrean dan diverifikasi pengurus</span>
                                </div>
                            </label>

                            <!-- Opsi: Diproses -->
                            <label class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition text-xs"
                                   :class="newStatus === 'diproses' ? 'border-amber-500 bg-amber-50/40 text-amber-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                                <input type="radio" name="status" value="diproses" x-model="newStatus" class="text-amber-600 focus:ring-amber-500">
                                <div class="flex-1">
                                    <span class="block">Diproses</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Pengurus sedang menindaklanjuti perbaikan / solusi</span>
                                </div>
                            </label>

                            <!-- Opsi: Selesai -->
                            <label class="flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition text-xs"
                                   :class="newStatus === 'selesai' ? 'border-emerald-600 bg-emerald-50/40 text-emerald-900 font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                                <input type="radio" name="status" value="selesai" x-model="newStatus" class="text-emerald-600 focus:ring-emerald-500">
                                <div class="flex-1">
                                    <span class="block">Selesai (Tuntas)</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Penanganan rampung, waktu pengerjaan otomatis dicatat saat ini</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Notifikasi Penting Jika Memilih Selesai -->
                    <template x-if="newStatus === 'selesai'">
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-[11px] text-emerald-800 leading-relaxed space-y-1">
                            <span class="font-bold flex items-center gap-1.5 text-emerald-900">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Informasi Otomatisasi Selesai:
                            </span>
                            <p>
                                Kolom <strong>selesai_at</strong> otomatis diset menjadi <code>{{ now()->translatedFormat('d F Y, H:i') }} WIB</code>.
                                Warga diberi waktu 1x24 jam untuk mengisi ulasan. Jika tidak diisi, sistem otomatis memberikan <strong>⭐ 5</strong>.
                            </p>
                        </div>
                    </template>

                    <!-- Tombol Aksi Form -->
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" 
                                :disabled="isSavingStatus"
                                @click="isStatusModalOpen = false" 
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 disabled:opacity-50 text-slate-700 font-bold text-xs transition">
                            Batal
                        </button>
                        <button type="submit" 
                                :disabled="isSavingStatus"
                                class="px-4 py-2 rounded-xl bg-[#1d4ed8] hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold text-xs transition shadow-2xs flex items-center gap-1.5">
                            <span x-show="!isSavingStatus">Simpan Perubahan</span>
                            <span x-show="isSavingStatus" style="display: none;">Menyimpan...</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================= -->
        <!-- MODAL 3: KONFIRMASI HAPUS PENGADUAN -->
        <!-- ========================================================= -->
        <div x-show="isDeleteModalOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
             style="display: none;">
            
            <div @click.away="isDeleteModalOpen = false" 
                 class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 space-y-4 my-8 transform transition-all text-slate-800 text-center">
                
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto shadow-2xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h3 class="text-base font-extrabold text-slate-800">Hapus Pengaduan?</h3>
                    <p class="text-xs text-slate-500">
                        Apakah Anda yakin ingin menghapus data pengaduan <span class="font-bold text-slate-700" x-text="selectedItem?.judul"></span>? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>

                <form :action="`/admin/pengaduan/${selectedItem?.id}`" method="POST" class="pt-2">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" 
                                @click="isDeleteModalOpen = false" 
                                class="w-full px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="w-full px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition shadow-2xs">
                            Ya, Hapus
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <!-- Alpine.js Pengaduan Management Logic -->
    <script>
        function pengaduanManagement(initialItems) {
            return {
                items: initialItems,
                searchQuery: '',
                statusFilter: 'semua',
                categoryFilter: 'semua',
                perPage: 10,
                currentPage: 1,
                sortColumn: 'tanggal',
                sortDirection: 'desc',

                // State Modals
                isDetailModalOpen: false,
                isStatusModalOpen: false,
                isDeleteModalOpen: false,
                isSavingStatus: false,
                toastMessage: null,
                selectedItem: null,
                newStatus: 'diproses',
                nowTime: Date.now(),

                init() {
                    // Cek perubahan query/filter untuk mereset halaman ke 1
                    this.$watch('searchQuery', () => { this.currentPage = 1; });
                    this.$watch('statusFilter', () => { this.currentPage = 1; });
                    this.$watch('categoryFilter', () => { this.currentPage = 1; });
                    this.$watch('perPage', () => { this.currentPage = 1; });

                    // Evaluasi rating realtime setiap 10 detik (1x24 jam auto rating 5)
                    this.updateRealtimeRatings();
                    setInterval(() => {
                        this.nowTime = Date.now();
                        this.updateRealtimeRatings();
                    }, 5000);

                    // Polling real-time sinkronisasi aduan baru setiap 4 detik
                    setInterval(() => {
                        if (this.isDetailModalOpen || this.isStatusModalOpen || this.isDeleteModalOpen) return;

                        fetch('{{ route('admin.pengaduan.index') }}', {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success && res.pengaduans) {
                                if (res.pengaduans.length > this.items.length) {
                                    this.toastMessage = '📣 Ada laporan pengaduan baru masuk dari warga!';
                                    setTimeout(() => { this.toastMessage = null; }, 5000);
                                }
                                this.items = res.pengaduans;
                            }
                        })
                        .catch(() => {});
                    }, 4000);
                },

                getRemainingTime(selesaiAt) {
                    if (!selesaiAt) return null;
                    const selesaiTime = new Date(selesaiAt).getTime();
                    const expiry = selesaiTime + (24 * 60 * 60 * 1000);
                    const diff = expiry - this.nowTime;

                    if (diff <= 0) return null;

                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    if (hours > 0) {
                        return `${hours}j ${minutes}m`;
                    }
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                    return `${minutes}m ${seconds}d`;
                },

                updateRealtimeRatings() {
                    const now = this.nowTime;
                    this.items.forEach(item => {
                        if (item.status === 'selesai' && !item.rating && item.selesai_at) {
                            const selesaiTime = new Date(item.selesai_at).getTime();
                            if ((now - selesaiTime) >= 24 * 60 * 60 * 1000) {
                                // 1x24 jam telah lewat! Otomatis jadikan rating 5 secara realtime
                                item.rating = 5;
                                item.ulasan_warga = 'Otomatis selesai oleh sistem (Layanan tuntas)';
                                item.is_menunggu_rating = false;
                            }
                        }
                    });

                    if (this.selectedItem && this.selectedItem.status === 'selesai' && !this.selectedItem.rating && this.selectedItem.selesai_at) {
                        const selesaiTime = new Date(this.selectedItem.selesai_at).getTime();
                        if ((now - selesaiTime) >= 24 * 60 * 60 * 1000) {
                            this.selectedItem.rating = 5;
                            this.selectedItem.ulasan_warga = 'Otomatis selesai oleh sistem (Layanan tuntas)';
                            this.selectedItem.is_menunggu_rating = false;
                        }
                    }
                },

                get availableCategories() {
                    const set = new Set();
                    this.items.forEach(i => {
                        if (i.kategori) set.add(i.kategori);
                    });
                    return Array.from(set);
                },

                get filteredItems() {
                    let result = this.items;

                    // Filter Status
                    if (this.statusFilter !== 'semua') {
                        result = result.filter(item => {
                            if (this.statusFilter === 'diterima') {
                                return item.status === 'diterima' || item.status === 'pending';
                            }
                            return item.status === this.statusFilter;
                        });
                    }

                    // Filter Kategori
                    if (this.categoryFilter !== 'semua') {
                        result = result.filter(item => item.kategori === this.categoryFilter);
                    }

                    // Filter Search Query
                    if (this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase();
                        result = result.filter(item => {
                            return (item.nama_warga && item.nama_warga.toLowerCase().includes(q)) ||
                                   (item.judul && item.judul.toLowerCase().includes(q)) ||
                                   (item.kategori && item.kategori.toLowerCase().includes(q)) ||
                                   (item.isi && item.isi.toLowerCase().includes(q)) ||
                                   (item.nomor_rumah && item.nomor_rumah.toLowerCase().includes(q));
                        });
                    }

                    // Sorting
                    return result.sort((a, b) => {
                        let modifier = this.sortDirection === 'asc' ? 1 : -1;
                        let valA = a[this.sortColumn] ?? '';
                        let valB = b[this.sortColumn] ?? '';

                        if (typeof valA === 'string') valA = valA.toLowerCase();
                        if (typeof valB === 'string') valB = valB.toLowerCase();

                        if (valA < valB) return -1 * modifier;
                        if (valA > valB) return 1 * modifier;
                        return 0;
                    });
                },

                get paginatedItems() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredItems.slice(start, start + this.perPage);
                },

                get totalFiltered() {
                    return this.filteredItems.length;
                },

                get totalPages() {
                    return Math.ceil(this.totalFiltered / this.perPage) || 1;
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
                },

                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },

                goToPage(p) {
                    this.currentPage = p;
                },

                openDetailModal(item) {
                    this.selectedItem = item;
                    this.isDetailModalOpen = true;
                },

                openStatusModal(item) {
                    this.selectedItem = item;
                    this.newStatus = item.status === 'pending' ? 'diterima' : item.status;
                    this.isStatusModalOpen = true;
                },

                openDeleteModal(item) {
                    this.selectedItem = item;
                    this.isDeleteModalOpen = true;
                },

                async submitStatusForm() {
                    if (!this.selectedItem || this.isSavingStatus) return;
                    this.isSavingStatus = true;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                                      || document.querySelector('input[name="_token"]')?.value;

                        const response = await fetch(`/admin/pengaduan/${this.selectedItem.id}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                _method: 'PATCH',
                                status: this.newStatus
                            })
                        });

                        if (response.status === 419) {
                            alert('⚠️ Sesi login telah berakhir atau Anda baru saja login/mendaftar dengan akun lain (misal: akun Warga) di tab browser lain.\n\nHalaman akan dimuat ulang agar Anda dapat masuk kembali sebagai Pengurus RT.');
                            window.location.reload();
                            return;
                        }

                        if (response.status === 403) {
                            const errData = await response.json().catch(() => ({}));
                            alert(errData.message || '⚠️ Akses ditolak: Anda saat ini login sebagai akun Warga. Silakan login kembali dengan akun Pengurus RT (Admin).');
                            window.location.href = '/login';
                            return;
                        }

                        if (!response.ok) {
                            const errData = await response.json().catch(() => ({}));
                            alert(errData.message || 'Gagal menyimpan perubahan status.');
                            return;
                        }

                        const result = await response.json();
                        
                        // Perbarui data selectedItem dan item di tabel secara reaktif
                        this.selectedItem.status = this.newStatus;
                        this.selectedItem.status_label = this.newStatus === 'selesai' ? 'Selesai' : (this.newStatus === 'diproses' ? 'Diproses' : 'Diterima');
                        if (this.newStatus === 'selesai') {
                            this.selectedItem.selesai_at = result.item?.selesai_at || new Date().toISOString();
                            this.selectedItem.selesai_at_lengkap = result.item?.selesai_at_lengkap || 'Baru saja';
                            this.selectedItem.is_menunggu_rating = true;
                        } else {
                            this.selectedItem.selesai_at = null;
                            this.selectedItem.selesai_at_lengkap = '-';
                            this.selectedItem.rating = null;
                            this.selectedItem.ulasan_warga = null;
                            this.selectedItem.is_menunggu_rating = false;
                        }

                        // Cari dan sinkronkan data di array items
                        const idx = this.items.findIndex(i => i.id === this.selectedItem.id);
                        if (idx !== -1) {
                            this.items[idx] = { ...this.selectedItem };
                        }

                        this.isStatusModalOpen = false;
                        this.toastMessage = result.message || 'Status pengaduan berhasil diperbarui!';
                        setTimeout(() => { this.toastMessage = null; }, 5000);
                    } catch (e) {
                        console.error('Submit error:', e);
                        alert('Terjadi kendala jaringan saat menghubungi server. Halaman akan dimuat ulang.');
                        window.location.reload();
                    } finally {
                        this.isSavingStatus = false;
                    }
                }
            };
        }
    </script>
</x-admin-layout>
