<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak {{ $pengaduan->judul_laporan }} - E-KAS RT</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Tinos:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Tinos', serif;
            background-color: #f1f5f9;
        }
        .sans {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        @media print {
            body {
                background-color: #ffffff;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 1.5cm 2cm !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="min-h-screen text-slate-900 antialiased p-4 sm:p-8 flex flex-col items-center">

    <!-- Top Action Toolbar (Hanya Tampil di Layar, Tidak Ikut Tercetak) -->
    <div class="no-print max-w-3xl w-full mb-6 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3 sans">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                📄
            </div>
            <div>
                <h4 class="font-bold text-slate-800 text-sm">Dokumen Siap Dicetak / Disimpan</h4>
                <p class="text-slate-500 text-xs">Pilih format "Save as PDF" pada jendela printer untuk menyimpan berkas PDF.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('warga.dashboard') }}" 
               class="flex-1 sm:flex-none text-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition border border-slate-200">
                Kembali
            </a>
            <button onclick="window.print()" 
                    class="flex-1 sm:flex-none px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-blue-200 flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Unduh PDF</span>
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen Surat Resmi (Standar Kertas A4) -->
    <div class="print-container max-w-3xl w-full bg-white p-10 sm:p-14 rounded-2xl border border-slate-200 shadow-md leading-relaxed text-sm">
        
        <!-- KOP SURAT RT -->
        <div class="text-center relative pb-3 border-b-2 border-slate-900 space-y-1">
            <h3 class="text-base font-bold tracking-wider uppercase leading-none">PENGURUS RUKUN TETANGGA 01 / RW 05</h3>
            <h2 class="text-xl font-bold tracking-tight uppercase leading-snug">KELURAHAN / DESA SEJAHTERA</h2>
            <p class="text-xs text-slate-600 leading-tight">
                Sekretariat: Balai Pertemuan RT 01, Blok Sentral No. 01 • Telp/WhatsApp: 0812-3456-7890 • E-Mail: sekretariat@rt01sejahtera.id
            </p>
        </div>
        <!-- Garis Ganda Pembatas Kop Surat -->
        <div class="border-b border-slate-900 mt-0.5 mb-6"></div>

        <!-- JUDUL & NOMOR SURAT -->
        <div class="text-center my-6 space-y-1">
            <h1 class="text-lg font-bold tracking-wider underline uppercase">SURAT PENGANTAR RT</h1>
            <p class="text-xs text-slate-700 font-semibold tracking-wide">
                Nomor: {{ $nomorSurat }}
            </p>
        </div>

        <!-- KONTEN SURAT -->
        <div class="space-y-4 text-justify leading-relaxed mt-6">
            <p>
                Yang bertanda tangan di bawah ini, Ketua / Pengurus Rukun Tetangga (RT) 01 Rukun Warga (RW) 05, dengan ini menerangkan bahwa:
            </p>

            <!-- Tabel Identitas Pemohon -->
            <table class="w-full my-3 text-sm">
                <tbody>
                    <tr class="align-top">
                        <td class="w-44 py-1.5 font-bold">Nama Lengkap</td>
                        <td class="w-4 py-1.5">:</td>
                        <td class="py-1.5 uppercase font-bold text-slate-900">{{ $pengaduan->nama_pelapor ?: '-' }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="py-1.5 font-bold">Nomor Rumah / Blok</td>
                        <td class="py-1.5">:</td>
                        <td class="py-1.5 font-semibold text-slate-800">{{ $pengaduan->nomor_rumah ?: '-' }}</td>
                    </tr>
                    @if(isset($wargaProfile) && $wargaProfile->nik)
                    <tr class="align-top">
                        <td class="py-1.5 font-bold">NIK</td>
                        <td class="py-1.5">:</td>
                        <td class="py-1.5 font-semibold text-slate-800">{{ $wargaProfile->nik }}</td>
                    </tr>
                    @endif
                    <tr class="align-top">
                        <td class="py-1.5 font-bold">Keperluan / Permohonan</td>
                        <td class="py-1.5">:</td>
                        <td class="py-1.5 font-bold text-blue-900 uppercase underline">{{ $pengaduan->judul_laporan }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="py-1.5 font-bold">Keterangan Tambahan</td>
                        <td class="py-1.5">:</td>
                        <td class="py-1.5 text-slate-800 italic">"{{ $pengaduan->isi_laporan ?: 'Tidak ada keterangan khusus' }}"</td>
                    </tr>
                </tbody>
            </table>

            <p>
                Berdasarkan catatan kependudukan lingkungan, nama yang bersangkutan di atas adalah benar warga yang berdomisili sah di wilayah RT 01 / RW 05 dan memiliki kelakuan baik dalam kehidupan bermasyarakat.
            </p>

            <p>
                Surat pengantar ini diberikan kepada yang bersangkutan untuk dipergunakan sebagai kelengkapan berkas administrasi sesuai dengan keperluan tersebut di atas.
            </p>

            <p>
                Demikian surat pengantar ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya oleh pihak yang berkepentingan.
            </p>
        </div>

        <!-- TANDA TANGAN & PENGESAHAN KETUA RT -->
        <div class="mt-12 flex justify-between items-start text-xs sm:text-sm">
            <div class="w-48 text-center space-y-1">
                <p class="invisible">Tanda Tangan</p>
                <p class="font-bold">Pemohon / Warga,</p>
                <div class="h-20 flex items-center justify-center"></div>
                <p class="font-bold uppercase underline">{{ $pengaduan->nama_pelapor ?: '( ................................ )' }}</p>
            </div>

            <div class="w-64 text-center space-y-1 relative">
                <p class="text-slate-700">
                    Dikeluarkan di: Jakarta<br>
                    Pada tanggal: <strong>{{ $pengaduan->selesai_at ? $pengaduan->selesai_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</strong>
                </p>
                <p class="font-bold mt-1">Pengurus RT 01 / RW 05,</p>
                <p class="font-bold -mt-1">Ketua RT 01</p>

                <!-- Stempel & Tanda Tangan Digital RT -->
                <div class="h-20 relative flex items-center justify-center">
                    <!-- Cap Stempel Digital -->
                    <div class="absolute w-24 h-24 rounded-full border-2 border-dashed border-blue-600/60 text-blue-700 flex flex-col items-center justify-center text-[9px] font-black uppercase tracking-tight transform -rotate-12 opacity-80 pointer-events-none">
                        <span>★ RT 01 / RW 05 ★</span>
                        <span class="text-[7px]">TERVERIFIKASI</span>
                        <span class="text-[7px]">RESMI DIGITAL</span>
                    </div>
                    <!-- Tanda Tangan Digital / QR Stamp -->
                    <div class="font-serif italic text-lg text-slate-800 font-bold z-10 select-none">
                        Pengurus RT 01
                    </div>
                </div>

                <p class="font-bold uppercase underline mt-2">BAMBANG SUTEDJO, S.E.</p>
                <p class="text-[11px] text-slate-500 -mt-1 font-sans">Ketua RT 01 Periode 2024 - 2027</p>
            </div>
        </div>

        <!-- Catatan Kaki Dokumen Digital -->
        <div class="mt-12 pt-4 border-t border-slate-200 text-[10px] text-slate-400 flex items-center justify-between font-sans">
            <span>Dokumen resmi diterbitkan melalui Sistem Digital E-KAS RT.</span>
            <span>ID Berkas: RT01-SRT-{{ sprintf('%05d', $pengaduan->id) }}</span>
        </div>

    </div>

</body>
</html>
