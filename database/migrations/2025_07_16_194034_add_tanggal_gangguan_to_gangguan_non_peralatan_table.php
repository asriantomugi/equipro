<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('gangguan_non_peralatan', function (Blueprint $table) {
            if (!Schema::hasColumn('gangguan_non_peralatan', 'tanggal_gangguan')) {
                $table->date('tanggal_gangguan')->nullable()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('gangguan_non_peralatan', function (Blueprint $table) {
            $table->dropColumn('tanggal_gangguan');
        });
    }
};
