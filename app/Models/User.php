<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isPemilik(): bool
    {
        return $this->role === 'pemilik';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetugasGudang(): bool
    {
        return $this->role === 'petugas_gudang';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'pemilik' => 'Pemilik',
            'admin' => 'Admin',
            'petugas_gudang' => 'Petugas Gudang',
            'kasir' => 'Kasir',
            default => ucfirst($this->role),
        };
    }
}
