<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;

class PostRepository implements PostRepositoryInterface
{
    public function getAllPosts()
    {
        return Post::with(['ingredients', 'user'])->get();
    }

    public function getPostById($id)
    {
        return Post::with(['ingredients', 'user'])->find($id);
    }

    public function createPost(array $data)
    {
        return Post::create($data);
    }

    public function updatePost($post, array $data)
    {
        $post->update($data);
        return $post;
    }

    public function deletePost($post)
    {
        return $post->delete();
    }

    public function attachIngredient($post, $ingredientId, $quantity)
    {
        $post->ingredients()->attach($ingredientId, ['quantity' => $quantity]);
        return $post->load('ingredients');
    }

    public function detachIngredient($post, $ingredientId)
    {
        $post->ingredients()->detach($ingredientId);
        return $post->load('ingredients');
    }
}
