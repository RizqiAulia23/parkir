<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [], [
            'username' => 'username',
            'password' => 'password',
        ]);

        // Login hanya untuk akun dengan status_aktif = 1.
        $berhasil = Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'status_aktif' => 1,
        ]);

        if (! $berhasil) {
            return back()
                ->withErrors(['username' => 'Username atau password salah, atau akun Anda tidak aktif.'])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        LogAktivitas::catat(
            'Login: '.$user->username.' ('.$user->role.')',
            $user->id_user
        );

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Selamat datang, '.$user->nama_lengkap.'.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            LogAktivitas::catat(
                'Logout: '.$user->username.' ('.$user->role.')',
                $user->id_user
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }
}
