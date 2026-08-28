<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'username', 'password', 'role', 'sub_role', 'no_hp', 'is_active'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['is_active' => 'boolean'];

    public function isSupervisor(): bool { return $this->role === 'supervisor'; }
    public function isTeknisi(): bool { return $this->role === 'teknisi'; }
    public function isManajer(): bool { return $this->role === 'manajer'; }

    public function initials(): string
    {
        $parts = explode(' ', trim($this->name));
        $ini = strtoupper(substr($parts[0] ?? '', 0, 1) . substr(end($parts), 0, 1));
        return $ini ?: 'U';
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'supervisor' => 'supervisor.dashboard',
            'teknisi'    => 'teknisi.dashboard',
            'manajer'    => 'manajer.dashboard',
        };
    }
}
