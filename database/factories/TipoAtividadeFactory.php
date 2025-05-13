<?php

namespace Database\Factories;

use App\Models\TipoAtividade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TipoAtividade>
 */
class TipoAtividadeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TipoAtividade::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->word(),
            'descricao' => fake()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 