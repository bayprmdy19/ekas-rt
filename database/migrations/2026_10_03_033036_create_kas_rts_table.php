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
        Schema::create('kas_rts', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal'); // Tanggal transaksi kas
            $table->enum('jenis', ['masuk', 'keluar']); // Pemasukan atau Pengeluaran
            $table->decimal('jumlah', 12, 2); // Nominal uang (contoh: 150000.00)
            $table->string('kategori'); // Contoh: Iuran Warga, Kebersihan, Keamanan, Perbaikan
            $table->text('keterangan'); // Detail penjelasan transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_rts');
    }
};