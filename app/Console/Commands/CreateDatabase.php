<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    protected $signature = 'db:create {name : Nome do banco de dados}';
    protected $description = 'Cria um novo banco de dados MySQL';

    public function handle()
    {
        $databaseName = $this->argument('name');
        
        try {
            $pdo = DB::connection()->getPdo();
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
            
            $this->info("Banco de dados '{$databaseName}' criado com sucesso!");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Erro ao criar o banco de dados: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 