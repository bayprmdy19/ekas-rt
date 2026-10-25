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
        Schema::create('master_iurans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_iuran');
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_mulai')->nullable();
            $table->string('status')->default('Aktif'); // Aktif / Nonaktif
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Ubah jenis_iuran di iuran_wargas agar fleksibel menampung nama iuran apa pun
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE iuran_wargas MODIFY COLUMN jenis_iuran VARCHAR(100) NOT NULL");
        } catch (\Throwable $e) {
            // Abaikan jika database selain mysql / sudah varchar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_iurans');
    }
};
