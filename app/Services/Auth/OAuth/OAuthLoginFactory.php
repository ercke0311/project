<?php

namespace App\Services\Auth\OAuth;

class OAuthLoginFactory
{
    public function __construct(
        private GithubLoginService $githubLogin,
        private GoogleLoginService $googleLogin,
    ) {}

    public function create(string $driver): OAuthLoginInterface
    {
        return match ($driver) {
            'github' => $this->githubLogin,
            'google' => $this->googleLogin,
            default => throw new \RuntimeException('unknown third party login'),
        };
    }
}