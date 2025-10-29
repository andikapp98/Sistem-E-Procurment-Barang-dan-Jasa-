<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Only admin can view all logs
        if ($user->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat mengakses log aktivitas.');
        }

        $query = UserActivityLog::with('user')->latest();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('url', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = $request->get('per_page', 25);
        $logs = $query->paginate($perPage)->withQueryString();

        // Get statistics
        $stats = [
            'total_today' => UserActivityLog::whereDate('created_at', today())->count(),
            'total_week' => UserActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total_month' => UserActivityLog::whereMonth('created_at', now()->month)->count(),
            'total_all' => UserActivityLog::count(),
        ];

        // Get unique values for filters
        $roles = UserActivityLog::select('role')->distinct()->pluck('role');
        $actions = UserActivityLog::select('action')->distinct()->pluck('action');
        $modules = UserActivityLog::select('module')->distinct()->pluck('module');

        return Inertia::render('Admin/ActivityLogs/Index', [
            'logs' => $logs,
            'stats' => $stats,
            'filters' => $request->only(['role', 'action', 'module', 'start_date', 'end_date', 'search', 'per_page']),
            'roles' => $roles,
            'actions' => $actions,
            'modules' => $modules,
            'userLogin' => $user,
        ]);
    }

    /**
     * Show single log detail
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat mengakses log aktivitas.');
        }

        $log = UserActivityLog::with('user')->findOrFail($id);

        return Inertia::render('Admin/ActivityLogs/Show', [
            'log' => $log,
            'userLogin' => $user,
        ]);
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403);
        }

        $query = UserActivityLog::with('user')->latest();

        // Apply same filters as index
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->get();

        $filename = 'activity_logs_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID',
                'Tanggal/Waktu',
                'User',
                'Role',
                'Action',
                'Module',
                'Description',
                'IP Address',
                'Status',
            ]);

            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->name : 'N/A',
                    $log->role,
                    $log->action,
                    $log->module,
                    $log->description,
                    $log->ip_address,
                    $log->status_code,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete old logs (cleanup)
     */
    public function cleanup(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403);
        }

        $days = $request->get('days', 90); // Default 90 days
        
        $deleted = UserActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return back()->with('success', "Berhasil menghapus {$deleted} log lama.");
    }
}
