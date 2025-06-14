<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Laporan
     *
     * @return tabel Laporan di database
     */
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {

            // membuat field-field
            $table->bigIncrements('id');
            $table->bigInteger('no_laporan')->unique();
            $table->bigInteger('layanan_id');
            $table->integer('jenis');
            $table->dateTime('waktu');
            $table->integer('status');
            $table->boolean('kondisi_layanan_temp');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();

            // $table->foreign('layanan_id')->references('id')->on('layanan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
