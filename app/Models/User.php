<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_OPERATOR = 'operator';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        'role' => 'string',
    ];

    protected $attributes = [
        'role' => self::ROLE_ADMIN,
    ];

    public function canAccessFilament(): bool
    {
        return $this->isRaiidaAdmin();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isRaiidaAdmin();
    }

    public function hasRaiidaRole(string ...$roles): bool
    {
        return $this->isRaiidaAdmin();
    }

    public function canRaiidaMutate(): bool
    {
        return $this->isRaiidaAdmin();
    }

    public function isRaiidaAdmin(): bool
    {
        return strtolower(trim((string) $this->role)) === self::ROLE_ADMIN;
    }

    public function setRoleAttribute(mixed $value): void
    {
        // Single-role mode: every account is admin.
        $this->attributes['role'] = self::ROLE_ADMIN;
    }
}
