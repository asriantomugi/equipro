<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use App\Models\LogAktivitas;

/**
 * Helper untuk mencatat log aktivitas user ke dalam tabel log_aktivitas
 *
 * Digunakan dalam:
 * - Pencatatan log otomatis (melalui middleware)
 * - Pencatatan log manual (pemanggilan eksplisit dari controller)
 *
 * @author Faldy
 */
class LogAktivitasHelper
{
    /**
     * Mencatat log aktivitas secara manual.
     *
     * Bisa dipanggil langsung di controller seperti:
     * LogAktivitasHelper::log("Menambahkan Data Fasilitas");
     *
     * @param string $aktivitas
     * @return void
     */
    public static function log(string $aktivitas): void
    {
        if (!Auth::check()) return;

        try {
            LogAktivitas::create([
                'user_id'    => Auth::id(),
                'aktivitas'  => $aktivitas,
                'ip_address' => Request::ip(),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mencatat log aktivitas: ' . $e->getMessage());
        }
    }

    /**
     * Digunakan oleh middleware untuk mencatat log secara otomatis
     * berdasarkan request method dan path.
     *
     * Hanya mencatat jika method adalah POST/PUT/PATCH/DELETE.
     *
     * @return void
     */
    public static function logRequest(): void
    {
        $method = strtoupper(Request::method());
        $path   = strtolower(Request::path());

        // Abaikan method GET
        if (!in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) return;

        // Hindari pencatatan aktivitas dari halaman log itu sendiri
        if (str_contains($path, 'log-aktivitas')) return;

        // Format dan simpan log aktivitas
        $aktivitas = self::formatAktivitas($method, $path);
        self::log($aktivitas);
    }

    /**
     * Mengonversi method + path menjadi deskripsi aktivitas yang manusiawi.
     *
     * Contoh:
     * - Method: POST
     * - Path: master-data/user/tambah
     * - Output: "Menambahkan Data User"
     *
     * @param string $method
     * @param string $path
     * @return string
     */
    private static function formatAktivitas(string $method, string $path): string
    {
        // Hilangkan angka ID di akhir URL (misalnya: /edit/5)
        $path = preg_replace('/\/\d+$/', '', $path);

        // Pecah path menjadi bagian-bagian
        $segments = explode('/', $path);
        $segments = array_filter($segments); // hapus segmen kosong
        $segments = array_values($segments); // reindex

        // Pemetaan keyword aksi
        $aksiMap = [
            'tambah' => 'Menambahkan',
            'create' => 'Menambahkan',
            'edit'   => 'Mengubah',
            'update' => 'Mengubah',
            'hapus'  => 'Menghapus',
            'delete' => 'Menghapus',
        ];

        // Cari keyword aksi dalam segmen
        $aksi = null;
        foreach ($segments as $seg) {
            if (isset($aksiMap[$seg])) {
                $aksi = $aksiMap[$seg];
                break;
            }
        }

        // Cari entitas yang relevan
        $entitas = 'Entitas';
        if (($key = array_search('master-data', $segments)) !== false && isset($segments[$key + 1])) {
            $entitas = $segments[$key + 1];
        } elseif (isset($segments[0])) {
            $entitas = $segments[0];
        }

        // Format output akhir
        return $aksi
            ? "$aksi Data " . ucfirst(str_replace('-', ' ', $entitas))
            : "$method " . strtoupper($path);
    }
}
