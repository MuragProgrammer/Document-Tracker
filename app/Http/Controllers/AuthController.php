<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('username', trim($request->username))->first();

        if (!$user) {
            return redirect()
                ->back()
                ->with('error', 'Username Not Found');
        }

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password_hash)) {
            return redirect()
                ->back()
                ->with('error', 'Wrong Password');
        }

        if ($user->is_active == 0) {
            return redirect()
                ->back()
                ->with('error', 'Account Disabled. Please contact the administrator.');
        }

        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard.index')
            ->with('success', 'Login successful.');
    }




    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}
