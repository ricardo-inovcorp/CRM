<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar usuários existentes de forma segura (sem usar truncate)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::whereNotNull('id')->delete(); // Mais seguro que truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->command->info('Criando usuários...');
        
        // Usuário Admin (sem tenant)
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'tenant_id' => null,
            'is_admin' => true,
        ]);
        
        $this->command->info('Admin global criado com sucesso!');
        
        // Obter os tenants existentes
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->command->info('Nenhum tenant encontrado. Execute TenantSeeder primeiro.');
            return;
        }
        
        // Usuário Ricardo Araujo (Tenant 1)
        if ($tenants->count() > 0) {
            User::create([
                'name' => 'Ricardo Araujo',
                'email' => 'ricardo@example.com',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants[0]->id,
                'is_admin' => false,
            ]);
            
            $this->command->info("Usuário Ricardo Araujo criado para o tenant {$tenants[0]->name}");
        }
        
        // Usuário David Mackinney (Tenant 2)
        if ($tenants->count() > 1) {
            User::create([
                'name' => 'David Mackinney',
                'email' => 'david@example.com',
                'password' => Hash::make('password'),
                'tenant_id' => $tenants[1]->id,
                'is_admin' => false,
            ]);
            
            $this->command->info("Usuário David Mackinney criado para o tenant {$tenants[1]->name}");
        }
        
        $this->command->info('Todos os usuários criados com sucesso!');
    }
} 