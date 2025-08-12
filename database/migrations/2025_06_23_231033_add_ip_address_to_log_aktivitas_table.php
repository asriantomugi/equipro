<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menjalankan migrasi: menambahkan kolom ip_address ke tabel log_aktivitas.
     */
    public function up(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('aktivitas');
        });
    }

    /**
     * Membalik migrasi: menghapus kolom ip_address dari tabel log_aktivitas.
     */
    public function down(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });
    }
};
