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
        Schema::table('administrasis', function (Blueprint $table) {
            if (!Schema::hasColumn('administrasis', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('administrasis', 'nomor_surat')) {
                $table->string('nomor_surat', 100)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('administrasis', 'catatan')) {
                $table->text('catatan')->nullable()->after('status');
            }
        });

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE administrasis MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'menunggu'");
        } catch (\Throwable $e) {
            // Abaikan jika tidak diizinkan DB engine tertentu
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('administrasis', function (Blueprint $table) {
            $cols = [];
            foreach (['user_id', 'nomor_surat', 'catatan'] as $col) {
                if (Schema::hasColumn('administrasis', $col)) {
                    $cols[] = $col;
                }
            }
            if (in_array('user_id', $cols)) {
                $table->dropForeign(['user_id']);
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
