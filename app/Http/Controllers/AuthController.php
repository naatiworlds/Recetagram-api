<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->attemptLogin($request->validated());

            if ($result) {
                return ResponseHelper::success($result, 'Login successful');
            }

            return ResponseHelper::error('Invalid credentials', 401);
        } catch (\Exception $e) {
            return ResponseHelper::error('Login failed' . $e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $result = $this->authService->logout($request->user());
            return ResponseHelper::success($result, 'Logout successful');
        } catch (\Exception $e) {
            return ResponseHelper::error('Logout failed', 500);
        }
    }

    public function users()
    {
        try {
            $users = $this->authService->getAllUsers();
            return ResponseHelper::success($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to retrieve users', 500);
        }
    }

    public function index()
    {
        try {
            $users = $this->authService->getAllUsers();
            return ResponseHelper::success($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to retrieve users', 500);
        }
    }

    public function show(User $user)
    {
        try {
            return response()->json([
                'status'  => 'success',
                'message' => 'Usuario recuperado exitosamente',
                'data'    => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'role'       => $user->role,
                    'is_public'  => $user->is_public,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al recuperar el usuario'
            ], 500)->header('Content-Type', 'application/json');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = $this->authService->createUser($validated);
            return ResponseHelper::success($user, 'User created successfully', 201);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return ResponseHelper::error('Failed to create user: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name'      => 'string|max:255|nullable',
                'email'     => 'email|unique:users,email,' . $user->id . '|nullable',
                'password'  => 'string|min:6|nullable',
                'is_public' => 'boolean|nullable'
            ]);

            // Convertir a booleano en caso de recibir "false" o "true" como string
            if (isset($validated['is_public'])) {
                $validated['is_public'] = filter_var($validated['is_public'], FILTER_VALIDATE_BOOLEAN);
            }

            $updatedUser = $this->authService->updateUser($user->id, $validated);
            return ResponseHelper::success($updatedUser, 'Usuario actualizado exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return ResponseHelper::error('Error al actualizar el usuario: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $this->authService->deleteUser($user->id);
            return ResponseHelper::success(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error('Failed to delete user', 500);
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();
            return response()->json([
                'status'  => 'success',
                'message' => 'Usuario recuperado exitosamente',
                'data'    => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'role'       => $user->role,
                    'is_public'  => $user->is_public,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
            ], 200)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al recuperar el usuario'
            ], 500)->header('Content-Type', 'application/json');
        }
    }
}
