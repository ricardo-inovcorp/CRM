<?php

namespace Database\Factories;

use App\Models\Entidade;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entidade>
 */
class EntidadeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Entidade::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->company(),
            'email' => fake()->companyEmail(),
            'telefone' => fake()->phoneNumber(),
            'morada' => fake()->address(),
            'tenant_id' => Tenant::factory(),
            'estado' => fake()->randomElement(['Ativo', 'Inativo']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 