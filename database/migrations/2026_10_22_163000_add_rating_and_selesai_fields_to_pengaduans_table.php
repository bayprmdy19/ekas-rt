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
        Schema::table('pengaduans', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaduans', 'kategori')) {
                $table->string('kategori', 100)->nullable()->after('nama_pelapor');
            }
            if (!Schema::hasColumn('pengaduans', 'foto')) {
                $table->string('foto')->nullable()->after('isi_laporan');
            }
            if (!Schema::hasColumn('pengaduans', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('status');
            }
            if (!Schema::hasColumn('pengaduans', 'ulasan_warga')) {
                $table->text('ulasan_warga')->nullable()->after('rating');
            }
            if (!Schema::hasColumn('pengaduans', 'selesai_at')) {
                $table->timestamp('selesai_at')->nullable()->after('ulasan_warga');
            }
        });

        // Ubah kolom status agar mendukung nilai 'diterima', 'pending', 'diproses', 'selesai'
        try {
            DB::statement("ALTER TABLE pengaduans MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'diterima'");
        } catch (\Throwable $e) {
            // Abaikan jika tidak diizinkan di database engine tertentu
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduans', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['kategori', 'foto', 'rating', 'ulasan_warga', 'selesai_at'] as $col) {
                if (Schema::hasColumn('pengaduans', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
