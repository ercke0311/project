<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\RefreshToken;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$accessToken = auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'email或密碼錯誤'
            ], 401);
        }

        $user = auth()->user();

        $meta = [
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'ip' => $request->ip(),
        ];

        $refreshCookie = $this->authService->createRefreshToken($user->id, $meta);

        return response()->json([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
            'user'         => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ])->cookie($refreshCookie);
    }

    public function logout(Request $request)
    {
        $refreshPlain = (string) $request->cookie('refresh_token');
        if ($refreshPlain !== '') {
            $hash = hash('sha256', $refreshPlain);

            RefreshToken::where('token_hash', $hash)
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);
        }

        return response()->json(['message' => 'logout success'])
            ->withoutCookie('refresh_token');
    }
    
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'user' => $user,
        ], 201);
    }

    public function refresh(Request $request)
    {
        $refreshPlain = (string) $request->cookie('refresh_token');
        
        if ($refreshPlain === '') {
            return response()->json(['message' => 'no refresh token'], 401);
        }

        $meta = [
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'ip' => $request->ip(),
        ];

        $result = $this->authService->rotateRefreshToken($refreshPlain, $meta);

        if (! $result) {
            return response()->json(['message' => 'refresh token invalid'], 401)
                ->withoutCookie('refresh_token');
        }

        return response()->json([
            'access_token' => $result['access_token'],
            'token_type' => 'Bearer',
            'expires_in' => $result['expires_in'],
        ])->cookie($result['refresh_cookie']);
    }

    public function me()
    {
        $user = auth()->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return response()->json($user);
    }
}