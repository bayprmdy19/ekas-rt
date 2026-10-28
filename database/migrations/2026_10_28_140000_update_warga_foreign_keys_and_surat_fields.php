<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modifikasi foreign key iuran_wargas agar nullable dan ON DELETE SET NULL
        if (DB::getDriverName() !== 'sqlite') {
            try {
                Schema::table('iuran_wargas', function (Blueprint $table) {
                    $table->dropForeign(['warga_id']);
                });
            } catch (\Throwable $e) {
                // Abaikan jika foreign key belum terpasang atau beda nama
            }

            try {
                DB::statement('ALTER TABLE iuran_wargas MODIFY warga_id BIGINT UNSIGNED NULL');
                DB::statement('ALTER TABLE iuran_wargas ADD CONSTRAINT iuran_wargas_warga_id_foreign FOREIGN KEY (warga_id) REFERENCES wargas(id) ON DELETE SET NULL');
            } catch (\Throwable $e) {
                // Fallback
            }
        }

        // 2. Tambah kolom warga_id ke kas_rts dengan ON DELETE SET NULL
        Schema::table('kas_rts', function (Blueprint $table) {
            if (!Schema::hasColumn('kas_rts', 'warga_id')) {
                $table->foreignId('warga_id')->nullable()->after('id')->constrained('wargas')->nullOnDelete();
            }
        });

        // 3. Tambah kolom pendukung ke tabel administrasis (Pengajuan Surat)
        Schema::table('administrasis', function (Blueprint $table) {
            if (!Schema::hasColumn('administrasis', 'warga_id')) {
                $table->foreignId('warga_id')->nullable()->after('user_id')->constrained('wargas')->nullOnDelete();
            }
            if (!Schema::hasColumn('administrasis', 'nomor_whatsapp')) {
                $table->string('nomor_whatsapp', 20)->nullable()->after('nama_pengaju');
            }
            if (!Schema::hasColumn('administrasis', 'tujuan_surat')) {
                $table->string('tujuan_surat')->nullable()->after('keperluan');
            }
            if (!Schema::hasColumn('administrasis', 'keterangan_detail')) {
                $table->text('keterangan_detail')->nullable()->after('tujuan_surat');
            }
        });

        // 4. Tambah kolom pendukung ke tabel pengaduans
        Schema::table('pengaduans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaduans', 'warga_id')) {
                $table->foreignId('warga_id')->nullable()->after('user_id')->constrained('wargas')->nullOnDelete();
            }
            if (!Schema::hasColumn('pengaduans', 'nomor_whatsapp')) {
                $table->string('nomor_whatsapp', 20)->nullable()->after('nama_pelapor');
            }
            if (!Schema::hasColumn('pengaduans', 'tujuan_surat')) {
                $table->string('tujuan_surat')->nullable()->after('isi_laporan');
            }
            if (!Schema::hasColumn('pengaduans', 'keterangan_detail')) {
                $table->text('keterangan_detail')->nullable()->after('tujuan_surat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduans', function (Blueprint $table) {
            if (Schema::hasColumn('pengaduans', 'warga_id')) {
                $table->dropForeign(['warga_id']);
            }
            $table->dropColumn(['warga_id', 'nomor_whatsapp', 'tujuan_surat', 'keterangan_detail']);
        });

        Schema::table('administrasis', function (Blueprint $table) {
            if (Schema::hasColumn('administrasis', 'warga_id')) {
                $table->dropForeign(['warga_id']);
            }
            $table->dropColumn(['warga_id', 'nomor_whatsapp', 'tujuan_surat', 'keterangan_detail']);
        });

        Schema::table('kas_rts', function (Blueprint $table) {
            if (Schema::hasColumn('kas_rts', 'warga_id')) {
                $table->dropForeign(['warga_id']);
                $table->dropColumn(['warga_id']);
            }
        });
    }
};

