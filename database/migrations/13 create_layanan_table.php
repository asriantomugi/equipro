<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Function untuk membuat tabel Layanan
     *
     * @return tabel Layanan di database
     */
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {

            // membuat field-field
            $table->id();
            $table->string('kode');
            $table->string('nama');
            $table->bigInteger('fasilitas_id');
            $table->bigInteger('lok_tk_1_id');
            $table->bigInteger('lok_tk_2_id');
            $table->bigInteger('lok_tk_3_id');
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
        Schema::dropIfExists('layanan');
    }
};
