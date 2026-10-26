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
        Schema::create('kartu_keluargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('warga_id')->nullable()->constrained('wargas')->nullOnDelete();
            $table->string('nomor_kk', 16)->nullable()->index();
            $table->string('kepala_keluarga');
            $table->string('nik_kepala_keluarga', 16)->nullable();
            $table->string('nomor_rumah');
            $table->string('rt', 5)->default('01');
            $table->string('rw', 5)->default('01');
            $table->string('no_hp')->nullable();
            $table->unsignedInteger('jumlah_anggota')->default(1);
            $table->string('foto_kk')->nullable();
            $table->string('foto_ktp')->nullable();
            $table->string('foto_bpjs')->nullable();
            $table->string('foto_lainnya')->nullable();
            $table->enum('status_verifikasi', ['menunggu', 'terverifikasi', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_keluargas');
    }
};
