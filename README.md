# Aplicação CRM - Laravel + Vue

Um sistema CRM (Customer Relationship Management) simples e eficiente, desenvolvido com Laravel 12 e Vue.js, utilizando Tailwind CSS para estilização.

## Características

- **Multi-tenant**: Múltiplas empresas na mesma instância
- **Entidades**: Cadastro e gestão de empresas
- **Contactos**: Gestão de contatos relacionados às entidades
- **Atividades**: Registro de interações como reuniões, chamadas, emails
- **Relatórios**: Análise de dados e produtividade
- **Layout Responsivo**: Interface adaptada para dispositivos móveis e desktop

## Tecnologias

- **Backend**: Laravel 12
- **Frontend**: Vue.js 3 + Tailwind CSS
- **Database**: MySQL
- **Multi-tenant**: Spatie Laravel Multitenancy

## Requisitos

- PHP 8.2 ou superior
- Composer
- Node.js e npm
- MySQL

## Instalação

1. Clone o repositório:
   ```bash
   git clone [url-do-repositorio]
   cd CRM
   ```

2. Instale as dependências PHP:
   ```bash
   composer install
   ```

3. Instale as dependências JavaScript:
   ```bash
   npm install
   ```

4. Crie um arquivo `.env` a partir do exemplo:
   ```bash
   cp .env.example .env
   ```

5. Configure o banco de dados no arquivo `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crm
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```

6. Gere a chave da aplicação:
   ```bash
   php artisan key:generate
   ```

7. Adicione ao seu arquivo de hosts (/etc/hosts) as seguintes entradas:
   ```
   127.0.0.1  exemplo.localhost
   127.0.0.1  outra.localhost
   ```

8. Execute o comando de instalação do CRM:
   ```bash
   php artisan crm:install
   ```

9. Compile os assets:
   ```bash
   npm run dev
   ```

10. Inicie o servidor:
    ```bash
    php artisan serve
    ```

11. Acesse a aplicação em:
    - `http://exemplo.localhost:8000` (Empresa Exemplo)
    - `http://outra.localhost:8000` (Outra Empresa)

    Credenciais:
    - Email: admin@exemplo.localhost
    - Senha: password

## Estrutura de Dados

- **Entidades**: Empresas ou organizações
- **Contactos**: Pessoas vinculadas às entidades
- **Atividades**: Interações com entidades ou contactos
- **Configurações**: Países, funções, tipos de atividade

## Desenvolvimento

- **Migrações**: Definições de tabelas em `database/migrations/`
- **Modelos**: Classes Eloquent em `app/Models/`
- **Controllers**: Lógica das rotas em `app/Http/Controllers/`
- **Views**: Componentes Vue em `resources/js/pages/`

Para adicionar um novo tenant:

```bash
php artisan tinker
$tenant = \App\Models\Tenant::create(['name' => 'Nova Empresa', 'domain' => 'nova.localhost', 'database' => 'tenant_nova']);
```

## Licença

Este projeto está licenciado sob a licença MIT.

## Autor

Desenvolvido para estágio em desenvolvimento web. 