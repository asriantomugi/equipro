<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Histori Gangguan Layanan
     *
     * @return tabel Layanan di database
     */
    public function up(): void
    {
        Schema::create('histori_gangguan_layanan', function (Blueprint $table) {

            // membuat field-field
            $table->id();
            $table->bigInteger('layanan_id');
            $table->bigInteger('laporan_id');
            $table->datetime('waktu_unserv');
            $table->datetime('waktu_serv')->nullable();
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('histori_gangguan_layanan');
    }
};
