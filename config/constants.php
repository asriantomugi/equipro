<?php

return [

    /*
    |*********************************************
    |------------- KONSTANTA VARIABEL ------------
    |*********************************************
    |
    | Parameter untuk mengatur nilai variabel yang tetap.
    |
    */
    
    // konstanta untuk id role user
    'role' => [
        'sistem' => 1, // Sistem
        'super_admin' => 2, // Super Admin
        'admin' => 3, // Admin
        'teknisi' => 4, // Teknisi
    ],

    // konstanta untuk kondisi peralatan
    'kondisi_peralatan' => [
        'normal' => TRUE,
        'rusak' => FALSE,
    ],

    // konstanta untuk status histori gangguan peralatan
    'status_histori_gangguan_peralatan' => [
        'close' => TRUE,
        'open' => FALSE,
    ],

    // konstanta untuk kondisi layanan
    'kondisi_layanan' => [
        'Serviceable' => TRUE,
        'Unserviceable' => FALSE,
    ],

    // konstanta untuk status layanan
    'status_layanan' => [
        'tidak_aktif' => 0,
        'aktif' => 1,
        'draft' => 2,
    ],

    // konstanta untuk kondisi peralatan di layanan
    'kondisi_peralatan_layanan' => [
        'beroperasi' => TRUE,
        'gangguan' => FALSE,
    ],

    // konstanta untuk status histori gangguan layanan
    'status_histori_gangguan_layanan' => [
        'close' => TRUE,
        'open' => FALSE,
    ],

    // konstanta untuk jenis laporan
    'jenis_laporan' => [
        'gangguan_peralatan' => TRUE,
        'gangguan_non_peralatan' => FALSE,
    ],

    // konstanta untuk kondisi gangguan peralatan 
    'kondisi_gangguan_peralatan' => [
        'beroperasi' => '1',
        'gangguan' => '0',
    ],

    // konstanta untuk kondisi tindaklanjut
    'kondisi_tindaklanjut' => [
        'beroperasi' => TRUE,
        'gangguan' => FALSE,
    ],

    // konstanta untuk kondisi tindaklanjut
    'jenis_tindaklanjut' => [
        'perbaikan' => TRUE,
        'penggantian' => FALSE,
    ],

    //konstanta untuk status laporan
    'status_laporan' => [
        'draft'  => 1,
        'open'   => 2,
        'closed' => 3,
    ],
];