<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel TL Penggantian Peralatan
     *
     * @return tabel TL Penggantian Peralatan di database
     */
    public function up(): void
    {
        Schema::create('tl_penggantian_peralatan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tl_gangguan_id');
            $table->bigInteger('laporan_id');
            $table->bigInteger('layanan_id');
            $table->bigInteger('peralatan_lama_id');
            $table->bigInteger('peralatan_baru_id');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();

            // foreign key
            // $table->foreign('tl_gangguan_id')->references('id')->on('tl_gangguan_peralatan')->onDelete('cascade');
            // $table->foreign('laporan_id')->references('id')->on('laporan')->onDelete('cascade');
            // $table->foreign('layanan_id')->references('id')->on('layanan')->onDelete('cascade');
            // $table->foreign('peralatan_lama_id')->references('id')->on('peralatan')->onDelete('cascade');
            // $table->foreign('peralatan_baru_id')->references('id')->on('peralatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tl_penggantian_peralatan');
    }
};
