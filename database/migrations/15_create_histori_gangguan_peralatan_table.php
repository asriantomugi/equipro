<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Histori Gangguan Peralatan
     *
     * @return tabel Histori Gangguan Peralatan di database
     */
    public function up(): void
    {
        Schema::create('histori_gangguan_peralatan', function (Blueprint $table) {

            // membuat field-field
            $table->id();
            $table->bigInteger('layanan_id');
            $table->bigInteger('peralatan_id');
            $table->bigInteger('laporan_id');
            $table->datetime('waktu_gangguan');
            $table->datetime('waktu_normal')->nullable();
            $table->boolean('status')->default(false);
            $table->string('keterangan')->nullable();
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
        Schema::dropIfExists('histori_gangguan_peralatan');
    }
};
