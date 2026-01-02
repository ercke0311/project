<?php

namespace App\Services\Auth;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie;

class AuthService
{
    public function rotateRefreshToken(string $refreshPlain, array $meta): ?array
    {
        $hash = hash('sha256', $refreshPlain);

        return DB::transaction(function () use ($hash, $meta) {
            $record = RefreshToken::where('token_hash', $hash)
                ->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $record) {
                return null;
            }

            $user = User::find($record->user_id);
            if (! $user) {
                return null;
            }

            $record->update(['revoked_at' => now()]);

            $accessToken = auth()->login($user);
            $expiresIn   = auth()->factory()->getTTL() * 60;

            $refreshCookie = $this->createRefreshToken($user->id, $meta);

            return [
                'access_token'   => $accessToken,
                'expires_in'     => $expiresIn,
                'refresh_cookie' => $refreshCookie,
                'user'           => $user,
            ];
        });
    }

    public function createRefreshToken($userId, $meta): Cookie
    {
        $refreshPlain = Str::random(64);
        $refreshHash  = hash('sha256', $refreshPlain);

        RefreshToken::create([
            'user_id'     => $userId,
            'token_hash'  => $refreshHash,
            'expires_at'  => now()->addDays(14),
            'user_agent'  => $meta['user_agent'],
            'ip'          => $meta['ip'],
        ]);

        return $this->refreshCookie($refreshPlain);
    }

    private function refreshCookie(string $refreshPlain): Cookie
    {
        return cookie(
            name: 'refresh_token',
            value: $refreshPlain,
            minutes: 60 * 24 * 14,
            path: '/api/auth',
            domain: null,
            secure: true,
            httpOnly: true,
            raw: false,
            sameSite: 'Lax',
        );
    }
}