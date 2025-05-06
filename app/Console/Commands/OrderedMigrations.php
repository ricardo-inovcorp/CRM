<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OrderedMigrations extends Command
{
    protected $signature = 'migrations:ordered';
    protected $description = 'Executa as migrações em uma ordem específica para evitar problemas de dependência';

    public function handle()
    {
        // Executar primeiro as migrações do sistema base
        $this->info('Executando migrações do sistema base...');
        Artisan::call('migrate', [
            '--path' => 'database/migrations/0001_01_01_000000_create_users_table.php',
        ]);
        $this->info(Artisan::output());
        
        Artisan::call('migrate', [
            '--path' => 'database/migrations/0001_01_01_000001_create_cache_table.php',
        ]);
        $this->info(Artisan::output());
        
        Artisan::call('migrate', [
            '--path' => 'database/migrations/0001_01_01_000002_create_jobs_table.php',
        ]);
        $this->info(Artisan::output());
        
        // Executar as migrações do landlord
        $this->info('Executando migrações do landlord...');
        Artisan::call('migrate', [
            '--path' => 'database/migrations/landlord',
        ]);
        $this->info(Artisan::output());
        
        // Executar as migrações do tenant na ordem correta
        $this->info('Executando migrações do tenant na ordem correta...');
        
        // 1. Países (não tem dependências)
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_091225_create_paises_table.php',
        ]);
        $this->info(Artisan::output());
        
        // 2. Funções (não tem dependências)
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_091253_create_funcoes_table.php',
        ]);
        $this->info(Artisan::output());
        
        // 3. Entidades (depende de países)
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_091253_create_entidades_table.php',
        ]);
        $this->info(Artisan::output());
        
        // 4. Contactos (depende de entidades e funções)
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_091253_create_contactos_table.php',
        ]);
        $this->info(Artisan::output());
        
        // 5. Tipos de atividade (não tem dependências)
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_091254_create_tipos_atividade_table.php',
        ]);
        $this->info(Artisan::output());
        
        // 6. Atividades (depende de entidades, contactos e tipos de atividade)
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_091254_create_atividades_table.php',
        ]);
        $this->info(Artisan::output());
        
        // 7. Tabelas de relacionamento (dependem de atividades)
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_091254_create_atividade_participante_table.php',
        ]);
        $this->info(Artisan::output());
        
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_091254_create_atividade_conhecimento_table.php',
        ]);
        $this->info(Artisan::output());
        
        // 8. Adicionar tenant_id à tabela users
        Artisan::call('migrate', [
            '--path' => 'database/migrations/2025_05_05_092031_add_tenant_id_to_users_table.php',
        ]);
        $this->info(Artisan::output());
        
        $this->info('Todas as migrações foram executadas com sucesso na ordem correta!');
        
        return Command::SUCCESS;
    }
} 