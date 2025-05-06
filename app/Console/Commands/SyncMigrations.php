<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SyncMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca todas as migrações como executadas sem realmente executá-las';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrationFiles = File::glob(database_path('migrations/*.php'));
        $landlordMigrationFiles = File::glob(database_path('migrations/landlord/*.php'));
        $allMigrationFiles = array_merge($migrationFiles, $landlordMigrationFiles);
        
        $batch = DB::table('migrations')->max('batch') ?? 0;
        $batch++;
        
        $count = 0;
        foreach ($allMigrationFiles as $file) {
            $migrationName = basename($file, '.php');
            
            // Verifica se a migração já foi executada
            $exists = DB::table('migrations')
                ->where('migration', $migrationName)
                ->exists();
                
            if (!$exists) {
                // Adiciona a migração à tabela
                DB::table('migrations')->insert([
                    'migration' => $migrationName,
                    'batch' => $batch
                ]);
                
                $this->info("Migração {$migrationName} marcada como executada.");
                $count++;
            }
        }
        
        if ($count > 0) {
            $this->info("{$count} migração(ões) sincronizada(s) com sucesso.");
        } else {
            $this->info("Todas as migrações já estão sincronizadas.");
        }
        
        return Command::SUCCESS;
    }
}
