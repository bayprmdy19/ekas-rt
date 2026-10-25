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
        Schema::create('administrasis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_rumah'); // Identifikasi pengaju tanpa perlu login
            $table->string('nama_pengaju'); // Nama warga yang mengajukan surat
            $table->string('jenis_surat'); // Contoh: Surat Pengantar RT, Keterangan Domisili
            $table->text('keperluan'); // Alasan/tujuan pengajuan
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrasis');
    }
};