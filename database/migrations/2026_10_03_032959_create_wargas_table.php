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
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nik', 16)->unique()->nullable(); // Boleh opsional jika tidak ingin mencatat NIK
            $table->string('nomor_rumah'); // Contoh: A-12
            $table->string('no_hp')->nullable();
            $table->enum('status_tinggal', ['tetap', 'kontrak'])->default('tetap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wargas');
    }
};