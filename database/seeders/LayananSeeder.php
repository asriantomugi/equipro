<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LayananSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk tabel layanan.
     */
    public function run(): void
    {
        /**
         * Menambahkan data layanan default
         */
        DB::table('layanan')->insert([
           'id' => 1,
            'kode' => 'LA1',
            'nama' => 'SGS GATE 1',
            'fasilitas_id' => 1,
            'lok_tk_1_id' => 1,
            'lok_tk_2_id' => 1,
            'lok_tk_3_id' => 1,
            'kondisi' => 1,
            'status' => TRUE,
        ]);
    }
}
