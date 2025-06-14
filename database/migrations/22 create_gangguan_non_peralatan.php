<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Gangguan non peralatan
     *
     * @return tabel Gangguan non peralatan di database
     */
    public function up(): void
    {
        Schema::create('gangguan_non_peralatan', function (Blueprint $table) {

            // membuat field-field
            $table->bigIncrements('id');
            $table->bigInteger('laporan_id');
            $table->bigInteger('layanan_id');
            $table->string('deskripsi');
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();

            // foreign key:
            // $table->foreign('laporan_id')->references('id')->on('laporan')->onDelete('cascade');
            // $table->foreign('layanan_id')->references('id')->on('layanan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gangguan_non_peralatan');
    }
};
