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
        'rusak' => FALSE
    ],

    // konstanta untuk status histori gangguan peralatan
    'status_histori_gangguan_peralatan' => [
        'close' => TRUE,
        'open' => FALSE
    ]


    
];