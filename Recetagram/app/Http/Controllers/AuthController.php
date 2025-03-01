<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $result = $this->authService->attemptLogin($credentials);

        if ($result) {
            return response()->json($result);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 401);
    }

    public function logout(Request $request)
    {
        $result = $this->authService->logout($request->user());
        return response()->json($result);
    }

    public function users()
    {
        $users = $this->authService->getAllUsers();
        return response()->json($users);
    }
}
