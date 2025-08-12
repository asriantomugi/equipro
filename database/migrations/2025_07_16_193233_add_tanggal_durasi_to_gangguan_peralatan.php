<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gangguan_peralatan', function (Blueprint $table) {
            if (!Schema::hasColumn('gangguan_peralatan', 'tanggal_gangguan')) {
                $table->date('tanggal_gangguan')->nullable()->after('deskripsi');
            }
            if (!Schema::hasColumn('gangguan_peralatan', 'bulan')) {
                $table->string('bulan', 2)->nullable()->after('tanggal_gangguan');
            }
            if (!Schema::hasColumn('gangguan_peralatan', 'durasi_perbaikan')) {
                $table->integer('durasi_perbaikan')->nullable()->after('bulan');
            }
            if (!Schema::hasColumn('gangguan_peralatan', 'durasi_mtbf')) {
                $table->integer('durasi_mtbf')->nullable()->after('durasi_perbaikan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gangguan_peralatan', function (Blueprint $table) {
            $table->dropColumn(['tanggal_gangguan', 'bulan', 'durasi_perbaikan', 'durasi_mtbf']);
        });
    }
};
