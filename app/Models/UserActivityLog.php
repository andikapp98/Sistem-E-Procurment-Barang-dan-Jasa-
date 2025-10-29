<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'action',
        'module',
        'description',
        'url',
        'method',
        'ip_address',
        'user_agent',
        'request_data',
        'response_data',
        'status_code',
        'related_id',
        'related_type',
        'duration',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'duration' => 'float',
    ];

    /**
     * Relationship dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk filter by role
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope untuk filter by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter by module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope untuk filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Log activity helper
     */
    public static function log($data)
    {
        return self::create($data);
    }
}
