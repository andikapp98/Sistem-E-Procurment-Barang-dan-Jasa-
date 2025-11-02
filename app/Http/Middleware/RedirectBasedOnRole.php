<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     * 
     * Redirect users to their appropriate dashboard based on role
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only redirect if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Admin can access all routes without redirection
            if ($user->role === 'admin') {
                return $next($request);
            }
            
            // If accessing generic /dashboard, redirect based on role
            if ($request->is('dashboard')) {
                switch($user->role) {
                    case 'kepala_instalasi':
                        return redirect()->route('kepala-instalasi.dashboard');
                    case 'kepala_ruang':
                        return redirect()->route('kepala-ruang.dashboard');
                    case 'kepala_poli':
                        return redirect()->route('kepala-poli.dashboard');
                    case 'kepala_bidang':
                        return redirect()->route('kepala-bidang.dashboard');
                    case 'wakil_direktur':
                        return redirect()->route('wakil-direktur.dashboard');
                    case 'direktur':
                        return redirect()->route('direktur.dashboard');
                    case 'staff_perencanaan':
                        return redirect()->route('staff-perencanaan.dashboard');
                    case 'kso':
                        return redirect()->route('kso.dashboard');
                    case 'pengadaan':
                        return redirect()->route('pengadaan.dashboard');
                    default:
                        return $next($request);
                }
            }
            
            // If accessing generic /permintaan, redirect based on role
            if ($request->is('permintaan') && !$request->is('permintaan/*')) {
                switch($user->role) {
                    case 'kepala_instalasi':
                        return redirect()->route('kepala-instalasi.index');
                    case 'kepala_ruang':
                        return redirect()->route('kepala-ruang.index');
                    case 'kepala_poli':
                        return redirect()->route('kepala-poli.index');
                    case 'kepala_bidang':
                        return redirect()->route('kepala-bidang.index');
                    case 'wakil_direktur':
                        return redirect()->route('wakil-direktur.index');
                    case 'direktur':
                        return redirect()->route('direktur.index');
                    case 'staff_perencanaan':
                        return redirect()->route('staff-perencanaan.index');
                    case 'kso':
                        return redirect()->route('kso.index');
                    case 'pengadaan':
                        return redirect()->route('pengadaan.index');
                    default:
                        return $next($request);
                }
            }
        }
        
        return $next($request);
    }
}
