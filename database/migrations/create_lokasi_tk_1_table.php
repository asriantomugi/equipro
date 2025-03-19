<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Lokasi Tingkat I
     *
     * @return tabel lokasi_tk_1 di database
     */
    public function up(): void
    {
        Schema::create('lokasi_tk_1', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('nama');
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
        Schema::dropIfExists('lokasi_tk_1');
    }
};
