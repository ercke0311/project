<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\OAuth\OAuthLoginFactory;
use Illuminate\Http\Request;

class SocialLoginController extends Controller
{
    private OAuthLoginFactory $loginFactory;

    public function __construct(OAuthLoginFactory $loginFactory)
    {
        $this->loginFactory = $loginFactory;
    }

    public function getRedirectUrl(string $driver)
    {
        return $this->loginFactory
            ->create($driver)
            ->getRedirectUrl();
    }

    public function callback(string $driver, Request $request)
    {
        $payload = [
            'email' => $request->email,
            'password' => $request->password,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];

        return $this->loginFactory
            ->create($driver)
            ->handleCallback($payload);
    }
}