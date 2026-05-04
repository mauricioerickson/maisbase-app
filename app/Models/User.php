<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, BelongsToTenant;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_FINANCIAL = 'financeiro';
    public const ROLE_STAFF = 'equipe';

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Verifica se o usuário possui um dos papéis informados.
     */
    public function hasRole(array|string $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
