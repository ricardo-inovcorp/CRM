<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->foreignId('tipo_id')->constrained('tipos_negocio');
            $table->foreignId('entidade_id')->constrained('entidades');
            $table->decimal('valor', 15, 2)->nullable();
            $table->enum('estado', ['novo', 'contactado', 'negociacao', 'proposta', 'ganho', 'perdido']);
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocios');
    }
}; 