<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null, // Se asignará en el seeder
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'imagen' => $this->faker->imageUrl(640, 480, 'food', true, 'Post Image'), // Generar una URL de imagen aleatoria
            'ingredients' => json_encode($this->faker->words(5)), // Generar una lista de 5 ingredientes aleatorios
        ];
    }
}
