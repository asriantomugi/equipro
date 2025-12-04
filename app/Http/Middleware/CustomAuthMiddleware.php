<?php

namespace App\Http\Middleware;

/**
 * CustomAuthMiddleware.php
 * Middleware ini digunakan untuk menangani pengecekan
 * apakah user sudah login dan statusnya aktif
 * sebelum mengakses Controller tertentu.
 * Middleware ini digunakan pada file routes/web.php
 *
 * @author Mugi Asrianto
 */

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RoleMiddleware
{
    /**
     * Function untuk memproses pengecekan login user dan status user
	 * 
     * Akses:
     * - All user
     * 
	 * Method: 
     * URL: ->middleware(['auth'])
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek apakah status user aktif
        $status = User::find(session()->get('id'))->status;
        if (!$status) {
            return redirect('/logout');
        }

        return $next($request);
    }
}