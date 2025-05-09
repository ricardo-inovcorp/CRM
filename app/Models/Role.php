<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nome',
        'slug',
        'descricao',
    ];
    
    /**
     * Os usuários que pertencem a esta role.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    
    /**
     * Role de Admin
     */
    public static function admin()
    {
        return static::where('slug', 'admin')->first();
    }
    
    /**
     * Role de Manager
     */
    public static function manager()
    {
        return static::where('slug', 'manager')->first();
    }
    
    /**
     * Role de Staff
     */
    public static function staff()
    {
        return static::where('slug', 'staff')->first();
    }
}
