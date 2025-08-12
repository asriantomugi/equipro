<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogAktivitasHelper;

/**
 * Middleware untuk mencatat log aktivitas user yang sedang login.
 *
 * Hanya mencatat method yang bersifat mutasi (POST, PUT, PATCH, DELETE),
 * dan menghindari pencatatan URL tertentu seperti login, logout, dan log aktivitas itu sendiri.
 *
 * Dipasang di middleware group web (biasanya di Kernel.php).
 *
 * @author Faldy
 */
class LogAktivitasMiddleware
{
    /**
     * Handle setiap request masuk dan mencatat aktivitas jika memenuhi syarat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        // Lanjutkan request ke tahap selanjutnya (controller, dsb.)
        $response = $next($request);

        // Jika user sudah login
        if (Auth::check()) {
            $method = strtoupper($request->method());
            $path   = strtolower($request->path());

            // URL yang dikecualikan dari pencatatan log
            $excludedPaths = [
                'login',
                'login/proses',
                'logout',
                'log-aktivitas',
                'json/',
                'fasilitas/layanan/filter',
                'logbook/laporan/filter',
                'fasilitas/layanan/peralatan/filter',
            ];

            // Jangan catat jika path-nya mengandung salah satu yang dikecualikan
            foreach ($excludedPaths as $excluded) {
                if (str_contains($path, $excluded)) {
                    return $response;
                }
            }

            // Catat hanya jika metode termasuk perubahan data
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                LogAktivitasHelper::logRequest();
            }
        }

        // Kembalikan response ke browser
        return $response;
    }
}
