<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('kondisi_layanan_temp')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('kondisi_layanan_temp')->nullable(false)->change();
        });
    }

};
