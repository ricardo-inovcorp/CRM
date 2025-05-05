<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar tenants
        $this->call(TenantSeeder::class);
        
        // Criar usuário administrador principal (landlord)
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@crm.com',
        ]);

        // Para cada tenant, executar o seeder de dados iniciais
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            // Fazer a migração do tenant
            Artisan::call('tenants:artisan', [
                'artisan_command' => 'migrate:fresh',
                '--tenant' => $tenant->id,
            ]);
            
            // Criar usuário admin para o tenant
            Artisan::call('tenants:artisan', [
                'artisan_command' => 'db:seed',
                '--class' => 'DadosIniciaisSeeder',
                '--tenant' => $tenant->id,
            ]);
            
            // Criar usuário admin para o tenant
            Artisan::call('tenants:artisan', [
                'artisan_command' => 'user:create',
                '--name' => "Admin {$tenant->name}",
                '--email' => "admin@{$tenant->domain}",
                '--password' => 'password',
                '--tenant' => $tenant->id,
            ]);
        }
    }
}
