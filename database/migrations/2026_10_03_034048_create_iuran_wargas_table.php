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
        Schema::create('iuran_wargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('wargas')->onDelete('cascade'); // Terhubung ke warga
            $table->enum('jenis_iuran', ['bulanan', 'sampah', 'ronda']); // Jenis tagihan iuran
            $table->string('bulan_tahun'); // Contoh format: '2026-10' (Oktober 2026)
            $table->decimal('jumlah', 12, 2); // Nominal iuran (contoh: 25000.00)
            $table->enum('status', ['belum_bayar', 'lunas'])->default('belum_bayar'); // Status pembayaran
            $table->date('tanggal_bayar')->nullable(); // Tanggal saat pembayaran dilakukan
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iuran_wargas');
    }
};