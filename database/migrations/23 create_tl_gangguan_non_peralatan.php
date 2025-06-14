<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Tl Gangguan non peralatan
     *
     * @return tabel Tl Gangguan non peralatan di database
     */
    public function up(): void
    {
        Schema::create('tl_gangguan_non_peralatan', function (Blueprint $table) {

            // membuat field-field
            $table->bigIncrements('id');
            $table->bigInteger('gangguan_non_peralatan_id');
            $table->bigInteger('laporan_id');
            $table->bigInteger('layanan_id');
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('deskripsi');
            $table->boolean('kondisi');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();

            // foreign keys
            // $table->foreign('gangguan_non_peralatan_id')->references('id')->on('gangguan_non_peralatan')->onDelete('cascade');
            // $table->foreign('laporan_id')->references('id')->on('laporan')->onDelete('cascade');
            // $table->foreign('layanan_id')->references('id')->on('layanan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tl_gangguan_non_peralatan');
    }
};
