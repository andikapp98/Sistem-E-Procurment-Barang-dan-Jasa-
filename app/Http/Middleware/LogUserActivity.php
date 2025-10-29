<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        // Only log if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $duration = microtime(true) - $startTime;
            
            // Determine action based on method and route
            $action = $this->determineAction($request);
            $module = $this->determineModule($request);
            
            // Skip logging for certain routes (to avoid clutter)
            if ($this->shouldLog($request, $action, $module)) {
                try {
                    UserActivityLog::create([
                        'user_id' => $user->id,
                        'role' => $user->role,
                        'action' => $action,
                        'module' => $module,
                        'description' => $this->generateDescription($request, $action, $module),
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'request_data' => $this->filterRequestData($request),
                        'status_code' => $response->getStatusCode(),
                        'related_id' => $this->extractRelatedId($request),
                        'related_type' => $this->extractRelatedType($request),
                        'duration' => round($duration, 2),
                    ]);
                } catch (\Exception $e) {
                    // Silent fail - don't break the request if logging fails
                    \Log::error('Failed to log user activity: ' . $e->getMessage());
                }
            }
        }
        
        return $response;
    }

    /**
     * Determine action from request
     */
    private function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();
        
        // Check for specific actions in URL
        if (str_contains($path, '/login')) return 'login';
        if (str_contains($path, '/logout')) return 'logout';
        if (str_contains($path, '/approve')) return 'approve';
        if (str_contains($path, '/reject')) return 'reject';
        if (str_contains($path, '/revisi')) return 'revisi';
        if (str_contains($path, '/disposisi')) return 'disposisi';
        if (str_contains($path, '/upload')) return 'upload';
        if (str_contains($path, '/download')) return 'download';
        if (str_contains($path, '/cetak')) return 'cetak';
        if (str_contains($path, '/print')) return 'print';
        
        // Based on HTTP method
        return match($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'unknown',
        };
    }

    /**
     * Determine module from request
     */
    private function determineModule(Request $request): string
    {
        $path = $request->path();
        
        // Extract module from path
        if (str_contains($path, '/permintaan')) return 'permintaan';
        if (str_contains($path, '/disposisi')) return 'disposisi';
        if (str_contains($path, '/nota-dinas')) return 'nota_dinas';
        if (str_contains($path, '/perencanaan')) return 'perencanaan';
        if (str_contains($path, '/kso')) return 'kso';
        if (str_contains($path, '/dpp')) return 'dpp';
        if (str_contains($path, '/hps')) return 'hps';
        if (str_contains($path, '/spesifikasi-teknis')) return 'spesifikasi_teknis';
        if (str_contains($path, '/tracking')) return 'tracking';
        if (str_contains($path, '/dashboard')) return 'dashboard';
        if (str_contains($path, '/profile')) return 'profile';
        
        return 'general';
    }

    /**
     * Generate human-readable description
     */
    private function generateDescription(Request $request, string $action, string $module): string
    {
        $user = Auth::user();
        $role = $this->getRoleName($user->role);
        
        $descriptions = [
            'login' => "{$role} melakukan login",
            'logout' => "{$role} melakukan logout",
            'view' => "{$role} melihat halaman {$module}",
            'create' => "{$role} membuat data baru di {$module}",
            'update' => "{$role} mengupdate data di {$module}",
            'delete' => "{$role} menghapus data di {$module}",
            'approve' => "{$role} menyetujui {$module}",
            'reject' => "{$role} menolak {$module}",
            'revisi' => "{$role} meminta revisi {$module}",
            'disposisi' => "{$role} melakukan disposisi {$module}",
            'upload' => "{$role} mengupload file di {$module}",
            'download' => "{$role} mengunduh file dari {$module}",
            'cetak' => "{$role} mencetak dokumen {$module}",
            'print' => "{$role} mencetak dokumen {$module}",
        ];
        
        return $descriptions[$action] ?? "{$role} melakukan {$action} pada {$module}";
    }

    /**
     * Get role display name
     */
    private function getRoleName(string $role): string
    {
        $roles = [
            'admin' => 'Admin',
            'kepala_instalasi' => 'Kepala Instalasi',
            'kepala_bidang' => 'Kepala Bidang',
            'wakil_direktur' => 'Wakil Direktur',
            'direktur' => 'Direktur',
            'staff_perencanaan' => 'Staff Perencanaan',
            'kso' => 'Bagian KSO',
        ];
        
        return $roles[$role] ?? ucfirst($role);
    }

    /**
     * Filter sensitive data from request
     */
    private function filterRequestData(Request $request): ?array
    {
        $data = $request->except([
            'password',
            'password_confirmation',
            '_token',
            '_method',
        ]);
        
        // Only store if not empty and not too large
        if (empty($data) || strlen(json_encode($data)) > 10000) {
            return null;
        }
        
        return $data;
    }

    /**
     * Extract related ID from request
     */
    private function extractRelatedId(Request $request): ?int
    {
        // Try to get ID from route parameters
        $route = $request->route();
        if ($route) {
            // Handle model binding - extract ID from model
            $param = null;
            if ($route->parameter('permintaan')) {
                $param = $route->parameter('permintaan');
                if (is_object($param) && method_exists($param, 'getKey')) {
                    // Laravel Model - use getKey() method
                    return (int)$param->getKey();
                } elseif (is_object($param) && !($param instanceof \Illuminate\Database\Eloquent\Model)) {
                    // If it's an object but not a Model, return null
                    return null;
                } elseif (is_object($param)) {
                    // Check multiple possible ID fields
                    return isset($param->permintaan_id) ? (int)$param->permintaan_id : 
                           (isset($param->id) ? (int)$param->id : null);
                }
                return is_numeric($param) ? (int)$param : null;
            }
            if ($route->parameter('disposisi')) {
                $param = $route->parameter('disposisi');
                if (is_object($param) && method_exists($param, 'getKey')) {
                    return (int)$param->getKey();
                } elseif (is_object($param) && !($param instanceof \Illuminate\Database\Eloquent\Model)) {
                    return null;
                } elseif (is_object($param)) {
                    return isset($param->disposisi_id) ? (int)$param->disposisi_id : 
                           (isset($param->id) ? (int)$param->id : null);
                }
                return is_numeric($param) ? (int)$param : null;
            }
            if ($route->parameter('perencanaan')) {
                $param = $route->parameter('perencanaan');
                if (is_object($param) && method_exists($param, 'getKey')) {
                    return (int)$param->getKey();
                } elseif (is_object($param) && !($param instanceof \Illuminate\Database\Eloquent\Model)) {
                    return null;
                } elseif (is_object($param)) {
                    return isset($param->perencanaan_id) ? (int)$param->perencanaan_id : 
                           (isset($param->id) ? (int)$param->id : null);
                }
                return is_numeric($param) ? (int)$param : null;
            }
            if ($route->parameter('kso')) {
                $param = $route->parameter('kso');
                if (is_object($param) && method_exists($param, 'getKey')) {
                    return (int)$param->getKey();
                } elseif (is_object($param) && !($param instanceof \Illuminate\Database\Eloquent\Model)) {
                    return null;
                } elseif (is_object($param)) {
                    return isset($param->kso_id) ? (int)$param->kso_id : 
                           (isset($param->id) ? (int)$param->id : null);
                }
                return is_numeric($param) ? (int)$param : null;
            }
            if ($route->parameter('id')) {
                $param = $route->parameter('id');
                if (is_object($param) && method_exists($param, 'getKey')) {
                    return (int)$param->getKey();
                } elseif (is_object($param) && !($param instanceof \Illuminate\Database\Eloquent\Model)) {
                    return null;
                } elseif (is_object($param)) {
                    // Try common ID field names
                    foreach (['id', 'permintaan_id', 'disposisi_id', 'nota_id'] as $field) {
                        if (isset($param->$field)) {
                            return (int)$param->$field;
                        }
                    }
                    return null;
                }
                return is_numeric($param) ? (int)$param : null;
            }
        }
        
        return null;
    }

    /**
     * Extract related type from request
     */
    private function extractRelatedType(Request $request): ?string
    {
        $module = $this->determineModule($request);
        
        $types = [
            'permintaan' => 'App\Models\Permintaan',
            'disposisi' => 'App\Models\Disposisi',
            'nota_dinas' => 'App\Models\NotaDinas',
            'perencanaan' => 'App\Models\Perencanaan',
            'kso' => 'App\Models\Kso',
            'dpp' => 'App\Models\Perencanaan',
            'hps' => 'App\Models\Hps',
        ];
        
        return $types[$module] ?? null;
    }

    /**
     * Determine if request should be logged
     */
    private function shouldLog(Request $request, string $action, string $module): bool
    {
        $path = $request->path();
        
        // Skip logging for these patterns
        $skipPatterns = [
            'telescope',
            'horizon',
            '_ignition',
            'livewire',
            'broadcasting/auth',
        ];
        
        foreach ($skipPatterns as $pattern) {
            if (str_contains($path, $pattern)) {
                return false;
            }
        }
        
        // Skip assets and static files
        if ($request->method() === 'GET' && 
            (str_ends_with($path, '.js') || 
             str_ends_with($path, '.css') || 
             str_ends_with($path, '.png') || 
             str_ends_with($path, '.jpg') ||
             str_contains($path, '/build/'))) {
            return false;
        }
        
        return true;
    }
}
