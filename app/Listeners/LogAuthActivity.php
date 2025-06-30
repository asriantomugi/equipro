<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Helpers\LogAktivitasHelper;

/**
 * Listener LogAuthActivity
 *
 * Listener ini mencatat aktivitas login dan logout pengguna
 * secara otomatis menggunakan helper LogAktivitasHelper.
 *
 * Listener ini harus didaftarkan di EventServiceProvider:
 * 
 *     protected $listen = [
 *         Login::class => [
 *             \App\Listeners\LogAuthActivity::class . '@handleLogin',
 *         ],
 *         Logout::class => [
 *             \App\Listeners\LogAuthActivity::class . '@handleLogout',
 *         ],
 *     ];
 *
 * @author Faldy
 */
class LogAuthActivity
{
    /**
     * Menangani event login dan mencatat ke log aktivitas
     *
     * @param \Illuminate\Auth\Events\Login $event
     * @return void
     */
    public function handleLogin(Login $event): void
    {
        LogAktivitasHelper::log('Login');
    }

    /**
     * Menangani event logout dan mencatat ke log aktivitas
     *
     * @param \Illuminate\Auth\Events\Logout $event
     * @return void
     */
    public function handleLogout(Logout $event): void
    {
        LogAktivitasHelper::log('Logout');
    }
}
