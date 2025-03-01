<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;

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

    public function logout($user)
    {
        $this->authRepository->deleteCurrentToken($user);
        return [
            'status' => 'success',
            'message' => 'Logged out successfully'
        ];
    }

    public function getAllUsers()
    {
        return $this->authRepository->getAllUsers();
    }
}
