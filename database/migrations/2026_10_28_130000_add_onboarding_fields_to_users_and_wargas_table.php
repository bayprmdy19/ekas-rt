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
        // 1. Tambahkan kolom status kelengkapan profil dan no_kk ke tabel users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_profile_complete')) {
                $table->boolean('is_profile_complete')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users', 'no_kk')) {
                $table->string('no_kk', 16)->nullable()->after('no_hp');
            }
        });

        // 2. Tambahkan kolom pendukung onboarding ke tabel wargas
        Schema::table('wargas', function (Blueprint $table) {
            if (!Schema::hasColumn('wargas', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('wargas', 'tempat_lahir')) {
                $table->string('tempat_lahir')->nullable()->after('nama_lengkap');
            }
            if (!Schema::hasColumn('wargas', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('wargas', 'is_berkeluarga')) {
                $table->boolean('is_berkeluarga')->default(false)->after('status_tinggal');
            }
            if (!Schema::hasColumn('wargas', 'memiliki_anak')) {
                $table->boolean('memiliki_anak')->default(false)->after('is_berkeluarga');
            }
            if (!Schema::hasColumn('wargas', 'file_ktp')) {
                $table->string('file_ktp')->nullable()->after('memiliki_anak');
            }
            if (!Schema::hasColumn('wargas', 'file_kk')) {
                $table->string('file_kk')->nullable()->after('file_ktp');
            }
            if (!Schema::hasColumn('wargas', 'is_profile_complete')) {
                $table->boolean('is_profile_complete')->default(false)->after('file_kk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            if (Schema::hasColumn('wargas', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
            $table->dropColumn([
                'user_id',
                'tempat_lahir',
                'tanggal_lahir',
                'is_berkeluarga',
                'memiliki_anak',
                'file_ktp',
                'file_kk',
                'is_profile_complete',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_profile_complete', 'no_kk']);
        });
    }
};

