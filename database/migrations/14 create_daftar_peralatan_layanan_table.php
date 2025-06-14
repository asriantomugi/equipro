<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Daftar peralatan Layanan
     *
     * @return tabel Daftar peralatan Layanan di database
     */
    public function up(): void
    {
        Schema::create('daftar_peralatan_layanan', function (Blueprint $table) {

            // membuat field-field
            $table->id();
            $table->bigInteger('layanan_id');
            $table->bigInteger('peralatan_id');
            $table->string('ip_address');
            $table->boolean('kondisi')->default(true);
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
        Schema::dropIfExists('daftar_peralatan_layanan');
    }
};
