<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            if (!Schema::hasColumn('log_aktivitas', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');

                // Jika kamu ingin relasi foreign key ke users
                // Hapus komentar berikut jika perlu
                // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            if (Schema::hasColumn('log_aktivitas', 'user_id')) {
                // Hapus foreign key dulu jika kamu menambahkan relasi
                // $table->dropForeign(['user_id']);

                $table->dropColumn('user_id');
            }
        });
    }
};
