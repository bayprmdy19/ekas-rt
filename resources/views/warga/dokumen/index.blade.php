<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        Portal Warga
                    </span>
                    <h2 class="font-bold text-xl text-slate-800 leading-tight">
                        Kelengkapan Data Diri & Dokumen KK
                    </h2>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Unggah dan kelola foto dokumen Kartu Keluarga, KTP, dan BPJS untuk keperluan administrasi dan pencatatan RT/RW Digital.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('warga.dashboard') }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition shadow-2xs">
                    ← Kembali ke Dashboard Warga
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

            <!-- Main Form Card -->
            <div class="p-6 sm:p-8 bg-white border border-slate-200/90 rounded-3xl shadow-sm">
                @include('profile.partials.update-dokumen-kk-form')
            </div>

            <!-- Petunjuk & Privasi Keamanan -->
            <div class="p-5 bg-blue-50/70 border border-blue-100 rounded-2xl text-xs text-blue-900 flex items-start gap-3">
                <span class="text-xl">🔒</span>
                <div class="space-y-1">
                    <h4 class="font-extrabold text-blue-900">Keamanan & Kerahasiaan Data Pribadi</h4>
                    <p class="text-[11px] text-blue-800/80 leading-relaxed">
                        Seluruh foto berkas KK, KTP, dan BPJS yang Anda unggah hanya dapat diakses dan diverifikasi oleh Pengurus RT resmi yang bertugas untuk keperluan pencatatan kependudukan serta administrasi pengantar surat. Data Anda tersimpan secara aman dan terenkripsi.
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
