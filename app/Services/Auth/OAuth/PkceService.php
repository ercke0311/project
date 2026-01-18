<?php

namespace App\Services\Auth\OAuth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * line pkce example: https://developers.line.biz/en/docs/line-login/integrate-pkce
 */
class PkceService
{
    public function generatePkce(string $provider): array
    {
        $state = Str::random(32);
        $codeVerifier = $this->generateCodeVerifier();

        Cache::put("pkce:{$provider}:state:{$state}", $codeVerifier, now()->addMinutes(10));

        return [
            'state' => $state,
            'code_verifier' => $codeVerifier,
            'code_challenge' => $this->generateCodeChallenge($codeVerifier),
        ];
    }

    public function authorize(string $provider, array $context): string
    {
        $code  = $context['code']  ?? null;
        $state = $context['state'] ?? null;

        if (empty($code) || empty($state)) {
            throw new UnauthorizedHttpException('OAuth', 'Missing code or state');
        }
        
        if (($context['cookie_state'] ?? null) !== $state) {
            throw new UnauthorizedHttpException('OAuth', 'State mismatch');
        }

        $codeVerifier = $this->getCodeVerifyWithState($provider, $state);

        if (empty($codeVerifier)) {
            throw new UnauthorizedHttpException('OAuth', 'Invalid state');
        }

        return $codeVerifier;
    }

    public function setStateWithCookie(string $provider, string $state): Cookie
    {
        return cookie(
            name: 'oauth_state',
            value: $state,
            minutes: 10,
            path: "/api/auth/social/$provider/callback",
            domain: null,
            secure: true,
            httpOnly: true,
            raw: false,
            sameSite: 'Lax',
        );
    }

    private function getCodeVerifyWithState(string $provider, string $state): ?string
    {
        return Cache::pull("pkce:{$provider}:state:{$state}");
    }

    private function generateCodeVerifier(): string
    {
        $length = random_int(43, 128);
        return Str::random($length);
    }

    private function generateCodeChallenge(string $verifier): string
    {
        return sodium_bin2base64(
            hash('sha256', $verifier, true),
            SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING
        );
    }
}