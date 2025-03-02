<?php

namespace App\Interfaces;

interface PostRepositoryInterface
{
    public function getAllPosts();
    public function getPostById($id);
    public function createPost(array $data);
    public function updatePost($post, array $data);
    public function deletePost($post);
    public function attachIngredient($post, $ingredientId, $quantity);
    public function detachIngredient($post, $ingredientId);
}
