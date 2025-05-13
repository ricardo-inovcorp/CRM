<?php

namespace Database\Factories;

use App\Models\Contacto;
use App\Models\Entidade;
use App\Models\Funcao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contacto>
 */
class ContactoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contacto::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->firstName(),
            'apelido' => fake()->lastName(),
            'email' => fake()->email(),
            'telefone' => fake()->phoneNumber(),
            'entidade_id' => Entidade::factory(),
            'funcao_id' => null,
            'estado' => fake()->randomElement(['Ativo', 'Inativo']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 