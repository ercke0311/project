<?php

namespace App\Services\Auth\OAuth;

class OAuthLoginFactory
{
    public function __construct(
        private GithubLoginService $githubLogin,
        private GoogleLoginService $googleLogin,
        private LineLoginService $lineLogin,
    ) {}

    public function create(string $driver): OAuthLoginInterface
    {
        return match ($driver) {
            'github' => $this->githubLogin,
            'google' => $this->googleLogin,
            'line' => $this->lineLogin,
            default => throw new \RuntimeException('unknown third party login'),
        };
    }
}