<?php

namespace App\Interfaces;

interface IngredientRepositoryInterface
{
    public function getAllIngredients();
    public function getIngredientById($id);
    public function createIngredient(array $data);
    public function updateIngredient($id, array $data);
    public function deleteIngredient($id);
}
