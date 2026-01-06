<?php

namespace App\Services\Auth\OAuth;

use App\Models\SocialAccount;
use App\Models\User;
use App\Services\Auth\AuthService;
use Socialite;

abstract class AbstractSocialiteLogin implements OAuthLoginInterface
{
    protected $provider;

    public function __construct(
        private AuthService $authService
    ) {}

    public function driver(): string
    {
        return $this->provider;
    }

    protected function socialite()
    {
        return Socialite::driver($this->driver())->stateless();
    }
    
    protected function fetchUser()
    {
        return $this->socialite()->user();
    }

    public function getRedirectUrl()
    {
        return $this->socialite()
            ->redirect()
            ->getTargetUrl();
    }

    public function handleCallback($payload)
    {
        $socialUser = $this->fetchUser();

        $providerId = $socialUser->getId();
        $email = $socialUser->getEmail();
        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? '';

        $social = SocialAccount::where('provider', $this->provider)
            ->where('provider_id', $providerId)
            ->first();

        if ($social) {
            $user = $social->user;
        } else {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'  => $name,
                    'password' => null,
                ]
            );

            SocialAccount::create([
                'user_id'     => $user->id,
                'provider'    => $this->provider,
                'provider_id' => $providerId,
                'name'        => $name,
                'email'       => $email,
            ]);
        }

        $meta = [
            'user_agent' => $payload['user_agent'],
            'ip' => $payload['ip'],
        ];

        $refreshCookie = $this->authService->createRefreshToken($user->id, $meta);
        
        return redirect()->away(config('app.frontend_url') . "/")
            ->withCookie($refreshCookie);
    }
}