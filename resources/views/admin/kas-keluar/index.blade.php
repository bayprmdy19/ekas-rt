<x-admin-layout>
    <div x-data="kasKeluarManagement()" class="p-5 sm:p-7 space-y-6">

        <!-- Top Header: Title & Action Icons -->
        <div class="flex items-center justify-between pb-1">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">
                Kas Keluar
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
                <button type="button" class="text-emerald-500 hover:text-emerald-700 font-bold cursor-pointer" onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        @if (($errors ?? false) && $errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 font-semibold shadow-xs">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <span>Terdapat beberapa kesalahan pengisian formulir:</span>
                </div>
                <ul class="list-disc list-inside ml-6 space-y-0.5 text-[11px] font-normal">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card Utama: Kas Keluar Sesuai Konsep (Nuansa Biru Modern) -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 sm:p-6">
            
            <!-- Header Card: Icon Kas Keluar (Panah Atas) + Judul & Tombol Tambah (Biru) -->
            <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 border border-blue-200/80 flex items-center justify-center font-bold">
                        <!-- Icon Panah Atas (Kas Keluar) -->
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                    </div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-800">
                        Kas Keluar
                    </h2>
                </div>

                <!-- Tombol + Tambah (Warna Biru) -->
                <button type="button" 
                        @click="openAddModal()" 
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1d4ed8] hover:bg-[#1e40af] text-white font-semibold text-xs sm:text-sm rounded-xl shadow-xs transition hover:shadow-md cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah</span>
                </button>
            </div>

            <!-- Kontrol Tabel: Tampilkan [10] data & Cari: [input] -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-5 pb-4 text-xs text-slate-600">
                <!-- Dropdown Tampilkan Data -->
                <div class="flex items-center gap-2">
                    <span>Tampilkan</span>
                    <select x-model.number="perPage" 
                            class="text-xs bg-white border border-slate-300 rounded-lg px-2.5 py-1.5 font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>data</span>
                </div>

                <!-- Input Cari Data -->
                <div class="flex items-center gap-2">
                    <label for="searchKasKeluar" class="font-medium text-slate-700">Cari:</label>
                    <div class="relative">
                        <input type="text" 
                                id="searchKasKeluar"
                                x-model="searchQuery" 
                                placeholder="" 
                                class="text-xs bg-white border border-slate-300 rounded-lg px-3 py-1.5 w-48 sm:w-60 text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        <button x-show="searchQuery" 
                                @click="searchQuery = ''" 
                                class="absolute right-2 top-1.5 text-slate-400 hover:text-slate-600 text-xs">
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Kas Keluar dengan Header Warna Biru Sesuai Permintaan -->
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs">
                    <!-- Thead dengan Warna Biru -->
                    <thead>
                        <tr class="bg-[#1d4ed8] text-white select-none">
                            <th scope="col" class="py-3 px-4 font-bold cursor-pointer hover:bg-[#1e40af] transition" @click="sortBy('tanggal')">
                                <div class="flex items-center gap-1.5">
                                    <span>Tanggal</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-3 px-4 font-bold cursor-pointer hover:bg-[#1e40af] transition" @click="sortBy('kategori')">
                                <div class="flex items-center gap-1.5">
                                    <span>Kategori</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-3 px-4 font-bold cursor-pointer hover:bg-[#1e40af] transition" @click="sortBy('keterangan')">
                                <div class="flex items-center gap-1.5">
                                    <span>Keterangan</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-3 px-4 font-bold cursor-pointer hover:bg-[#1e40af] transition" @click="sortBy('nominal')">
                                <div class="flex items-center gap-1.5">
                                    <span>Nominal</span>
                                    <svg class="w-3.5 h-3.5 text-blue-200 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-3 px-4 font-bold text-center w-28">
                                <span>Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <template x-for="(item, index) in paginatedData" :key="item.id">
                            <tr class="hover:bg-blue-50/40 transition">
                                <!-- Tanggal -->
                                <td class="py-3 px-4 text-slate-600 font-medium whitespace-nowrap" x-text="item.tanggal"></td>
                                
                                <!-- Kategori -->
                                <td class="py-3 px-4 text-slate-700 font-medium">
                                    <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-semibold" x-text="item.kategori"></span>
                                </td>
                                
                                <!-- Keterangan dengan Icon Lampiran jika ada -->
                                <td class="py-3 px-4 text-slate-700">
                                    <div class="flex items-center gap-1.5">
                                        <span x-text="item.keterangan"></span>
                                        <template x-if="item.lampiran_url">
                                            <button type="button" 
                                                    @click="previewAttachment(item)" 
                                                    title="Lihat Bukti Nota / Lampiran"
                                                    class="inline-flex items-center text-blue-600 hover:text-blue-800 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                                
                                <!-- Nominal (Pengeluaran dengan tanda minus - Rp) -->
                                <td class="py-3 px-4 text-rose-600 font-bold whitespace-nowrap" x-text="item.nominal_formatted"></td>
                                
                                <!-- Tombol Aksi: Edit & Hapus -->
                                <td class="py-3 px-4 text-center">
                                    <div class="inline-flex items-center justify-center gap-1.5">
                                        <!-- Tombol Edit -->
                                        <button type="button" 
                                                @click="openEditModal(item)" 
                                                title="Edit Transaksi"
                                                class="w-7 h-7 rounded-lg border border-slate-300 text-slate-500 hover:text-blue-600 hover:border-blue-400 hover:bg-blue-50 flex items-center justify-center transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <button type="button" 
                                                @click="confirmDelete(item)" 
                                                title="Hapus Transaksi"
                                                class="w-7 h-7 rounded-lg border border-slate-300 text-slate-500 hover:text-rose-600 hover:border-rose-400 hover:bg-rose-50 flex items-center justify-center transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Baris Kosong Jika Belum Ada Data -->
                        <tr x-show="filteredData.length === 0">
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <span class="text-xs font-medium">Tidak ada data transaksi kas keluar ditemukan.</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer: Ringkasan Jumlah Data & Tombol Pagination -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-5 text-xs text-slate-600">
                <!-- Data X - Y dari Z -->
                <div>
                    <span x-text="paginationInfo"></span>
                </div>

                <!-- Navigasi Halaman -->
                <div class="inline-flex items-center gap-1">
                    <!-- Tombol Sebelumnya -->
                    <button type="button" 
                            @click="prevPage()" 
                            :disabled="currentPage === 1" 
                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed text-slate-400 bg-slate-100 border-slate-200' : 'text-slate-700 bg-white hover:bg-slate-50 border-slate-300 cursor-pointer'"
                            class="px-3 py-1.5 rounded-lg border font-medium transition">
                        Sebelumnya
                    </button>

                    <!-- Tombol Angka Halaman -->
                    <template x-for="p in totalPages" :key="p">
                        <button type="button" 
                                @click="currentPage = p" 
                                :class="currentPage === p ? 'bg-[#1d4ed8] text-white border-[#1d4ed8] font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-50 border-slate-300 font-medium'"
                                class="w-8 h-8 rounded-lg border flex items-center justify-center transition cursor-pointer" 
                                x-text="p">
                        </button>
                    </template>

                    <!-- Tombol Selanjutnya -->
                    <button type="button" 
                            @click="nextPage()" 
                            :disabled="currentPage === totalPages || totalPages === 0" 
                            :class="(currentPage === totalPages || totalPages === 0) ? 'opacity-50 cursor-not-allowed text-slate-400 bg-slate-100 border-slate-200' : 'text-slate-700 bg-white hover:bg-slate-50 border-slate-300 cursor-pointer'"
                            class="px-3 py-1.5 rounded-lg border font-medium transition">
                        Selanjutnya
                    </button>
                </div>
            </div>

        </div>

        <!-- ================= MODAL TAMBAH KAS KELUAR ================= -->
        <div x-show="showAddModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
             style="display: none;">
            
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 relative" 
                 @click.away="showAddModal = false">
                
                <!-- Header Modal -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Tambah Kas Keluar</h3>
                    </div>
                    <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <!-- Form Tambah -->
                <form action="{{ route('admin.kas-keluar.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf

                    <!-- Tanggal Transaksi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" 
                               name="tanggal" 
                               value="{{ date('Y-m-d') }}" 
                               required 
                               class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>

                    <!-- Kategori Pengeluaran -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                        <div class="space-y-2">
                            <select name="kategori" 
                                    x-model="addForm.kategori"
                                    required 
                                    class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer">
                                <option value="" disabled selected>-- Pilih Kategori Pengeluaran --</option>
                                <option value="Kebersihan">Kebersihan</option>
                                <option value="Sosial">Sosial</option>
                                <option value="Keamanan">Keamanan</option>
                                <option value="Perbaikan & Fasilitas">Perbaikan & Fasilitas</option>
                                <option value="Konsumsi & Rapat">Konsumsi & Rapat</option>
                                <option value="Operasional RT">Operasional RT</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            
                            <!-- Input khusus jika memilih Lainnya -->
                            <input x-show="addForm.kategori === 'Lainnya'" 
                                   type="text" 
                                   name="kategori_custom" 
                                   placeholder="Tuliskan nama kategori lainnya..." 
                                   @input="addForm.kategoriCustom = $event.target.value"
                                   class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Nominal (Rp) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" 
                                   name="jumlah" 
                                   placeholder="Contoh: 200000" 
                                   min="1" 
                                   step="any"
                                   required 
                                   class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-3.5 py-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Keterangan Transaksi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan <span class="text-rose-500">*</span></label>
                        <textarea name="keterangan" 
                                  rows="3" 
                                  placeholder="Contoh: Pengeluaran rutin, Tengok tetangga yang sakit, Pembelian lampu jalan..." 
                                  required 
                                  class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Upload Lampiran / Nota / Kwitansi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Bukti Nota / Kwitansi Pengeluaran 
                            <span class="text-slate-400 font-normal">(Opsional: JPG, PNG, PDF max 5MB)</span>
                        </label>
                        <input type="file" 
                               name="lampiran" 
                               accept="image/png,image/jpeg,image/webp,application/pdf"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    </div>

                    <!-- Tombol Aksi Modal -->
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" 
                                @click="showAddModal = false" 
                                class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl bg-[#1d4ed8] hover:bg-[#1e40af] text-xs font-semibold text-white shadow-xs transition cursor-pointer">
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL EDIT KAS KELUAR ================= -->
        <div x-show="showEditModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
             style="display: none;">
            
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 relative" 
                 @click.away="showEditModal = false">
                
                <!-- Header Modal Edit -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Edit Kas Keluar</h3>
                    </div>
                    <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <!-- Form Edit -->
                <form :action="'{{ url('/admin/kas-keluar') }}/' + editForm.id" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Tanggal Transaksi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" 
                               name="tanggal" 
                               x-model="editForm.tanggal" 
                               required 
                               class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>

                    <!-- Kategori -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                        <select name="kategori" 
                                x-model="editForm.kategori" 
                                required 
                                class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 cursor-pointer">
                            <option value="Kebersihan">Kebersihan</option>
                            <option value="Sosial">Sosial</option>
                            <option value="Keamanan">Keamanan</option>
                            <option value="Perbaikan & Fasilitas">Perbaikan & Fasilitas</option>
                            <option value="Konsumsi & Rapat">Konsumsi & Rapat</option>
                            <option value="Operasional RT">Operasional RT</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Nominal (Rp) -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" 
                                   name="jumlah" 
                                   x-model="editForm.nominal" 
                                   min="1" 
                                   step="any"
                                   required 
                                   class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-3.5 py-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Keterangan Transaksi -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan <span class="text-rose-500">*</span></label>
                        <textarea name="keterangan" 
                                  x-model="editForm.keterangan" 
                                  rows="3" 
                                  required 
                                  class="w-full text-xs bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                    </div>

                    <!-- Upload Lampiran Baru -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Ganti Bukti Nota / Lampiran
                            <span class="text-slate-400 font-normal">(Kosongkan jika tidak diubah)</span>
                        </label>
                        <input type="file" 
                               name="lampiran" 
                               accept="image/png,image/jpeg,image/webp,application/pdf"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    </div>

                    <!-- Tombol Aksi Modal Edit -->
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <button type="button" 
                                @click="showEditModal = false" 
                                class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl bg-[#1d4ed8] hover:bg-[#1e40af] text-xs font-semibold text-white shadow-xs transition cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL HAPUS KAS KELUAR ================= -->
        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
             style="display: none;">
            
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl border border-slate-100 text-center relative" 
                 @click.away="showDeleteModal = false">
                
                <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>

                <h3 class="text-base font-bold text-slate-800">Hapus Transaksi?</h3>
                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                    Apakah Anda yakin ingin menghapus transaksi kas keluar <span class="font-bold text-slate-700" x-text="deleteItem.keterangan"></span> sebesar <span class="font-bold text-rose-600" x-text="deleteItem.nominal_formatted"></span>?
                </p>

                <form :action="'{{ url('/admin/kas-keluar') }}/' + deleteItem.id" method="POST" class="mt-5 flex items-center justify-center gap-2.5">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            @click="showDeleteModal = false" 
                            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-xs font-semibold text-white shadow-xs transition cursor-pointer">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>

        <!-- ================= MODAL PREVIEW LAMPIRAN / NOTA ================= -->
        <div x-show="showPreviewModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition ease-in duration-150" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4" 
             style="display: none;">
            
            <div class="bg-white rounded-2xl max-w-xl w-full p-5 shadow-2xl border border-slate-100 relative" 
                 @click.away="showPreviewModal = false">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Bukti Nota / Lampiran Kas Keluar</h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="previewItem.keterangan"></p>
                    </div>
                    <button type="button" @click="showPreviewModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                </div>

                <div class="py-4 flex flex-col items-center justify-center">
                    <!-- Jika PDF -->
                    <template x-if="previewItem.is_pdf">
                        <div class="text-center py-6">
                            <svg class="w-16 h-16 text-rose-500 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs text-slate-600 mb-3 font-medium">Dokumen berformat PDF</p>
                            <a :href="previewItem.lampiran_url" 
                               target="_blank" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                <span>Buka Dokumen PDF</span>
                            </a>
                        </div>
                    </template>

                    <!-- Jika Gambar -->
                    <template x-if="!previewItem.is_pdf && previewItem.lampiran_url">
                        <div class="max-h-96 overflow-auto rounded-xl border border-slate-200">
                            <img :src="previewItem.lampiran_url" alt="Lampiran" class="max-w-full h-auto object-contain rounded-xl">
                        </div>
                    </template>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="button" 
                            @click="showPreviewModal = false" 
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Script Alpine.js Kas Keluar Management -->
    <script>
        function kasKeluarManagement() {
            return {
                rawItems: @json($kasKeluarFormatted),
                searchQuery: '',
                perPage: 10,
                currentPage: 1,
                sortColumn: 'tanggal',
                sortDirection: 'desc',

                // State Modal
                showAddModal: false,
                showEditModal: false,
                showDeleteModal: false,
                showPreviewModal: false,

                // Form Data
                addForm: {
                    kategori: '',
                    kategoriCustom: '',
                },
                editForm: {
                    id: null,
                    tanggal: '',
                    kategori: '',
                    nominal: '',
                    keterangan: '',
                },
                deleteItem: {
                    id: null,
                    keterangan: '',
                    nominal_formatted: '',
                },
                previewItem: {
                    lampiran_url: '',
                    keterangan: '',
                    is_pdf: false,
                },

                // Filtered items based on searchQuery
                get filteredData() {
                    let items = this.rawItems;

                    if (this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase();
                        items = items.filter(item => {
                            return (item.tanggal && item.tanggal.toLowerCase().includes(q)) ||
                                   (item.kategori && item.kategori.toLowerCase().includes(q)) ||
                                   (item.keterangan && item.keterangan.toLowerCase().includes(q)) ||
                                   (item.nominal_formatted && item.nominal_formatted.toLowerCase().includes(q));
                        });
                    }

                    // Sort items
                    items.sort((a, b) => {
                        let fieldA = a[this.sortColumn];
                        let fieldB = b[this.sortColumn];

                        if (this.sortColumn === 'nominal') {
                            fieldA = Number(fieldA);
                            fieldB = Number(fieldB);
                        } else {
                            fieldA = (fieldA || '').toString().toLowerCase();
                            fieldB = (fieldB || '').toString().toLowerCase();
                        }

                        if (fieldA < fieldB) return this.sortDirection === 'asc' ? -1 : 1;
                        if (fieldA > fieldB) return this.sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    });

                    return items;
                },

                // Total pages
                get totalPages() {
                    return Math.ceil(this.filteredData.length / this.perPage) || 1;
                },

                // Paginated items
                get paginatedData() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredData.slice(start, start + this.perPage);
                },

                // Pagination info text: "Data 1 - 2 dari 2"
                get paginationInfo() {
                    const total = this.filteredData.length;
                    if (total === 0) return 'Data 0 dari 0';
                    const start = (this.currentPage - 1) * this.perPage + 1;
                    const end = Math.min(start + this.perPage - 1, total);
                    return `Data ${start} - ${end} dari ${total}`;
                },

                // Sort toggle
                sortBy(column) {
                    if (this.sortColumn === column) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = column;
                        this.sortDirection = 'asc';
                    }
                },

                // Navigasi Halaman
                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                    }
                },
                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                    }
                },

                // Modal Triggers
                openAddModal() {
                    this.addForm.kategori = '';
                    this.addForm.kategoriCustom = '';
                    this.showAddModal = true;
                },
                openEditModal(item) {
                    this.editForm = {
                        id: item.id,
                        tanggal: item.tanggal,
                        kategori: item.kategori,
                        nominal: item.nominal,
                        keterangan: item.keterangan,
                    };
                    this.showEditModal = true;
                },
                confirmDelete(item) {
                    this.deleteItem = item;
                    this.showDeleteModal = true;
                },
                previewAttachment(item) {
                    this.previewItem = item;
                    this.showPreviewModal = true;
                }
            };
        }
    </script>
</x-admin-layout>
