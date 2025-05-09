<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\AtividadeSeeder;
use Database\Seeders\ContactoSeeder;
use Database\Seeders\EntidadeSeeder;
use Database\Seeders\FuncaoSeeder;
use Database\Seeders\PaisSeeder;
use Database\Seeders\TipoAtividadeSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\RoleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chamar outros seeders
        $this->call([
            RoleSeeder::class,
            TenantSeeder::class,   // Primeiro criar os tenants
            UserSeeder::class,     // Depois criar os usuários vinculados aos tenants
            PaisSeeder::class,     // Adicionar países antes das entidades
            FuncaoSeeder::class,   // Adicionar funções antes dos contactos
            TipoAtividadeSeeder::class, // Adicionar tipos de atividade antes das atividades
            EntidadeSeeder::class,
            ContactoSeeder::class,
            AtividadeSeeder::class,
        ]);
    }
}
