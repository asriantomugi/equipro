<?php

namespace App\Http\Controllers;

/**
 * OtentiskasiController.php
 * Controller ini digunakan untuk menangani proses login
 *
 * @author Mugi Asrianto
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class OtentikasiController extends Controller
{
    /**
     * Function untuk menampilkan halaman login.
     * URL: /login
     * 
     * @param  Request  $request
     * @return void
     */
    public function show(Request $request)
    {
    	if (Auth::check()) {
            return redirect('/');
        }

        return view('login');
    }

    /**
     * Function untuk menangani proses login.
     * URL: /login/proses
     * 
     * @param  Request  $request
     * @return void
     */
    public function process(Request $request)
    {
    	// melakukan validasi input dari form login
    	$credentials  = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

    	// melakukan verifikasi akun ke database (tabel "User")
        if(Auth::attempt($credentials)){

            // jika status user tidak aktif
            if(Auth::user()->status != TRUE){

                // user dipaksa logout
                Auth::logout();

                // session dihapus
                $request->session()->invalidate();

                // regenerate token
                $request->session()->regenerateToken();

                // alihkan ke halaman login dengan pesan error
                return redirect('/login')->with('notif', 'tidak_aktif');
            }

            // jika status user aktif, ambil role id dan role name
            $role = User::find(Auth::user()->id)->role;

            // simpan data user ke dalam session
            session([
                'id' => Auth::user()->id,
                'name' => Auth::user()->name,
                'status' => Auth::user()->status,
                'role_id' => $role->id,
                'role_name' => $role->nama
            ]);

            // generate session
            $request->session()->regenerate();

        	// alihkan ke url "/"
        	return redirect('/');
        }

        // jika verifikasi gagal, maka dialihkan ke halaman login dengan pesan error
        return redirect('/login')->with('notif', 'tidak_sesuai'); 
    }

	/**
	 * Function untuk menangani proses logout.
     * URL: /logout
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request)
	{
		// melakukan proses logout
	    Auth::logout();
	 
	    $request->session()->invalidate();
	 
	    $request->session()->regenerateToken();
	 
	 	// mengalihkan ke url "/"
	    return redirect('/');
	}
}
