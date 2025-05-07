<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class OrderedMigrations extends Command
{
    protected $signature = 'migrations:ordered {--fresh : Indica se deve dropar todas as tabelas antes}';
    protected $description = 'Executa as migrações em uma ordem específica para evitar problemas de dependência';

    public function handle()
    {
        // Verificar se deve limpar o banco
        if ($this->option('fresh')) {
            // Desativar verificações de chave estrangeira temporariamente
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Listar todas as tabelas
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::connection()->getDatabaseName();
            $tableColumn = 'Tables_in_' . $dbName;
            
            // Excluir cada tabela
            foreach ($tables as $table) {
                Schema::dropIfExists($table->$tableColumn);
                $this->info("Tabela {$table->$tableColumn} excluída com sucesso!");
            }
            
            // Reativar verificações de chave estrangeira
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            $this->info('Todas as tabelas foram removidas do banco de dados.');
            
            // Recriar a tabela de migrações
            Schema::create('migrations', function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
            });
            
            $this->info('Tabela de migrações recriada.');
        }
        
        // Executar primeiro as migrações do sistema base
        $this->info('Executando migrações do sistema base...');
        $this->runMigration('database/migrations/0001_01_01_000000_create_users_table.php');
        $this->runMigration('database/migrations/0001_01_01_000001_create_cache_table.php');
        $this->runMigration('database/migrations/0001_01_01_000002_create_jobs_table.php');
        
        // Executar a migração de tenants
        $this->info('Executando migração de tenants...');
        $this->runMigration('database/migrations/2025_05_05_091000_create_tenants_table.php');
        
        // Executar as migrações na ordem correta
        $this->info('Executando migrações na ordem correta...');
        
        // 1. Países (não tem dependências)
        $this->runMigration('database/migrations/2025_05_05_091225_create_paises_table.php');
        
        // 2. Funções (não tem dependências)
        $this->runMigration('database/migrations/2025_05_05_091253_create_funcoes_table.php');
        
        // 3. Entidades (depende de países)
        $this->runMigration('database/migrations/2025_05_05_091253_create_entidades_table.php');
        
        // 4. Contactos (depende de entidades e funções)
        $this->runMigration('database/migrations/2025_05_05_091253_create_contactos_table.php');
        
        // 5. Tipos de atividade (não tem dependências)
        $this->runMigration('database/migrations/2025_05_05_091254_create_tipos_atividade_table.php');
        
        // 6. Atividades (depende de entidades, contactos e tipos de atividade)
        $this->runMigration('database/migrations/2025_05_05_091254_create_atividades_table.php');
        
        // 7. Tabelas de relacionamento (dependem de atividades)
        $this->runMigration('database/migrations/2025_05_05_091254_create_atividade_participante_table.php');
        $this->runMigration('database/migrations/2025_05_05_091254_create_atividade_conhecimento_table.php');
        
        // 8. Adicionar tenant_id à tabela users
        $this->runMigration('database/migrations/2025_05_05_092031_add_tenant_id_to_users_table.php');
        
        $this->info('Todas as migrações foram executadas com sucesso na ordem correta!');
        
        return Command::SUCCESS;
    }
    
    protected function runMigration($path)
    {
        try {
            $this->info("Executando migração: $path");
            Artisan::call('migrate', [
                '--path' => $path,
                '--force' => true,
            ]);
            $this->info("Migração concluída com sucesso: $path");
        } catch (\Exception $e) {
            $this->error("Erro ao executar migração $path: " . $e->getMessage());
        }
    }
} 