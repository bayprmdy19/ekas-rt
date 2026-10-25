<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Akun Pengurus (Admin) & Warga
        DB::table('users')->updateOrInsert(
            ['username' => 'admin'],
            [
                'name' => 'Pengurus RT 01',
                'username' => 'admin',
                'email' => 'admin@ekasrt.id',
                'password' => Hash::make('Admin1234'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('users')->updateOrInsert(
            ['username' => 'budi_santoso'],
            [
                'name' => 'Budi Santoso',
                'username' => 'budi_santoso',
                'email' => 'budi@warga.id',
                'password' => Hash::make('Warga1234'),
                'role' => 'warga',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 1. Data Dummy Warga (jika belum ada)
        if (DB::table('wargas')->count() === 0) {
            $warga1 = DB::table('wargas')->insertGetId([
                'nama_lengkap' => 'Budi Santoso',
                'nik' => '3201234567890001',
                'nomor_rumah' => 'Blok A No. 12',
                'no_hp' => '081234567890',
                'status_tinggal' => 'tetap',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $warga2 = DB::table('wargas')->insertGetId([
                'nama_lengkap' => 'Siti Rahma',
                'nik' => '3201234567890002',
                'nomor_rumah' => 'Blok B No. 05',
                'no_hp' => '085678901234',
                'status_tinggal' => 'tetap',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Data Transaksi Kas RT
            DB::table('kas_rts')->insert([
                [
                    'tanggal' => Carbon::now()->subDays(5),
                    'kategori' => 'Iuran Warga Bulanan',
                    'jenis' => 'masuk',
                    'jumlah' => 750000,
                    'keterangan' => 'Pemasukan iuran kas bulan ini',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'tanggal' => Carbon::now()->subDays(2),
                    'kategori' => 'Perbaikan Fasilitas',
                    'jenis' => 'keluar',
                    'jumlah' => 150000,
                    'keterangan' => 'Pembelian lampu jalan Blok A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // 3. Data Status Iuran Warga
            DB::table('iuran_wargas')->insert([
                [
                    'warga_id' => $warga1,
                    'jenis_iuran' => 'bulanan',
                    'bulan_tahun' => '2026-10',
                    'jumlah' => 50000,
                    'status' => 'lunas',
                    'tanggal_bayar' => now()->toDateString(),
                    'keterangan' => 'Lunas via transfer',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'warga_id' => $warga2,
                    'jenis_iuran' => 'bulanan',
                    'bulan_tahun' => '2026-10',
                    'jumlah' => 50000,
                    'status' => 'belum_bayar',
                    'tanggal_bayar' => null,
                    'keterangan' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            // 4. Data Pengaduan Lingkungan
            DB::table('pengaduans')->insert([
                [
                    'nomor_rumah' => 'Blok A No. 12',
                    'nama_pelapor' => 'Budi Santoso',
                    'judul_laporan' => 'Lampu Jalan Mati di Blok A',
                    'isi_laporan' => 'Lampu jalan dekat tiang listrik nomor 3 padam sejak kemarin malam.',
                    'status' => 'diproses',
                    'created_at' => now()->subDays(1),
                    'updated_at' => now()->subDays(1),
                ]
            ]);

            // 5. Data Administrasi Surat
            DB::table('administrasis')->insert([
                [
                    'nomor_rumah' => 'Blok B No. 05',
                    'nama_pengaju' => 'Siti Rahma',
                    'jenis_surat' => 'Surat Pengantar KTP',
                    'keperluan' => 'Permohonan surat pengantar pembuatan KTP baru.',
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // 6. Data Master Iuran (sesuai tampilan di Daftar Iuran)
        if (DB::table('master_iurans')->count() === 0) {
            DB::table('master_iurans')->insert([
                [
                    'nama_iuran' => 'Iuran RT',
                    'nominal' => 75000,
                    'tanggal_mulai' => '2026-06-25',
                    'status' => 'Aktif',
                    'keterangan' => 'Iuran wajib pemeliharaan lingkungan RT',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_iuran' => 'Keamanan',
                    'nominal' => 25000,
                    'tanggal_mulai' => '2026-06-25',
                    'status' => 'Aktif',
                    'keterangan' => 'Honor keamanan & pos ronda lingkungan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_iuran' => 'Kebersihan',
                    'nominal' => 10000,
                    'tanggal_mulai' => '2026-06-25',
                    'status' => 'Aktif',
                    'keterangan' => 'Pengangkutan sampah rutin berkala',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_iuran' => 'Dana Sosial',
                    'nominal' => 10000,
                    'tanggal_mulai' => '2026-06-25',
                    'status' => 'Aktif',
                    'keterangan' => 'Bantuan warga sakit / terkena musibah',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_iuran' => 'Dana Kematian',
                    'nominal' => 10000,
                    'tanggal_mulai' => '2026-06-25',
                    'status' => 'Aktif',
                    'keterangan' => 'Santunan duka cita warga rukun tetangga',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_iuran' => 'Pembelian Alat Tenda',
                    'nominal' => 20000,
                    'tanggal_mulai' => '2026-06-25',
                    'status' => 'Aktif',
                    'keterangan' => 'Pengadaan tenda & inventaris perlengkapan bersama',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 7. Warga Arif & Data Tagihan
        $arif = DB::table('wargas')->where('nama_lengkap', 'Arif')->first();
        if (!$arif) {
            $arifId = DB::table('wargas')->insertGetId([
                'nama_lengkap' => 'Arif',
                'nik' => '3201234567890003',
                'nomor_rumah' => 'Blok C No. 08',
                'no_hp' => '087812345678',
                'status_tinggal' => 'tetap',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('iuran_wargas')->insert([
                [
                    'warga_id' => $arifId,
                    'jenis_iuran' => 'Iuran RT',
                    'bulan_tahun' => '2024-01',
                    'jumlah' => 75000,
                    'status' => 'lunas',
                    'tanggal_bayar' => '2024-01-10',
                    'keterangan' => '1/2024',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'warga_id' => $arifId,
                    'jenis_iuran' => 'Iuran RT',
                    'bulan_tahun' => '2024-02',
                    'jumlah' => 75000,
                    'status' => 'belum_bayar',
                    'tanggal_bayar' => null,
                    'keterangan' => '2/2024',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'warga_id' => $arifId,
                    'jenis_iuran' => 'Iuran RT',
                    'bulan_tahun' => '2024-03',
                    'jumlah' => 75000,
                    'status' => 'belum_bayar',
                    'tanggal_bayar' => null,
                    'keterangan' => '3/2024',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 8. Data Rekening RT & E-Wallet
        if (DB::table('rekening_rts')->count() === 0) {
            DB::table('rekening_rts')->insert([
                [
                    'nama_bank' => 'BCA',
                    'nomor_rekening' => '123456',
                    'atas_nama' => 'Rekening RT',
                    'jenis' => 'bank',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_bank' => 'DANA',
                    'nomor_rekening' => '87741130529',
                    'atas_nama' => 'Arif',
                    'jenis' => 'ewallet',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_bank' => 'OVO',
                    'nomor_rekening' => '87741130529',
                    'atas_nama' => 'ARIF',
                    'jenis' => 'ewallet',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 9. Data Warga AJie dan Pembayaran Sesuai Screenshot
        $ajie = DB::table('wargas')->where('nama_lengkap', 'AJie')->first();
        if (!$ajie) {
            $ajieId = DB::table('wargas')->insertGetId([
                'nama_lengkap' => 'AJie',
                'nik' => '3201234567890004',
                'nomor_rumah' => 'Blok D No. 02',
                'no_hp' => '081298765432',
                'status_tinggal' => 'tetap',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $ajieId = $ajie->id;
        }

        $arifData = DB::table('wargas')->where('nama_lengkap', 'Arif')->first();
        if ($arifData) {
            // Pastikan pembayaran sesuai screenshot ada
            $existingCount = DB::table('iuran_wargas')
                ->where('metode_pembayaran', 'Transfer Bank')
                ->where('status', 'lunas')
                ->count();

            if ($existingCount < 4) {
                DB::table('iuran_wargas')->insert([
                    [
                        'warga_id' => $arifData->id,
                        'jenis_iuran' => 'Iuran RT',
                        'bulan_tahun' => '2024-01',
                        'jumlah' => 75000,
                        'status' => 'lunas',
                        'metode_pembayaran' => 'Transfer Bank',
                        'tanggal_bayar' => '2026-06-25',
                        'keterangan' => '1/2024',
                        'created_at' => Carbon::parse('2026-06-25 09:30:00'),
                        'updated_at' => Carbon::parse('2026-06-25 09:30:00'),
                    ],
                    [
                        'warga_id' => $ajieId,
                        'jenis_iuran' => 'Iuran RT',
                        'bulan_tahun' => '2024-01',
                        'jumlah' => 50000,
                        'status' => 'lunas',
                        'metode_pembayaran' => 'Transfer Bank',
                        'tanggal_bayar' => '2026-06-25',
                        'keterangan' => '1/2024',
                        'created_at' => Carbon::parse('2026-06-25 10:15:00'),
                        'updated_at' => Carbon::parse('2026-06-25 10:15:00'),
                    ],
                    [
                        'warga_id' => $arifData->id,
                        'jenis_iuran' => 'Keamanan',
                        'bulan_tahun' => '2024-01',
                        'jumlah' => 25000,
                        'status' => 'lunas',
                        'metode_pembayaran' => 'Transfer Bank',
                        'tanggal_bayar' => '2026-06-26',
                        'keterangan' => '1/2024',
                        'created_at' => Carbon::parse('2026-06-26 08:20:00'),
                        'updated_at' => Carbon::parse('2026-06-26 08:20:00'),
                    ],
                    [
                        'warga_id' => $arifData->id,
                        'jenis_iuran' => 'Iuran RT',
                        'bulan_tahun' => '2024-01',
                        'jumlah' => 75000,
                        'status' => 'lunas',
                        'metode_pembayaran' => 'Transfer Bank',
                        'tanggal_bayar' => '2026-06-26',
                        'keterangan' => '1/2024',
                        'created_at' => Carbon::parse('2026-06-26 14:05:00'),
                        'updated_at' => Carbon::parse('2026-06-26 14:05:00'),
                    ],
                ]);
            }
        }

        // 8. Data Kas Keluar (Sesuai Tampilan Desain)
        $existingKasKeluar = DB::table('kas_rts')->where('jenis', 'keluar')->where('kategori', 'Kebersihan')->count();
        if ($existingKasKeluar === 0) {
            DB::table('kas_rts')->insert([
                [
                    'tanggal' => '2026-06-25',
                    'jenis' => 'keluar',
                    'kategori' => 'Kebersihan',
                    'jumlah' => 200000,
                    'keterangan' => 'Pengeluaran',
                    'created_at' => Carbon::parse('2026-06-25 10:00:00'),
                    'updated_at' => Carbon::parse('2026-06-25 10:00:00'),
                ],
                [
                    'tanggal' => '2026-06-25',
                    'jenis' => 'keluar',
                    'kategori' => 'Sosial',
                    'jumlah' => 500000,
                    'keterangan' => 'Tengok tetangga yang sakit',
                    'created_at' => Carbon::parse('2026-06-25 11:30:00'),
                    'updated_at' => Carbon::parse('2026-06-25 11:30:00'),
                ],
            ]);
        }
    }
}