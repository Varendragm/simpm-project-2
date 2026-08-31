<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'sub_role',
        'no_hp',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isTeknisi(): bool
    {
        return $this->role === 'teknisi';
    }

    public function isManajer(): bool
    {
        return $this->role === 'manajer';
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY);

        if (empty($parts)) {
            return 'U';
        }

        $first = mb_substr($parts[0], 0, 1);
        $last = count($parts) > 1 ? mb_substr($parts[count($parts) - 1], 0, 1) : '';

        return mb_strtoupper($first . $last);
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'supervisor' => 'supervisor.dashboard',
            'teknisi' => 'teknisi.dashboard',
            'manajer' => 'manajer.dashboard',
            default => 'login',
        };
    }
}
