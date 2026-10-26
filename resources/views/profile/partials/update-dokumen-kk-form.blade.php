<section x-data="{
    fotoKkPreview: '{{ $kk?->foto_kk_url ?? '' }}',
    fotoKtpPreview: '{{ $kk?->foto_ktp_url ?? '' }}',
    fotoBpjsPreview: '{{ $kk?->foto_bpjs_url ?? '' }}',
    fotoLainnyaPreview: '{{ $kk?->foto_lainnya_url ?? '' }}',
    lightboxImg: null,
    previewFile(event, key) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this[key] = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
}" class="space-y-6">
    <header class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    📁
                </span>
                <h2 class="text-base font-extrabold text-slate-900">
                    Kelengkapan Data Diri & Dokumen KK
                </h2>
            </div>
            <p class="mt-1 text-xs text-slate-500 font-medium">
                Unggah dokumen identitas asli (Foto KK, KTP, BPJS) agar data kependudukan keluarga Anda tercatat dan diverifikasi pengurus RT.
            </p>
        </div>

        <div>
            @if(!$kk)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Belum Dilengkapi
                </span>
            @elseif($kk->status_verifikasi === 'terverifikasi')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Dokumen Terverifikasi RT
                </span>
            @elseif($kk->status_verifikasi === 'ditolak')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Perlu Perbaikan Berkas
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Menunggu Verifikasi RT
                </span>
            @endif
        </div>
    </header>

    @if($kk && $kk->catatan_admin)
        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl text-xs text-amber-900 space-y-1">
            <span class="font-bold flex items-center gap-1 text-amber-800">
                ⚠️ Catatan dari Pengurus RT:
            </span>
            <p class="text-[11px] leading-relaxed">{{ $kk->catatan_admin }}</p>
        </div>
    @endif

    <form method="post" action="{{ route('warga.dokumen.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Grid Data Kependudukan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="nomor_kk" class="block font-bold text-xs text-slate-700 mb-1">
                    Nomor Kartu Keluarga (KK) <span class="text-slate-400 font-normal">(16 Digit)</span>
                </label>
                <input id="nomor_kk" name="nomor_kk" type="text" maxlength="20"
                       value="{{ old('nomor_kk', $kk?->nomor_kk) }}" 
                       placeholder="Contoh: 3201234567890001"
                       class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50" />
                @error('nomor_kk')
                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nik_kepala_keluarga" class="block font-bold text-xs text-slate-700 mb-1">
                    NIK Kepala Keluarga <span class="text-slate-400 font-normal">(16 Digit)</span>
                </label>
                <input id="nik_kepala_keluarga" name="nik_kepala_keluarga" type="text" maxlength="20"
                       value="{{ old('nik_kepala_keluarga', $kk?->nik_kepala_keluarga ?? $wargaProfile?->nik) }}" 
                       placeholder="Contoh: 3201234567890001"
                       class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50" />
                @error('nik_kepala_keluarga')
                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nomor_rumah" class="block font-bold text-xs text-slate-700 mb-1">
                    Nomor Rumah / Blok <span class="text-rose-500">*</span>
                </label>
                <input id="nomor_rumah" name="nomor_rumah" type="text" required
                       value="{{ old('nomor_rumah', $kk?->nomor_rumah ?? $wargaProfile?->nomor_rumah) }}" 
                       placeholder="Contoh: Blok A No. 12"
                       class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50" />
                @error('nomor_rumah')
                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="no_hp" class="block font-bold text-xs text-slate-700 mb-1">
                    Nomor WhatsApp / HP
                </label>
                <input id="no_hp" name="no_hp" type="text"
                       value="{{ old('no_hp', $kk?->no_hp ?? $wargaProfile?->no_hp) }}" 
                       placeholder="Contoh: 081234567890"
                       class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50" />
                @error('no_hp')
                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jumlah_anggota" class="block font-bold text-xs text-slate-700 mb-1">
                    Jumlah Anggota Keluarga Tercatat
                </label>
                <input id="jumlah_anggota" name="jumlah_anggota" type="number" min="1" max="30"
                       value="{{ old('jumlah_anggota', $kk?->jumlah_anggota ?? 1) }}" 
                       class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-600 focus:ring-blue-600 bg-slate-50/50" />
                @error('jumlah_anggota')
                    <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label for="rt" class="block font-bold text-xs text-slate-700 mb-1">RT</label>
                    <input id="rt" name="rt" type="text" value="{{ old('rt', $kk?->rt ?? '01') }}" class="w-full text-xs rounded-xl border-slate-200 bg-slate-50/50" />
                </div>
                <div>
                    <label for="rw" class="block font-bold text-xs text-slate-700 mb-1">RW</label>
                    <input id="rw" name="rw" type="text" value="{{ old('rw', $kk?->rw ?? '01') }}" class="w-full text-xs rounded-xl border-slate-200 bg-slate-50/50" />
                </div>
            </div>
        </div>

        <!-- Bagian Unggah Foto Dokumen Diri & KK -->
        <div class="space-y-4 pt-3 border-t border-slate-100">
            <div>
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                    Unggah Berkas Foto Dokumen Asli
                </h3>
                <p class="text-[11px] text-slate-500">
                    Pastikan foto dokumen jelas, tidak terpotong, dan dapat terbaca dengan baik. Format JPG, PNG, WEBP, atau PDF (maks. 5MB per berkas).
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <!-- 1. Foto Kartu Keluarga (KK) -->
                <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 transition space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            📄 1. Foto Kartu Keluarga (KK)
                        </span>
                        <template x-if="fotoKkPreview">
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                                ✓ Ada Berkas
                            </span>
                        </template>
                    </div>

                    <!-- Area Preview -->
                    <template x-if="fotoKkPreview">
                        <div class="relative group rounded-xl overflow-hidden border border-slate-100 bg-slate-50 h-36 flex items-center justify-center">
                            <img :src="fotoKkPreview" alt="Foto KK" class="h-full w-full object-cover group-hover:scale-105 transition" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <button type="button" @click="lightboxImg = fotoKkPreview" class="px-2.5 py-1.5 rounded-lg bg-white/90 text-slate-800 font-bold text-[10px] shadow">
                                    🔍 Perbesar
                                </button>
                            </div>
                        </div>
                    </template>

                    <input type="file" name="foto_kk" id="foto_kk" accept="image/*,.pdf"
                           @change="previewFile($event, 'fotoKkPreview')"
                           class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                    @error('foto_kk')
                        <p class="text-rose-500 text-[10px]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Foto KTP -->
                <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 transition space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            🪪 2. Foto KTP (Kepala Keluarga / Warga)
                        </span>
                        <template x-if="fotoKtpPreview">
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                                ✓ Ada Berkas
                            </span>
                        </template>
                    </div>

                    <!-- Area Preview -->
                    <template x-if="fotoKtpPreview">
                        <div class="relative group rounded-xl overflow-hidden border border-slate-100 bg-slate-50 h-36 flex items-center justify-center">
                            <img :src="fotoKtpPreview" alt="Foto KTP" class="h-full w-full object-cover group-hover:scale-105 transition" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <button type="button" @click="lightboxImg = fotoKtpPreview" class="px-2.5 py-1.5 rounded-lg bg-white/90 text-slate-800 font-bold text-[10px] shadow">
                                    🔍 Perbesar
                                </button>
                            </div>
                        </div>
                    </template>

                    <input type="file" name="foto_ktp" id="foto_ktp" accept="image/*,.pdf"
                           @change="previewFile($event, 'fotoKtpPreview')"
                           class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                    @error('foto_ktp')
                        <p class="text-rose-500 text-[10px]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3. Foto BPJS -->
                <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 transition space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            🏥 3. Foto Kartu BPJS (Kesehatan / Ketenagakerjaan)
                        </span>
                        <template x-if="fotoBpjsPreview">
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                                ✓ Ada Berkas
                            </span>
                        </template>
                    </div>

                    <!-- Area Preview -->
                    <template x-if="fotoBpjsPreview">
                        <div class="relative group rounded-xl overflow-hidden border border-slate-100 bg-slate-50 h-36 flex items-center justify-center">
                            <img :src="fotoBpjsPreview" alt="Foto BPJS" class="h-full w-full object-cover group-hover:scale-105 transition" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <button type="button" @click="lightboxImg = fotoBpjsPreview" class="px-2.5 py-1.5 rounded-lg bg-white/90 text-slate-800 font-bold text-[10px] shadow">
                                    🔍 Perbesar
                                </button>
                            </div>
                        </div>
                    </template>

                    <input type="file" name="foto_bpjs" id="foto_bpjs" accept="image/*,.pdf"
                           @change="previewFile($event, 'fotoBpjsPreview')"
                           class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                    @error('foto_bpjs')
                        <p class="text-rose-500 text-[10px]">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 4. Foto Dokumen Lainnya (Opsional) -->
                <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 transition space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            📑 4. Foto Dokumen Pendukung Lainnya <span class="text-slate-400 font-normal">(Opsional)</span>
                        </span>
                        <template x-if="fotoLainnyaPreview">
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                                ✓ Ada Berkas
                            </span>
                        </template>
                    </div>

                    <!-- Area Preview -->
                    <template x-if="fotoLainnyaPreview">
                        <div class="relative group rounded-xl overflow-hidden border border-slate-100 bg-slate-50 h-36 flex items-center justify-center">
                            <img :src="fotoLainnyaPreview" alt="Foto Dokumen Lainnya" class="h-full w-full object-cover group-hover:scale-105 transition" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                <button type="button" @click="lightboxImg = fotoLainnyaPreview" class="px-2.5 py-1.5 rounded-lg bg-white/90 text-slate-800 font-bold text-[10px] shadow">
                                    🔍 Perbesar
                                </button>
                            </div>
                        </div>
                    </template>

                    <input type="file" name="foto_lainnya" id="foto_lainnya" accept="image/*,.pdf"
                           @change="previewFile($event, 'fotoLainnyaPreview')"
                           class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" />
                    <p class="text-[10px] text-slate-400">Contoh: Akta Kelahiran, Surat Nikah, atau Bukti Domisili.</p>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2.5 bg-[#1d4ed8] hover:bg-blue-800 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span>Simpan & Kirim Dokumen ke RT</span>
            </button>
        </div>
    </form>

    <!-- Modal Lightbox Pembesaran Foto -->
    <div x-show="lightboxImg" 
         x-cloak
         @click.away="lightboxImg = null"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-[90vh] bg-white rounded-3xl overflow-hidden p-2 shadow-2xl">
            <button type="button" @click="lightboxImg = null" class="absolute top-4 right-4 w-9 h-9 rounded-full bg-black/60 text-white hover:bg-black flex items-center justify-center font-bold text-sm z-10 transition">
                ✕
            </button>
            <img :src="lightboxImg" alt="Pratinjau Dokumen" class="max-h-[85vh] w-auto mx-auto rounded-2xl object-contain" />
        </div>
    </div>
</section>
