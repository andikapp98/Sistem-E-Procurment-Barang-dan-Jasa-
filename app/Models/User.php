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

    // Use the existing primary key in your DB
    protected $primaryKey = 'user_id';

    // Allow mass assignment for these columns (includes the existing 'nama')
    protected $fillable = [
        'nama',
        'name', // virtual, maps to nama
        'email',
        'password',
        'role',
        'jabatan',
        'unit_kerja',
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
        //'password' => 'hashed', // leave hashing to controller when setting
    ];

    /**
     * Provide a `name` attribute that maps to the `nama` column in the DB.
     */
    public function getNameAttribute()
    {
        return $this->attributes['nama'] ?? $this->attributes['name'] ?? null;
    }

    public function setNameAttribute($value)
    {
        // store under the existing `nama` column so legacy rows remain consistent
        $this->attributes['nama'] = $value;
    }
}
