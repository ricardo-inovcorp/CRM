<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CrmInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instalar o CRM com todos os componentes necessários';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando instalação do CRM...');
        
        // Executar migrações landlord
        $this->info('Executando migrações do landlord...');
        Artisan::call('migrate', ['--path' => 'database/migrations/landlord']);
        $this->line(Artisan::output());
        
        // Executar outras migrações base
        $this->info('Executando migrações base...');
        Artisan::call('migrate');
        $this->line(Artisan::output());
        
        // Executar seeders
        $this->info('Executando seeders...');
        Artisan::call('db:seed');
        $this->line(Artisan::output());
        
        $this->info('Instalação do CRM concluída com sucesso!');
        $this->info('Você pode acessar o sistema em:');
        $this->info('- http://exemplo.localhost (Empresa Exemplo)');
        $this->info('- http://outra.localhost (Outra Empresa)');
        $this->info('Login: admin@exemplo.localhost');
        $this->info('Senha: password');
        
        return Command::SUCCESS;
    }
} 