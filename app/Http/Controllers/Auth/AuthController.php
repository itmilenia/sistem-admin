<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'uname' => ['required', 'string'],
            'pwd'   => ['required', 'string'],
        ]);

        // Ambil user berdasarkan uname
        $user = User::query()
            ->select('ID', 'uname', 'pwd')
            ->where('uname', $data['uname'])
            ->where('Aktif', 1)
            ->first();

        if (!$user || $data['pwd'] !== $user->pwd) {
            return back()
                ->withErrors(['uname' => 'Username atau password salah.'])
                ->onlyInput('uname');
        }


        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
