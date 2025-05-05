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
        Schema::create('atividades', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->time('hora');
            $table->integer('duracao')->nullable();
            $table->foreignId('entidade_id')->constrained('entidades');
            $table->foreignId('contacto_id')->nullable()->constrained('contactos');
            $table->foreignId('tipo_id')->constrained('tipos_atividade');
            $table->text('descricao')->nullable();
            $table->foreignId('tenant_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atividades');
    }
};
