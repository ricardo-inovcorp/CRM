<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar tenant de exemplo
        Tenant::create([
            'name' => 'Empresa Exemplo',
            'domain' => 'exemplo.localhost',
            'database' => 'tenant_exemplo',
            'email' => 'admin@exemplo.com',
            'telefone' => '123456789',
            'active' => true
        ]);
        
        // Criar outro tenant para demonstrar multi-tenancy
        Tenant::create([
            'name' => 'Outra Empresa',
            'domain' => 'outra.localhost',
            'database' => 'tenant_outra',
            'email' => 'admin@outraempresa.com',
            'telefone' => '987654321',
            'active' => true
        ]);
    }
}
