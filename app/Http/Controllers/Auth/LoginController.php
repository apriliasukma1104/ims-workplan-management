<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $this->validate($request, [
            'name' => ['required'],
            'password' => ['required'],
        ]);

        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Memeriksa peran pengguna saat login
            $user = Auth::user();

            if ($user->members_status === '1') {
                // Jika members_status='1', maka bisa login
                if ($user->role === 'Admin') {
                    return Inertia::location('/members/list_members');
                } elseif ($user->role === 'Kabag') {
                    return Inertia::location('/workplans/list_workplans');
                } elseif ($user->role === 'Kadep' || $user->role === 'Staf') {
                    return Inertia::location('/dashboard');
                }
            } else {
                // Jika members_status='0', maka gagal login
                Auth::logout(); // Logout pengguna yang tidak aktif
                return back()->withErrors([
                    'error' => 'Your account is inactive. Please contact the administrator.',
                ]);
            }
        }

        return back()->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
