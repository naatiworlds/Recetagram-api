<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function attemptLogin(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return [
                'status' => 'success',
                'token' => $token,
                'user' => $user
            ];
        }

        return false;
    }

    

    public function getAllUsers()
    {
        return User::all();
    }

    public function getUser($id)
    {
        return User::findOrFail($id);
    }

    public function createUser(array $data)
    {
        try {
            $data['password'] = Hash::make($data['password']);
            $data['role'] = 'user';
            
            Log::info('Attempting to create user with data:', ['email' => $data['email']]);
            
            $user = User::create($data);
            
            Log::info('User created successfully', ['user_id' => $user->id]);
            
            return $user;
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateUser($id, array $data)
    {
        $user = User::findOrFail($id);
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if (isset($data['is_private'])) {
            $data['is_private'] = (bool) $data['is_private'];
        }

        $user->update($data);
        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }

    public function login(array $credentials)
    {
        try {
            Log::info('Attempting login for user:', ['email' => $credentials['email']]);
            
            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                throw new \Exception('Credenciales inválidas');
            }

            // Revocar tokens antiguos
            $user->tokens()->delete();

            // Crear nuevo token
            $token = $user->createToken('auth_token');

            Log::info('Login successful for user:', ['user_id' => $user->id]);

            return [
                'user' => $user,
                'token' => $token->plainTextToken
            ];
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function logout($user)
    {
        try {
            if ($user && $user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            throw $e;
        }
    }
}
