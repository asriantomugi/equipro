<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
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
        if (!$status) {
            return redirect('/logout');
        }

        // Cek role user
        $userRole = session()->get('role_id');
        
        // Convert role constants to array for checking
        $allowedRoles = [];
        foreach ($roles as $role) {
            switch ($role) {
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

        // Cek apakah user memiliki akses
        if (!in_array($userRole, $allowedRoles)) {
            return redirect('/module')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}