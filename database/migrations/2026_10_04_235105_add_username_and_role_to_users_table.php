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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->enum('role', ['admin', 'pengurus', 'warga'])->default('warga')->after('password');
        });

        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role']);
        });
    }
};
