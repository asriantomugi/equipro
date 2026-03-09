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
        'normal' => 1, // NORMAL
        'normal_sebagian' => 2, // NORMAL SEBAGIAN
        'rusak' => 0, // RUSAK
    ],

    // konstanta untuk status histori gangguan peralatan
    'status_histori_gangguan_peralatan' => [
        'close' => 1, // TRUE
        'open' => 0, // FALSE
    ],

    // konstanta untuk kondisi layanan
    'kondisi_layanan' => [
        'serviceable' => 1, // TRUE
        'unserviceable' => 0, // FALSE
    ],

    // konstanta untuk status layanan
    'status_layanan' => [
        'tidak_aktif' => 0, // TIDAK AKTIF
        'aktif' => 1, // AKTIF
        'draft' => 2, // DRAFT
    ],

    // konstanta untuk status histori gangguan layanan
    'status_histori_gangguan_layanan' => [
        'close' => 1, // TRUE
        'open' => 0, // FALSE
    ],

    // konstanta untuk jenis laporan
    'jenis_laporan' => [
        'gangguan_peralatan' => 1,
        'gangguan_non_peralatan' => 2,
    ],

    // konstanta untuk jenis tindaklanjut gangguan peralatan
    'jenis_tindaklanjut_gangguan_peralatan' => [
        'perbaikan' => 1,
        'penggantian' => 2,
    ],

    // konstanta untuk jenis tindaklanjut gangguan non peralatan
    'jenis_tindaklanjut_gangguan_non_peralatan' => [
        'perbaikan' => 1,
    ],

    //konstanta untuk status laporan
    'status_laporan' => [
        'open'   => 0,
        'close' => 1,
        'draft'  => 2,
    ],
];