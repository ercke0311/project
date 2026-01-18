<?php

namespace App\Services\Auth\OAuth;

interface OAuthLoginInterface
{
    public function driver(): string;
    public function getRedirectUrl(): array;
    public function handleCallback(array $context): array;
}