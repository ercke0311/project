<?php

namespace App\Services\Auth\OAuth;

interface OAuthLoginInterface
{
    public function driver(): string;
    public function getRedirectUrl();
    public function handleCallback($payload);
}