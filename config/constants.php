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

    // konstanta untuk kondisi peralatans
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
        'serviceable' => TRUE,
        'unserviceable' => FALSE,
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
    ] 
];