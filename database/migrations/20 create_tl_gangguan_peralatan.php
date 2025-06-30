<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel TL Gangguan Peralatan
     *
     * @return tabel TL Gangguan Peralatan di database
     */
    public function up(): void
    {
        Schema::create('tl_gangguan_peralatan', function (Blueprint $table) {

            // membuat field-field
            $table->bigIncrements('id');
            $table->bigInteger('gangguan_peralatan_id');
            $table->bigInteger('laporan_id');
            $table->bigInteger('layanan_id');
            $table->bigInteger('peralatan_id');
            $table->dateTime('waktu');
            $table->string('deskripsi');
            $table->boolean('kondisi');
            $table->boolean('jenis_tindaklanjut');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();

            // foreign key:
            // $table->foreign('gangguan_peralatan_id')->references('id')->on('gangguan_peralatan')->onDelete('cascade');
            // $table->foreign('laporan_id')->references('id')->on('laporan')->onDelete('cascade');
            // $table->foreign('layanan_id')->references('id')->on('layanan')->onDelete('cascade');
            // $table->foreign('peralatan_id')->references('id')->on('peralatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tl_gangguan_peralatan');
    }
};
