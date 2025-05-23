<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'tenant_id',
        'is_admin',
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
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Os papéis (roles) deste usuário.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * O tenant (empresa) ao qual este usuário pertence.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Verificar se o usuário tem uma determinada role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Verificar se o usuário é Admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Verificar se o usuário é Manager.
     */
    public function isManager(): bool
    {
        return $this->hasRole('manager') || $this->isAdmin();
    }

    /**
     * Verificar se o usuário é Staff.
     */
    public function isStaff(): bool
    {
        return $this->hasRole('staff') || $this->isManager();
    }

    public function atividadesParticipante(): BelongsToMany
    {
        return $this->belongsToMany(Atividade::class, 'atividade_participante')
                    ->withTimestamps();
    }

    public function atividadesConhecimento(): BelongsToMany
    {
        return $this->belongsToMany(Atividade::class, 'atividade_conhecimento')
                    ->withTimestamps();
    }
}
