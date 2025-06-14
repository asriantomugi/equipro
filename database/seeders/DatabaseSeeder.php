<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Function untuk menambahkan data default ketika aplikasi pertama kali
     * di deployment
     * 
     * @author Mugi Asrianto
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

        /**
         * Menambahkan data role Sistem
         */
        DB::table('roles')->insert([
            'id' => 1,
            'nama' => 'SISTEM',
        ]);

        /**
         * Menambahkan data role Super Admin
         */
        DB::table('roles')->insert([
            'id' => 2,
            'nama' => 'SUPER ADMIN',
        ]);

        /**
         * Menambahkan data role Admin
         */
        DB::table('roles')->insert([
            'id' => 3,
            'nama' => 'ADMIN',
        ]);

        /**
         * Menambahkan data role Teknisi
         */
        DB::table('roles')->insert([
            'id' => 4,
            'nama' => 'TEKNISI',
        ]);

        /**
         * Menambahkan data perusahaan default
         */
        DB::table('perusahaan')->insert([
            'id' => 1,
            'nama' => 'PT. ANGKASA PURA INDONESIA',
            'email' => 'dps.ph@ap1.co.id',
            'alamat' => 'KANTOR CABANG BANDAR UDARA I GUSTI NGURAH RAI BALI',
            'telepon' => '03619351011',
            'status' => TRUE,
        ]);
        
        /**
         * Menambahkan data user default Super Admin
         */
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'SUPER ADMIN',
            'email' => 'super.admin@mail.com',
            'password' => Hash::make('superadmin'),
            'status' => TRUE,
            'role_id' => 2, // role sebagai Super Admin
        ]);
        
        /**
         * Menambahkan data detail user super admin
         */
        DB::table('detail_users')->insert([
           'id' => 1,
            'user_id' => 1,
            'perusahaan_id' => 1,
            'alamat' => 'KANTOR CABANG BANDAR UDARA I GUSTI NGURAH RAI BALI',
            'telepon' => '',
            'jabatan' => '',
        ]);

        
    }
}
