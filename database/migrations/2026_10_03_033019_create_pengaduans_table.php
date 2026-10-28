<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_rumah'); // Identifikasi pelapor tanpa perlu login
            $table->string('nama_pelapor')->nullable(); // Nama warga (opsional, bisa anonim jika mau)
            $table->string('judul_laporan'); // Contoh: Lampu Jalan Blok A Mati / Sampah Belum Diangkut
            $table->text('isi_laporan'); // Detail keluhan atau pengaduan warga
            $table->string('status', 50)->default('diterima');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};