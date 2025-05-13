<?php

namespace Database\Factories;

use App\Models\Atividade;
use App\Models\Entidade;
use App\Models\Contacto;
use App\Models\TipoAtividade;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Atividade>
 */
class AtividadeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Atividade::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entidade = Entidade::factory()->create();

        return [
            'data' => fake()->date(),
            'hora' => fake()->time(),
            'duracao' => fake()->numberBetween(15, 180),
            'entidade_id' => $entidade->id,
            'contacto_id' => Contacto::factory()->create(['entidade_id' => $entidade->id])->id,
            'tipo_id' => TipoAtividade::factory(),
            'descricao' => fake()->sentence(),
            'tenant_id' => $entidade->tenant_id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 