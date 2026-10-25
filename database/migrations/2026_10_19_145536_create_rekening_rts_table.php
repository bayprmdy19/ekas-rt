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
        Schema::create('rekening_rts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bank');
            $table->string('nomor_rekening');
            $table->string('atas_nama');
            $table->string('jenis')->default('bank'); // bank / ewallet
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('iuran_wargas', function (Blueprint $table) {
            if (!Schema::hasColumn('iuran_wargas', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->default('Transfer Bank')->after('status');
            }
            if (!Schema::hasColumn('iuran_wargas', 'bukti_bayar')) {
                $table->string('bukti_bayar')->nullable()->after('metode_pembayaran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iuran_wargas', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'bukti_bayar']);
        });
        Schema::dropIfExists('rekening_rts');
    }
};
