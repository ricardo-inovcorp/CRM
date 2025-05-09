<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Renomear a coluna 'name' para 'nome'
            $table->renameColumn('name', 'nome');
            
            // Adicionar as novas colunas
            $table->string('nif')->nullable()->after('nome');
            $table->string('morada')->nullable()->after('nif');
            
            // Tornar opcionais colunas que não precisamos obrigatoriamente
            $table->string('domain')->nullable()->change();
            $table->string('database')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // Reverter as alterações
            $table->renameColumn('nome', 'name');
            $table->dropColumn(['nif', 'morada']);
            $table->string('domain')->nullable(false)->change();
            $table->string('database')->nullable(false)->change();
        });
    }
};
