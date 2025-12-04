<?php

namespace App\Http\Middleware;

/**
 * RoleMiddleware.php
 * Middleware ini digunakan untuk menangani pengecekan otentikasi dan role user
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
     * Function untuk memproses pengecekan otentikasi dan role user
	 * 
     * Akses:
     * - All user
     * 
	 * Method: 
     * URL: ->middleware(['role: ...'])
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek apakah status user aktif
        $status = User::find(session()->get('id'))->status;
        if($status != TRUE){
            return redirect('/logout');
        }

        // Cek role user
        $userRole = session()->get('role_id');
        
        // ambil id user dan masukkan ke variable array
        $allowedRoles = [];
        foreach ($roles as $satu) {
            switch ($satu) {
                case 'super_admin':
                    $allowedRoles[] = config('constants.role.super_admin');
                    break;
                case 'admin':
                    $allowedRoles[] = config('constants.role.admin');
                    break;
                case 'teknisi':
                    $allowedRoles[] = config('constants.role.teknisi');
                    break;
            }
        }

        // Cek apakah user memiliki akses sesuai dengan variable array
        // jika tidak, alihkan ke halaman modul
        if (!in_array($userRole, $allowedRoles)) {
            return redirect('/')->with('notif', 'tidak_diizinkan'); 
        }

        return $next($request);
    }
}