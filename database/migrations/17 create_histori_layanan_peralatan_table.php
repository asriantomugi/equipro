<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Histori Layanan Perperalatanan
     *
     * @return tabel Histori Layanan Perperalatanan di database
     */
    public function up(): void
    {
        Schema::create('histori_layanan_perperalatanan', function (Blueprint $table) {

            // membuat field-field
            $table->id();
            $table->bigInteger('layanan_id');
            $table->bigInteger('peralatan_id');
            $table->bigInteger('laporan_id');
            $table->datetime('waktu_pasang');
            $table->datetime('waktu_lepas')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_layanan_peralatan');
    }
};
