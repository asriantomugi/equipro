<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn('waktu');

            // Tambahkan kolom baru
            $table->dateTime('waktu_open')->nullable()->after('jenis');
            $table->dateTime('waktu_close')->nullable()->after('waktu_open');
        });
    }

    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            // Kembalikan ke kondisi semula jika di-rollback
            $table->dropColumn(['waktu_open', 'waktu_close']);
            $table->dateTime('waktu')->nullable()->after('jenis');
        });
    }
};
