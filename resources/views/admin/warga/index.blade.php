<x-admin-layout>
    <div x-data="wargaPage()" class="p-5 sm:p-7 space-y-6">

        <!-- 1. Header Halaman -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                    Data Warga
                </h1>
                <p class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">
                    KELOLA DATA KEPENDUDUKAN DAN REPOSITORI BERKAS KELUARGA
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
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl px-5 py-2.5 shadow-sm transition flex items-center gap-2 text-xs sm:text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Warga</span>
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
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

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-800 space-y-1">
                <div class="font-bold">Terdapat kesalahan pengisian data:</div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Ringkasan Statistik Singkat -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Warga</div>
                <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $totalWarga }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Penduduk terdata RT 01</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kepala Keluarga</div>
                <div class="text-xl sm:text-2xl font-black text-blue-600 mt-1">{{ $totalKepalaKeluarga }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Penanggung jawab KK</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Warga Tetap</div>
                <div class="text-xl sm:text-2xl font-black text-emerald-600 mt-1">{{ $totalTetap }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Status domisili tetap</div>
            </div>
            <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kontrak / Kos</div>
                <div class="text-xl sm:text-2xl font-black text-amber-600 mt-1">{{ $totalKontrak }}</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Warga sewa & domisili</div>
            </div>
        </div>

        <!-- 2. Filter & Pencarian (Presisi 100% seperti referensi IPM) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-6">
            <form action="{{ route('admin.warga.index') }}" method="GET">
                <div class="flex flex-wrap xl:flex-nowrap items-end gap-4 w-full">
                    
                    <!-- Search Input (Lebar paling luas flex-1 min-w-[280px]) -->
                    <div class="flex-1 min-w-[280px]">
                        <label for="q" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                            SEARCH
                        </label>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   id="q" 
                                   name="q" 
                                   value="{{ $search }}"
                                   placeholder="Cari Nama Kepala Keluarga, Nomor KK (16 digit), atau No. WhatsApp..."
                                   class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-white text-slate-800 placeholder:text-slate-400 font-medium transition" />
                        </div>
                    </div>

                    <!-- Blok / Gang -->
                    <div class="w-full sm:w-44 md:w-48 shrink-0">
                        <label for="blok" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                            BLOK / GANG
                        </label>
                        <select id="blok" 
                                name="blok" 
                                class="w-full py-2.5 px-4 text-sm rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-white text-slate-800 font-medium cursor-pointer transition">
                            <option value="">Semua Blok</option>
                            @foreach ($blokList as $b)
                                <option value="{{ $b }}" {{ $blok === $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                            <option value="Belum Diisi" {{ $blok === 'Belum Diisi' ? 'selected' : '' }}>Belum Diisi</option>
                        </select>
                    </div>

                    <!-- Status Keluarga -->
                    <div class="w-full sm:w-44 md:w-48 shrink-0">
                        <label for="status_keluarga" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                            STATUS KELUARGA
                        </label>
                        <select id="status_keluarga" 
                                name="status_keluarga" 
                                class="w-full py-2.5 px-4 text-sm rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-white text-slate-800 font-medium cursor-pointer transition">
                            <option value="">Semua Status</option>
                            @foreach ($statusKeluargaList as $sk)
                                <option value="{{ $sk }}" {{ $statusKeluarga === $sk ? 'selected' : '' }}>{{ $sk }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Domisili -->
                    <div class="w-full sm:w-44 md:w-48 shrink-0">
                        <label for="status_domisili" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
                            STATUS DOMISILI
                        </label>
                        <select id="status_domisili" 
                                name="status_domisili" 
                                class="w-full py-2.5 px-4 text-sm rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-white text-slate-800 font-medium cursor-pointer transition">
                            <option value="">Semua Domisili</option>
                            @foreach ($statusDomisiliList as $sd)
                                <option value="{{ $sd }}" {{ $statusDomisili === $sd ? 'selected' : '' }}>{{ $sd }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Aksi Sejajar di Paling Kanan -->
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl px-6 py-2.5 text-sm shadow-sm transition inline-flex items-center justify-center">
                            Filter
                        </button>
                        <a href="{{ route('admin.warga.index') }}" 
                           class="border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium rounded-xl px-5 py-2.5 text-sm transition inline-flex items-center justify-center text-center">
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>

        <!-- 3. Tabel Data Warga (Card Putih, Border halus, Header tabel teks kapital abu-abu) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/70 border-b border-slate-100">
                            <th class="py-3.5 px-4 text-center w-12">NO</th>
                            <th class="py-3.5 px-4">NAMA KEPALA KELUARGA / WARGA</th>
                            <th class="py-3.5 px-4">STATUS KELUARGA</th>
                            <th class="py-3.5 px-4">NIK</th>
                            <th class="py-3.5 px-4">NOMOR KK</th>
                            <th class="py-3.5 px-4">BLOK / NO. RUMAH</th>
                            <th class="py-3.5 px-4">NOMOR WHATSAPP</th>
                            <th class="py-3.5 px-4 text-center">STATUS DOMISILI</th>
                            <th class="py-3.5 px-4 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($wargas as $index => $warga)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- NO -->
                                <td class="py-3.5 px-4 text-center text-slate-400 font-bold whitespace-nowrap">
                                    {{ $wargas->firstItem() + $index }}
                                </td>

                                <!-- NAMA -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 text-xs">
                                        {{ $warga->nama_lengkap }}
                                    </div>
                                </td>

                                <!-- STATUS KELUARGA -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="bg-blue-50 text-blue-600 rounded-full px-3 py-1 font-semibold text-xs inline-block">
                                        {{ $warga->status_keluarga ?: 'Kepala Keluarga' }}
                                    </span>
                                </td>

                                <!-- NIK -->
                                <td class="py-3.5 px-4 whitespace-nowrap font-mono text-slate-600">
                                    {{ $warga->nik ?: '-' }}
                                </td>

                                <!-- NO. KK -->
                                <td class="py-3.5 px-4 whitespace-nowrap font-mono text-slate-600">
                                    {{ $warga->no_kk ?: ($warga->berkas_kk?->nomor_kk ?: '-') }}
                                </td>

                                <!-- BLOK / NO. RUMAH -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if(!empty($warga->nomor_rumah) && $warga->nomor_rumah !== '-')
                                        <div class="font-medium text-slate-800">
                                            {{ $warga->nomor_rumah }}
                                        </div>
                                        @if($warga->blok)
                                            <div class="text-[10px] text-slate-400 font-normal">
                                                {{ $warga->blok }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum dilengkapi</span>
                                    @endif
                                </td>

                                <!-- NOMOR WHATSAPP -->
                                <td class="py-3.5 px-4 whitespace-nowrap text-slate-600 font-medium">
                                    @php $noWa = $warga->no_hp ?: $warga->berkas_kk?->no_hp; @endphp
                                    @if($noWa)
                                        <span class="inline-flex items-center gap-1 text-slate-700 font-mono">
                                            {{ $noWa }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <!-- STATUS DOMISILI -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if(strtolower($warga->status_tinggal) === 'tetap')
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full px-3 py-1 text-xs font-semibold inline-block">
                                            Tetap
                                        </span>
                                    @else
                                        <span class="bg-amber-50 text-amber-700 border border-amber-100 rounded-full px-3 py-1 text-xs font-semibold inline-block">
                                            Kontrak/Kos
                                        </span>
                                    @endif
                                </td>

                                <!-- AKSI -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Tombol Edit -->
                                        <button type="button" 
                                                @click="openEditModal({{ json_encode($warga) }})"
                                                class="border border-amber-200 text-amber-600 bg-amber-50/50 hover:bg-amber-100 px-3 py-1 text-xs rounded-full font-medium transition cursor-pointer">
                                            Edit
                                        </button>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('admin.warga.destroy', $warga->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data warga {{ $warga->nama_lengkap }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="border border-red-200 text-red-600 bg-red-50/50 hover:bg-red-100 px-3 py-1 text-xs rounded-full font-medium transition cursor-pointer">
                                                Hapus
                                            </button>
                                        </form>

                                        <!-- Tombol Berkas -->
                                        <button type="button" 
                                                @click="openBerkasModal({{ $warga->id }})"
                                                class="border border-blue-200 text-blue-600 bg-blue-50/50 hover:bg-blue-100 px-3 py-1 text-xs rounded-full font-medium transition cursor-pointer">
                                            Berkas
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <div class="max-w-xs mx-auto space-y-2">
                                        <span class="text-3xl block">👥</span>
                                        <div class="font-bold text-slate-700 text-xs">Belum ada data warga ditemukan</div>
                                        <p class="text-[11px] text-slate-400">Silakan sesuaikan kata kunci filter pencarian Anda atau tambahkan data warga baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if ($wargas->hasPages())
                <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $wargas->links() }}
                </div>
            @endif
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 1: TAMBAH WARGA                                                     -->
        <!-- ========================================================================= -->
        <div x-show="isAddModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div @click.away="isAddModalOpen = false" 
                 class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 space-y-5 shadow-2xl border border-slate-100 my-8 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Tambah Data Warga</h3>
                        <p class="text-xs text-slate-500">Lengkapi informasi di bawah untuk mendaftarkan akun & data warga baru.</p>
                    </div>
                    <button type="button" @click="isAddModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-sm">
                        ✕
                    </button>
                </div>

                <form action="{{ route('admin.warga.store') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <!-- Nama Kepala Keluarga -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama Kepala Keluarga</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required
                               placeholder="Contoh: Budi Santoso"
                               class="w-full text-xs border border-slate-200 rounded-xl p-3.5 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                    </div>

                    <!-- Nomor KK -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nomor KK</label>
                        <input type="text" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16"
                               title="Nomor KK harus berjumlah tepat 16 digit angka"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                               name="no_kk" value="{{ old('no_kk') }}" required autocomplete="off"
                               placeholder="Contoh: 12910239123"
                               class="w-full text-xs border border-slate-200 rounded-xl p-3.5 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                    </div>

                    <!-- Nomor WhatsApp -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nomor WhatsApp</label>
                        <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="13"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                               name="no_hp" value="{{ old('no_hp') }}" required autocomplete="tel"
                               placeholder="Contoh: 081234567890"
                               class="w-full text-xs border border-slate-200 rounded-xl p-3.5 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Password</label>
                        <input type="password" name="password" required autocomplete="new-password"
                               placeholder="Minimal 8 karakter kombinasi huruf & angka"
                               class="w-full text-xs border border-slate-200 rounded-xl p-3.5 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                        <p class="text-[11px] text-slate-500 mt-1">Wajib minimal 8 karakter dan berisi gabungan huruf serta angka.</p>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" required autocomplete="new-password"
                               placeholder="Ketik ulang password"
                               class="w-full text-xs border border-slate-200 rounded-xl p-3.5 bg-slate-50 placeholder:text-slate-400/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isAddModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-xs transition">
                            Simpan Warga
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 2: EDIT WARGA                                                       -->
        <!-- ========================================================================= -->
        <div x-show="isEditModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div @click.away="isEditModalOpen = false" 
                 class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 space-y-5 shadow-2xl border border-slate-100 my-8 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Perbarui Data Warga</h3>
                        <p class="text-xs text-slate-500">Edit data identitas kependudukan warga.</p>
                    </div>
                    <button type="button" @click="isEditModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-sm">
                        ✕
                    </button>
                </div>

                <form :action="'{{ url('admin/warga') }}/' + editForm.id" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Kepala Keluarga / Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama_lengkap" x-model="editForm.nama_lengkap" required 
                               class="w-full text-xs rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">NIK (16 Digit)</label>
                            <input type="text" name="nik" x-model="editForm.nik" maxlength="16" inputmode="numeric"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                   class="w-full text-xs rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50 font-mono" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor KK (16 Digit)</label>
                            <input type="text" name="no_kk" x-model="editForm.no_kk" minlength="16" maxlength="16" pattern="[0-9]{16}" inputmode="numeric"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16)"
                                   class="w-full text-xs rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50 font-mono" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Status Keluarga <span class="text-rose-500">*</span></label>
                            <select name="status_keluarga" x-model="editForm.status_keluarga" required class="w-full text-xs rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50 font-medium">
                                <option value="Kepala Keluarga">Kepala Keluarga</option>
                                <option value="Istri">Istri</option>
                                <option value="Anak">Anak</option>
                                <option value="Famili Lain">Famili Lain</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Status Domisili <span class="text-rose-500">*</span></label>
                            <select name="status_tinggal" x-model="editForm.status_tinggal" required class="w-full text-xs rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50 font-medium">
                                <option value="tetap">Tetap</option>
                                <option value="kontrak">Kontrak/Kos</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">Nomor Rumah / Alamat <span class="text-rose-500">*</span></label>
                            <input type="text" name="nomor_rumah" x-model="editForm.nomor_rumah" required 
                                   class="w-full text-xs rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Blok / Wilayah</label>
                            <select name="blok" x-model="editForm.blok" class="w-full text-xs rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50 font-medium">
                                <option value="">Otomatis</option>
                                @foreach ($blokList as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp (Maks 13 Digit)</label>
                        <input type="text" name="no_hp" x-model="editForm.no_hp" maxlength="13" pattern="[0-9]*" inputmode="numeric"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                               class="w-full text-xs rounded-xl border border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50" />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isEditModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-xs transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 3: REPOSITORI BERKAS KELUARGA (BERDASARKAN NO. KK)                  -->
        <!-- ========================================================================= -->
        <div x-show="isBerkasModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto">
            <div @click.away="isBerkasModalOpen = false" 
                 class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-7 space-y-6 shadow-2xl border border-slate-100 my-8 max-h-[90vh] overflow-y-auto">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg shrink-0">
                            📁
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-slate-900">
                                Berkas Keluarga - No. KK: <span class="text-blue-600 font-mono" x-text="berkasData?.warga?.no_kk || berkasData?.kk?.nomor_kk || '-'"></span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Kepala Keluarga: <span class="font-bold text-slate-800" x-text="berkasData?.kk?.kepala_keluarga || berkasData?.warga?.nama"></span>
                                <span class="mx-1 text-slate-300">•</span>
                                Warga Terkait: <span class="font-semibold text-slate-700" x-text="berkasData?.warga?.nama"></span> (<span x-text="berkasData?.warga?.status_keluarga"></span>)
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="isBerkasModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center font-bold text-sm transition cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- Info Singkat Warga & Data KK -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-slate-50/80 rounded-2xl border border-slate-100 text-xs">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIK Warga</div>
                        <div class="font-mono font-bold text-slate-800 mt-0.5 truncate" x-text="berkasData?.warga?.nik || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">No. KK</div>
                        <div class="font-mono font-bold text-slate-800 mt-0.5 truncate" x-text="berkasData?.warga?.no_kk || berkasData?.kk?.nomor_kk || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Keluarga</div>
                        <div class="font-bold text-blue-600 mt-0.5" x-text="berkasData?.warga?.status_keluarga || '-'"></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat Rumah</div>
                        <div class="font-semibold text-slate-800 mt-0.5 truncate" x-text="berkasData?.warga?.nomor_rumah || '-'"></div>
                    </div>
                </div>

                <!-- Daftar Dokumen Keluarga Terhubung -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span>📄</span> Repositori Dokumen Keluarga
                        </span>
                        <template x-if="berkasData?.kk">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide"
                                  :class="{
                                      'bg-emerald-50 text-emerald-700 border border-emerald-200': berkasData.kk.status_verifikasi === 'terverifikasi',
                                      'bg-amber-50 text-amber-700 border border-amber-200': berkasData.kk.status_verifikasi === 'menunggu',
                                      'bg-rose-50 text-rose-700 border border-rose-200': berkasData.kk.status_verifikasi === 'ditolak'
                                  }"
                                  x-text="berkasData.kk.status_label">
                            </span>
                        </template>
                    </div>

                    <!-- Grid List 4 Dokumen -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        
                        <!-- 1. Scan Kartu Keluarga -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-2xs flex flex-col justify-between gap-3">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">📋</span>
                                        <span class="font-bold text-xs text-slate-800">Scan Kartu Keluarga</span>
                                    </div>
                                    <template x-if="berkasData?.kk?.foto_kk_url">
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full text-[10px] font-bold">Tersedia</span>
                                    </template>
                                    <template x-if="!berkasData?.kk?.foto_kk_url">
                                        <span class="bg-slate-100 text-slate-500 border border-slate-200 px-2 py-0.5 rounded-full text-[10px] font-medium">Belum Diunggah</span>
                                    </template>
                                </div>
                                <template x-if="berkasData?.kk?.foto_kk_url">
                                    <div class="relative group rounded-xl overflow-hidden border border-slate-100 h-28 bg-slate-50 flex items-center justify-center">
                                        <img :src="berkasData.kk.foto_kk_url" alt="Scan Kartu Keluarga" class="h-full w-full object-cover group-hover:scale-105 transition" />
                                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                            <button type="button" @click="openLightbox(berkasData.kk.foto_kk_url, 'Scan Kartu Keluarga')" class="px-2.5 py-1 bg-white text-slate-800 text-[10px] font-bold rounded-lg shadow-sm cursor-pointer">
                                                🔍 Pratinjau
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!berkasData?.kk?.foto_kk_url">
                                    <div class="h-28 rounded-xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs bg-slate-50/50">
                                        <span class="text-xl mb-1">📄</span>
                                        <span>Belum ada berkas KK</span>
                                    </div>
                                </template>
                            </div>
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2 text-[11px]">
                                <template x-if="berkasData?.kk?.foto_kk_url">
                                    <a :href="berkasData.kk.foto_kk_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-1 cursor-pointer">
                                        <span>Buka Dokumen</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- 2. KTP Kepala Keluarga -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-2xs flex flex-col justify-between gap-3">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">🪪</span>
                                        <span class="font-bold text-xs text-slate-800">KTP Kepala Keluarga</span>
                                    </div>
                                    <template x-if="berkasData?.kk?.foto_ktp_url">
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full text-[10px] font-bold">Tersedia</span>
                                    </template>
                                    <template x-if="!berkasData?.kk?.foto_ktp_url">
                                        <span class="bg-slate-100 text-slate-500 border border-slate-200 px-2 py-0.5 rounded-full text-[10px] font-medium">Belum Diunggah</span>
                                    </template>
                                </div>
                                <template x-if="berkasData?.kk?.foto_ktp_url">
                                    <div class="relative group rounded-xl overflow-hidden border border-slate-100 h-28 bg-slate-50 flex items-center justify-center">
                                        <img :src="berkasData.kk.foto_ktp_url" alt="KTP Kepala Keluarga" class="h-full w-full object-cover group-hover:scale-105 transition" />
                                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                            <button type="button" @click="openLightbox(berkasData.kk.foto_ktp_url, 'KTP Kepala Keluarga')" class="px-2.5 py-1 bg-white text-slate-800 text-[10px] font-bold rounded-lg shadow-sm cursor-pointer">
                                                🔍 Pratinjau
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!berkasData?.kk?.foto_ktp_url">
                                    <div class="h-28 rounded-xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs bg-slate-50/50">
                                        <span class="text-xl mb-1">🪪</span>
                                        <span>Belum ada KTP terlampir</span>
                                    </div>
                                </template>
                            </div>
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2 text-[11px]">
                                <template x-if="berkasData?.kk?.foto_ktp_url">
                                    <a :href="berkasData.kk.foto_ktp_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-1 cursor-pointer">
                                        <span>Buka Dokumen</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- 3. BPJS Kesehatan / Ketenagakerjaan -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-2xs flex flex-col justify-between gap-3">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">🏥</span>
                                        <span class="font-bold text-xs text-slate-800">Kartu BPJS / JKN</span>
                                    </div>
                                    <template x-if="berkasData?.kk?.foto_bpjs_url">
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full text-[10px] font-bold">Tersedia</span>
                                    </template>
                                    <template x-if="!berkasData?.kk?.foto_bpjs_url">
                                        <span class="bg-slate-100 text-slate-500 border border-slate-200 px-2 py-0.5 rounded-full text-[10px] font-medium">Belum Diunggah</span>
                                    </template>
                                </div>
                                <template x-if="berkasData?.kk?.foto_bpjs_url">
                                    <div class="relative group rounded-xl overflow-hidden border border-slate-100 h-28 bg-slate-50 flex items-center justify-center">
                                        <img :src="berkasData.kk.foto_bpjs_url" alt="Kartu BPJS" class="h-full w-full object-cover group-hover:scale-105 transition" />
                                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                            <button type="button" @click="openLightbox(berkasData.kk.foto_bpjs_url, 'Kartu BPJS')" class="px-2.5 py-1 bg-white text-slate-800 text-[10px] font-bold rounded-lg shadow-sm cursor-pointer">
                                                🔍 Pratinjau
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!berkasData?.kk?.foto_bpjs_url">
                                    <div class="h-28 rounded-xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs bg-slate-50/50">
                                        <span class="text-xl mb-1">🏥</span>
                                        <span>Belum ada foto BPJS</span>
                                    </div>
                                </template>
                            </div>
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2 text-[11px]">
                                <template x-if="berkasData?.kk?.foto_bpjs_url">
                                    <a :href="berkasData.kk.foto_bpjs_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-1 cursor-pointer">
                                        <span>Buka Dokumen</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <!-- 4. Dokumen Pendukung / Surat Pengantar -->
                        <div class="p-4 rounded-2xl border border-slate-200 bg-white shadow-2xs flex flex-col justify-between gap-3">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold">📑</span>
                                        <span class="font-bold text-xs text-slate-800">Surat Pengantar / Pendukung</span>
                                    </div>
                                    <template x-if="berkasData?.kk?.foto_lainnya_url">
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full text-[10px] font-bold">Tersedia</span>
                                    </template>
                                    <template x-if="!berkasData?.kk?.foto_lainnya_url">
                                        <span class="bg-slate-100 text-slate-500 border border-slate-200 px-2 py-0.5 rounded-full text-[10px] font-medium">Belum Diunggah</span>
                                    </template>
                                </div>
                                <template x-if="berkasData?.kk?.foto_lainnya_url">
                                    <div class="relative group rounded-xl overflow-hidden border border-slate-100 h-28 bg-slate-50 flex items-center justify-center">
                                        <img :src="berkasData.kk.foto_lainnya_url" alt="Dokumen Pendukung" class="h-full w-full object-cover group-hover:scale-105 transition" />
                                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                            <button type="button" @click="openLightbox(berkasData.kk.foto_lainnya_url, 'Dokumen Pendukung')" class="px-2.5 py-1 bg-white text-slate-800 text-[10px] font-bold rounded-lg shadow-sm cursor-pointer">
                                                🔍 Pratinjau
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!berkasData?.kk?.foto_lainnya_url">
                                    <div class="h-28 rounded-xl border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-400 text-xs bg-slate-50/50">
                                        <span class="text-xl mb-1">📑</span>
                                        <span>Belum ada dokumen pendukung</span>
                                    </div>
                                </template>
                            </div>
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2 text-[11px]">
                                <template x-if="berkasData?.kk?.foto_lainnya_url">
                                    <a :href="berkasData.kk.foto_lainnya_url" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-700 font-bold inline-flex items-center gap-1 cursor-pointer">
                                        <span>Buka Dokumen</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </template>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Form Ringkas Admin untuk Mengunggah / Memperbarui File Berkas KK -->
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-3">
                    <div>
                        <span class="font-extrabold text-xs text-slate-900 flex items-center gap-1.5">
                            <span>📤</span> Unggah / Perbarui Berkas Dokumen Keluarga
                        </span>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pengurus RT dapat melengkapi atau memperbarui file berkas keluarga di bawah ini.</p>
                    </div>

                    <form :action="'{{ url('admin/warga') }}/' + berkasData?.warga?.id + '/berkas'" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Unggah Scan KK</label>
                                <input type="file" name="foto_kk" accept="image/*,application/pdf"
                                       class="w-full text-[11px] text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-white p-1" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Unggah KTP Kepala Keluarga</label>
                                <input type="file" name="foto_ktp" accept="image/*,application/pdf"
                                       class="w-full text-[11px] text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-white p-1" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Unggah Foto BPJS</label>
                                <input type="file" name="foto_bpjs" accept="image/*,application/pdf"
                                       class="w-full text-[11px] text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-white p-1" />
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Unggah Dokumen Pendukung</label>
                                <input type="file" name="foto_lainnya" accept="image/*,application/pdf"
                                       class="w-full text-[11px] text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-slate-200 rounded-xl bg-white p-1" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-1">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-4 py-2 text-xs font-semibold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span>Simpan & Perbarui Berkas</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer Modal -->
                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <a href="{{ route('admin.kk.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                        <span>Buka Menu Data KK Lengkap</span>
                        <span>→</span>
                    </a>
                    <button type="button" @click="isBerkasModalOpen = false" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL 4: LIGHTBOX FULLSCREEN                                              -->
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
        function wargaPage() {
            return {
                isAddModalOpen: false,
                isEditModalOpen: false,
                isBerkasModalOpen: false,
                editForm: {
                    id: null,
                    nama_lengkap: '',
                    nik: '',
                    no_kk: '',
                    status_keluarga: 'Kepala Keluarga',
                    status_tinggal: 'tetap',
                    nomor_rumah: '',
                    blok: '',
                    no_hp: '',
                },
                berkasData: null,
                lightboxUrl: null,
                lightboxTitle: '',

                openAddModal() {
                    this.isAddModalOpen = true;
                },

                openEditModal(warga) {
                    this.editForm = {
                        id: warga.id,
                        nama_lengkap: warga.nama_lengkap || '',
                        nik: warga.nik || '',
                        no_kk: warga.no_kk || '',
                        status_keluarga: warga.status_keluarga || 'Kepala Keluarga',
                        status_tinggal: (warga.status_tinggal || 'tetap').toLowerCase(),
                        nomor_rumah: warga.nomor_rumah || '',
                        blok: warga.blok || '',
                        no_hp: warga.no_hp || '',
                    };
                    this.isEditModalOpen = true;
                },

                async openBerkasModal(wargaId) {
                    this.berkasData = null;
                    this.isBerkasModalOpen = true;

                    try {
                        const res = await fetch(`{{ url('admin/warga') }}/${wargaId}/berkas`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            this.berkasData = await res.json();
                        }
                    } catch (e) {
                        console.error('Gagal mengambil repositori berkas:', e);
                    }
                },

                openLightbox(url, title) {
                    this.lightboxUrl = url;
                    this.lightboxTitle = title || 'Pratinjau Berkas';
                }
            };
        }
    </script>
</x-admin-layout>
