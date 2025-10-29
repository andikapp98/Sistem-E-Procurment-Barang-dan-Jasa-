<?php

namespace App\Helpers;

use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    /**
     * Log user login
     */
    public static function logLogin($user)
    {
        return UserActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action' => 'login',
            'module' => 'auth',
            'description' => self::getRoleName($user->role) . ' berhasil login',
            'url' => request()->fullUrl(),
            'method' => 'POST',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status_code' => 200,
        ]);
    }

    /**
     * Log user logout
     */
    public static function logLogout($user)
    {
        return UserActivityLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action' => 'logout',
            'module' => 'auth',
            'description' => self::getRoleName($user->role) . ' melakukan logout',
            'url' => request()->fullUrl(),
            'method' => 'POST',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status_code' => 200,
        ]);
    }

    /**
     * Log custom activity
     */
    public static function log($action, $module, $description, $relatedId = null, $relatedType = null, $additionalData = [])
    {
        $user = Auth::user();
        
        if (!$user) return null;

        return UserActivityLog::create(array_merge([
            'user_id' => $user->id,
            'role' => $user->role,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'status_code' => 200,
        ], $additionalData));
    }

    /**
     * Get role display name
     */
    private static function getRoleName(string $role): string
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
     * Log approval action
     */
    public static function logApproval($module, $relatedId, $description)
    {
        return self::log('approve', $module, $description, $relatedId, "App\\Models\\" . ucfirst($module));
    }

    /**
     * Log rejection action
     */
    public static function logRejection($module, $relatedId, $description)
    {
        return self::log('reject', $module, $description, $relatedId, "App\\Models\\" . ucfirst($module));
    }

    /**
     * Log revision request
     */
    public static function logRevision($module, $relatedId, $description)
    {
        return self::log('revisi', $module, $description, $relatedId, "App\\Models\\" . ucfirst($module));
    }

    /**
     * Log create action
     */
    public static function logCreate($module, $relatedId, $description)
    {
        return self::log('create', $module, $description, $relatedId, "App\\Models\\" . ucfirst($module));
    }

    /**
     * Log update action
     */
    public static function logUpdate($module, $relatedId, $description)
    {
        return self::log('update', $module, $description, $relatedId, "App\\Models\\" . ucfirst($module));
    }

    /**
     * Log delete action
     */
    public static function logDelete($module, $relatedId, $description)
    {
        return self::log('delete', $module, $description, $relatedId, "App\\Models\\" . ucfirst($module));
    }

    /**
     * Log view/access action
     */
    public static function logView($module, $relatedId = null, $description = null)
    {
        $user = Auth::user();
        if (!$description) {
            $description = self::getRoleName($user->role) . " mengakses halaman {$module}";
        }
        return self::log('view', $module, $description, $relatedId, $relatedId ? "App\\Models\\" . ucfirst($module) : null);
    }

    /**
     * Log file upload
     */
    public static function logUpload($module, $filename, $relatedId = null)
    {
        $user = Auth::user();
        $description = self::getRoleName($user->role) . " mengupload file: {$filename}";
        return self::log('upload', $module, $description, $relatedId, $relatedId ? "App\\Models\\" . ucfirst($module) : null);
    }

    /**
     * Log file download
     */
    public static function logDownload($module, $filename, $relatedId = null)
    {
        $user = Auth::user();
        $description = self::getRoleName($user->role) . " mengunduh file: {$filename}";
        return self::log('download', $module, $description, $relatedId, $relatedId ? "App\\Models\\" . ucfirst($module) : null);
    }

    /**
     * Log print/cetak action
     */
    public static function logPrint($module, $relatedId, $description)
    {
        return self::log('cetak', $module, $description, $relatedId, "App\\Models\\" . ucfirst($module));
    }
}
