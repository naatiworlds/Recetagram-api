<?php

namespace App\Repositories;

use App\Models\Ingredient;
use App\Interfaces\IngredientRepositoryInterface;

class IngredientRepository implements IngredientRepositoryInterface
{
    public function getAllIngredients()
    {
        return Ingredient::all();
    }

    public function getIngredientById($id)
    {
        return Ingredient::findOrFail($id);
    }

    public function createIngredient(array $data)
    {
        return Ingredient::create($data);
    }

    public function updateIngredient($id, array $data)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->update($data);
        return $ingredient;
    }

    public function deleteIngredient($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();
        return true;
    }
}
