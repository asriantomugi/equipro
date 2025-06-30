<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Peralatan
     *
     * @return tabel peralatan di database
     */
    public function up(): void
    {
        Schema::create('peralatan', function (Blueprint $table) {

            // membuat field-field
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->string('merk')->nullable();
            $table->string('tipe')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->integer('thn_produksi')->nullable();
            $table->integer('thn_pengadaan')->nullable();
            $table->bigInteger('jenis_id');
            $table->bigInteger('perusahaan_id');
            $table->boolean('sewa');
            $table->boolean('status')->default(true);
            $table->boolean('kondisi')->default(true);
            $table->string('keterangan')->nullable();
            $table->tinyInteger('flag_layanan')->default(0);
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
        Schema::dropIfExists('peralatan');
    }
};
