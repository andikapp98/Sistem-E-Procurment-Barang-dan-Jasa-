<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Use default primary key 'id'
    // protected $primaryKey = 'id'; // Default, no need to specify

    // Allow mass assignment for these columns
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'jabatan',
        'unit_kerja',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get all permintaan for this user (Kepala Instalasi)
     */
    public function permintaan()
    {
        return $this->hasMany(Permintaan::class, 'user_id', 'id');
    }
}
