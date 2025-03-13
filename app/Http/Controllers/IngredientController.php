<?php

namespace App\Http\Controllers;

use App\Services\IngredientService;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class IngredientController extends Controller
{
    protected $ingredientService;

    public function __construct(IngredientService $ingredientService)
    {
        $this->ingredientService = $ingredientService;
    }

    public function index()
    {
        try {
            $ingredients = $this->ingredientService->getAllIngredients();
            return ResponseHelper::success($ingredients, 'Ingredients retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to retrieve ingredients', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:ingredients'
            ]);

            $ingredient = $this->ingredientService->createIngredient($validated);
            return ResponseHelper::success($ingredient, 'Ingredient created successfully', 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to create ingredient', 500);
        }
    }

    public function show($id)
    {
        try {
            $ingredient = $this->ingredientService->getIngredientById($id);
            return ResponseHelper::success($ingredient, 'Ingredient retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Ingredient not found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:ingredients,name,' . $id
            ]);

            $ingredient = $this->ingredientService->updateIngredient($id, $validated);
            return ResponseHelper::success($ingredient, 'Ingredient updated successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to update ingredient', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->ingredientService->deleteIngredient($id);
            return ResponseHelper::success(null, 'Ingredient deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to delete ingredient', 500);
        }
    }
}
