<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;

class UsersAndPostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear 10 usuarios de ejemplo
        $users = User::factory(10)->create();

        // Crear 5 posts para cada usuario
        $users->each(function ($user) {
            Post::factory(5)->create([
                'user_id' => $user->id,
                'image' => 'https://picsum.photos/640/480?random=' . rand(1, 1000), // URL válida para imágenes aleatorias
                'ingredients' => json_encode([
                    ['name' => 'Ingrediente 1', 'quantity' => '1 taza'],
                    ['name' => 'Ingrediente 2', 'quantity' => '2 cucharadas'],
                    ['name' => 'Ingrediente 3', 'quantity' => '3 piezas'],
                    ['name' => 'Ingrediente 4', 'quantity' => '500 ml'],
                    ['name' => 'Ingrediente 5', 'quantity' => '1 cucharadita'],
                ]), // Formato requerido para los ingredientes
            ]);
        });
    }
}
