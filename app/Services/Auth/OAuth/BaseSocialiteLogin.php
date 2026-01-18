<?php

namespace App\Services\Auth\OAuth;

use Socialite;

abstract class BaseSocialiteLogin implements OAuthLoginInterface
{
    protected $provider;

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

    /**
     * @return array{redirect_url: string, cookie: ?\Symfony\Component\HttpFoundation\Cookie}
     */
    public function getRedirectUrl(): array
    {
        $url = $this->socialite()
            ->redirect()
            ->getTargetUrl();

        return [
            'redirect_url' => $url,
            'cookie' => null,
        ];
    }

    public function handleCallback(array $context): array
    {
        $socialUser = $this->fetchUser();

        $profile = [
            'provider_id' => $socialUser->getId(),
            'email' => $socialUser->getEmail(),
            'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? '',
        ];

        return $profile;
    }
}