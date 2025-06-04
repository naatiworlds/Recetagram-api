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
                'imagen' => 'https://via.placeholder.com/640x480.png?text=Example+Image',
                'ingredients' => json_encode(['flour', 'sugar', 'eggs', 'milk', 'butter']),
            ]);
        });
    }
}
