<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Helpers\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            // Log successful login
            $user = Auth::user();
            
            if ($user) {
                ActivityLogger::logLogin($user);

                // Redirect berdasarkan role user
                if ($user->role === 'kepala_instalasi') {
                    return redirect()->intended(route('kepala-instalasi.dashboard'));
                }
                
                if ($user->role === 'kepala_bidang') {
                    return redirect()->intended(route('kepala-bidang.dashboard'));
                }
                
                if ($user->role === 'kepala_poli') {
                    return redirect()->intended(route('kepala-poli.dashboard'));
                }
                
                if ($user->role === 'kepala_ruang') {
                    return redirect()->intended(route('kepala-ruang.dashboard'));
                }
                
                if ($user->role === 'direktur') {
                    return redirect()->intended(route('direktur.dashboard'));
                }
                
                if ($user->role === 'wakil_direktur') {
                    return redirect()->intended(route('wakil-direktur.dashboard'));
                }
                
                if ($user->role === 'staff_perencanaan') {
                    return redirect()->intended(route('staff-perencanaan.dashboard'));
                }
                
                if ($user->role === 'pengadaan') {
                    return redirect()->intended(route('pengadaan.dashboard'));
                }
                
                if ($user->role === 'kso') {
                    return redirect()->intended(route('kso.dashboard'));
                }

                return redirect()->intended(route('dashboard'));
            }
            
            // Fallback jika user tidak ditemukan
            Auth::logout();
            return back()->withErrors([
                'email' => 'Terjadi kesalahan. Silakan coba lagi.',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'Terjadi kesalahan saat login. Silakan coba lagi.',
            ])->withInput($request->only('email', 'remember'));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log logout before destroying session
        $user = Auth::user();
        if ($user) {
            ActivityLogger::logLogout($user);
        }
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
