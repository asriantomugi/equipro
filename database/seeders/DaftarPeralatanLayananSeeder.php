<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaftarPeralatanLayananSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('daftar_peralatan_layanan')->insert([
            'layanan_id'   => 1, // ID dari LayananSeeder
            'peralatan_id' => 1, // Pastikan ID ini ada di tabel `peralatan`
            'ip_address'   => '192.168.0.10',
            'kondisi'      => true,
            'status'       => true,
            'created_by'   => 2, // Sesuaikan dengan ID user dummy jika ada
            'updated_by'   => 2,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
}
