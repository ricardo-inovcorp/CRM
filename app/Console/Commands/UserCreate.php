<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UserCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--name= : Nome do usuário} {--email= : Email do usuário} {--password= : Senha do usuário} {--tenant_id= : ID do tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar um novo usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');
        $email = $this->option('email');
        $password = $this->option('password');
        $tenant_id = $this->option('tenant_id');
        
        if (!$name) {
            $name = $this->ask('Digite o nome do usuário');
        }
        
        if (!$email) {
            $email = $this->ask('Digite o email do usuário');
        }
        
        if (!$password) {
            $password = $this->secret('Digite a senha do usuário');
        }
        
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'tenant_id' => $tenant_id,
        ]);
        
        $this->info("Usuário {$user->name} criado com sucesso!");
        
        return Command::SUCCESS;
    }
}
