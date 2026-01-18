<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\OAuth\OAuthLoginFactory;
use App\Services\Auth\Social\SocialAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SocialLoginController extends Controller
{

    public function __construct(
        private OAuthLoginFactory $loginFactory,
        private SocialAccountService $socialAccountService,
    ){}

    public function getRedirectUrl(string $provider): JsonResponse
    {
        $result = $this->loginFactory
            ->create($provider)
            ->getRedirectUrl();

        $response = response()->json([
            'redirect_url' => $result['redirect_url'],
        ]);

        return $result['cookie']
            ? $response->withCookie($result['cookie'])
            : $response;
    }

    public function callback(string $provider, Request $request): RedirectResponse
    {
        $context = [
            ...$request->all(),
            'cookie_state' => $request->cookie('oauth_state')
        ];

        $profile = $this->loginFactory
            ->create($provider)
            ->handleCallback($context);

        $meta = [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        $refreshTokenCookie = $this->socialAccountService
            ->handleSocialLogin($provider, $profile, $meta);

        return redirect()->away(config('app.frontend_url') . "/")
            ->withCookie($refreshTokenCookie);
    }
}