<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Members;
class LoginController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $this->validate($request, [
            'nip' => ['required'],
            'password' => ['required'],
        ]);

        $members = Members::where('nip', $request->nip)->first();

        if (!$members) {
            return back()->withErrors([
                'error' => 'We could not find an account with that username. Please contact Administrator (IT Help Desk: 081334342992).',
            ]);
        } elseif ($members->members_status === '0') {
            return back()->withErrors([
                'error' => 'Your account is inactive. Please contact Administrator (IT Help Desk: 081334342992).',
            ]);
        } elseif (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'error' => 'The password provided does not match our records. Please contact Administrator (IT Help Desk: 081334342992).',
            ]);
        } else {
            return Inertia::location('/dashboard'); 
        }

        return back()->withErrors([
            'error' => 'Unknown error occurred. Please contact Administrator (IT Help Desk: 081334342992).',
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
