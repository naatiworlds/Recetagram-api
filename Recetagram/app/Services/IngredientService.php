<?php

namespace App\Services;

use App\Interfaces\IngredientRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IngredientService
{
    protected $ingredientRepository;

    public function __construct(IngredientRepositoryInterface $ingredientRepository)
    {
        $this->ingredientRepository = $ingredientRepository;
    }

    public function getAllIngredients()
    {
        return $this->ingredientRepository->getAllIngredients();
    }

    public function getIngredientById($id)
    {
        try {
            return $this->ingredientRepository->getIngredientById($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Ingredient not found');
        }
    }

    public function createIngredient(array $data)
    {
        return $this->ingredientRepository->createIngredient($data);
    }

    public function updateIngredient($id, array $data)
    {
        try {
            return $this->ingredientRepository->updateIngredient($id, $data);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Ingredient not found');
        }
    }

    public function deleteIngredient($id)
    {
        try {
            return $this->ingredientRepository->deleteIngredient($id);
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Ingredient not found');
        }
    }
}
