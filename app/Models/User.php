<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function allPermissions(): Collection
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return $this->roles()->whereIn('slug', $roles)->exists();
    }

    public function hasAnyPermission(string|array $permissions): bool
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        $userPermissions = $this->allPermissions()->pluck('slug');

        return collect($permissions)->some(fn (string $permission) => $userPermissions->contains($permission));
    }

    public function hasPermission(string|array $permissions): bool
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];
        $userPermissions = $this->allPermissions()->pluck('slug');

        return collect($permissions)->every(fn (string $permission) => $userPermissions->contains($permission));
    }
}
