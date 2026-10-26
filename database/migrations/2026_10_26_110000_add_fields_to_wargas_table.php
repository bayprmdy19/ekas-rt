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
        Schema::table('wargas', function (Blueprint $table) {
            $table->string('no_kk', 20)->nullable()->after('nik');
            $table->string('status_keluarga', 50)->default('Kepala Keluarga')->after('no_kk');
            $table->string('blok', 50)->nullable()->after('nomor_rumah');
            $table->foreignId('kartu_keluarga_id')->nullable()->after('status_tinggal')->constrained('kartu_keluargas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropForeign(['kartu_keluarga_id']);
            $table->dropColumn(['no_kk', 'status_keluarga', 'blok', 'kartu_keluarga_id']);
        });
    }
};
