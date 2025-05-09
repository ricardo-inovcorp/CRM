<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Role
        Role::create([
            'nome' => 'Administrador',
            'slug' => 'admin',
            'descricao' => 'Acesso completo ao sistema',
        ]);
        
        // Manager Role
        Role::create([
            'nome' => 'Gestor',
            'slug' => 'manager',
            'descricao' => 'Acesso limitado à gestão de utilizadores e recursos',
        ]);
        
        // Staff Role
        Role::create([
            'nome' => 'Funcionário',
            'slug' => 'staff',
            'descricao' => 'Acesso básico ao sistema',
        ]);
    }
}
